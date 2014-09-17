<?php
namespace Webity\Web\Application;

use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Web;
use Joomla\Session\Session;
use Joomla\Registry\Registry;
use Joomla\Database;
use Webity\Web\Input\Router;
use Webity\Web\Oauth2\Server as OauthServer;
use Webity\Web\Document\Document;
use Webity\Web\Layout\File as Layout;
use PHPMailer;

class WebApp extends AbstractWebApplication
{

	// Override a few core variables to make this work for REST purposes

	// TODO: support multiple response types: html, xml, json
	//public $mimeType = 'application/json';
	protected static $instances = array();
	protected $db = null;
	protected $user = null;
	protected $debug = false;
	protected $debug_values = array();
	protected $document = null;
	protected $template_engine = null;
	protected $template_layout = 'template';
	protected $controller_paths = array();
	protected $session = null;
	protected static $mailer = null;

	public function __construct(Input $input = null, Registry $config = null, Web\WebClient $client = null)
	{
		if (!defined("JPATH_ROOT")) {
			throw new \RuntimeException('"JPATH_ROOT" is a required constant. Please define before invoking the application.', 500);
		}

		if (is_null($config)) {
			$config = $this->loadConfiguration();
		}

		if ($config->get('debug')) {
			$this->debug = true;
			$this->markDebug('Start');
		}

		parent::__construct($input, $config, $client);

		$this->document = new Document();
		$this->document->setbase($this->config->get('uri.base.path'));
	}

	private function loadSession() {
		if (session_status() == PHP_SESSION_NONE) {
		    session_start();
		}

		return $_SESSION;
	}

	public function getSession() {
		if (is_null($this->session)) {
			$this->session = $this->loadSession();
		}
		return $this->session;
	}

	protected function markDebug($msg) {
		if (!$this->debug) {
			return;
		}

		$array = array();
		$array['message'] = $msg;
		$array['time'] = microtime(true);

		if ($start = $this->debug_values[0]['time']) {
			$array['elapsed'] = $array['time'] - $start;
		}

		$this->debug_values[] = $array;

		return true;
	}

	protected function loadConfiguration() {
		$registry = Registry::getInstance(1);

		if (file_exists(JPATH_ROOT . '/config.inc.php')) {
			$registry->loadFile(JPATH_ROOT . '/config.inc.php');
		}

		return $registry;
	}

	protected function initialiseDbo()
    {
        // Make the database driver.
        $dbFactory = new Database\DatabaseFactory;

        $this->db = $dbFactory->getDriver(
            $this->get('database.driver'),
            array(
                'host' => $this->get('database.host'),
                'user' => $this->get('database.user'),
                'password' => $this->get('database.password'),
                'database' => $this->get('database.name'),
                'prefix' => $this->get('database.prefix', 'jos_'),
            )
        );
	}

	public static function getInstance($id = 1)
	{
		if (empty(self::$instances[$id]))
		{
			// use static instead of self to return an instance of the class that was called
			self::$instances[$id] = new static;
		}

		return self::$instances[$id];
	}

	public function getDbo() {
		if (is_null($this->db)) {
			$this->initialiseDbo();
		}

		return $this->db;
	}

	public function getDocument() {
		return $this->document;
	}

	public function addTemplateFolder($folder) {
		$this->template_folder[] = $folder;
	}

	// TODO: necessary for JSON response!
	// public function getBody()
	// {
	// 	return json_encode($this->response->body);
	// }

	public function authenticate() {
		$this->markDebug('Start Authentication');

		$session = $this->getSession();

		if (!$this->getUser()->username) {
			try {
				$controller = new \Webity\Web\Components\Login\Controller($this->input, $this);
				$response = $controller->execute();
				if ($response === FALSE) {
					throw new \Exception('Unknown error', 404);
				} elseif ($response === TRUE) {
					// user has been authenticated, reset to a get request to same url
					$this->redirect($this->config->get('uri.request'));
				} elseif (is_string($response)) {
					$this->setBody($response);
					$this->respond();
					$this->close();
				}
			} catch (Exception $e) {
				throw new \Exception('Unable to Authenticate User: ' . $e->getMessage(), 401);
			}
		}

		return $this;
	}

	public function login($username, $password) {
		// TODO: write base login function to load data from user table if exists
	}

	public function logout() {
		// destroy the session and redirect to homepage
		$session = $this->getSession();
		foreach ($_SESSION as $key => $val) {
			unset($_SESSION[$key]);
		}
		$this->redirect($this->config->get('uri.base.path'));
	}

	protected function setUser(\stdClass $user) {
		$this->user = $user;
	}

	public function getUser()
	{
		if ($this->user) {
			return $this->user;
		} else {
			$session = $this->getSession();
			return $_SESSION['user'];
		}
	}

	public function getMailer()
	{
		if (!self::$mailer)
		{
			self::$mailer = self::createMailer();
		}

		$copy = clone self::$mailer;

		return $copy;
	}

	protected function createMailer()
	{

		$smtpauth = ($this->get('smtpauth') == 0) ? null : 1;
		$smtpuser = $this->get('smtpuser');
		$smtppass = $this->get('smtppass');
		$smtphost = $this->get('smtphost');
		$smtpsecure = $this->get('smtpsecure');
		$smtpport = $this->get('smtpport');
		$mailfrom = $this->get('mailfrom');
		$fromname = $this->get('fromname');
		$mailer = $this->get('mailer');

		// Create a JMail object
		$mail = new PHPMailer;

		// Set default sender without Reply-to
		$mail->SetFrom($mailfrom, $fromname, 0);

		// Default mailer is to use PHP's mail function
		switch ($mailer)
		{
			case 'smtp':
				$mail->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;

			case 'sendmail':
				$mail->IsSendmail();
				break;

			default:
				$mail->IsMail();
				break;
		}

		return $mail;
	}

	public function route()
	{
		$this->markDebug('Start Routing');

		if ($this->config->get('uri.route') == 'logout') {
			$this->logout();
		}

		$router = new Router($this->input);
		$router->setDefaultController('\\Webity\\Web\\Controller\\Controller');

		$this->controller = $router->getController($this->config->get('uri.route'));

		$this->markDebug('Complete Routing');
		return $this;
	}

	public function execute()
	{
		$this->markDebug('Start Execution');
		try {
			parent::execute();
		} catch (\Exception $e) {
			$this->raiseError($e->getMessage(), $e->getCode());
		}
	}

    public function doExecute()
    {
    	$response = $this->controller->execute();
    	$this->appendBody($response);
    }

    protected function respond()
	{
		// print debug to response if set up
		if ($this->debug) {
			$this->markDebug('Respond');
			$obj = new \stdClass;
			$obj->debug = $this->debug_values;

			$layout = new Layout('Debug');
			$this->appendBody($layout->render($obj));
		}

		$this->loadTemplate();

		parent::respond();
	}

	public function getTemplateEngine() {
		if (!$this->template_engine) {
			$this->setupTemplateEngine();
		}

		return $this->template_engine;
	}

	public function getTemplateLayout() {
		return $this->template_layout;
	}

	public function setTemplateLayout($string) {
		$this->template_layout = $string;

		return $this;
	}

	protected function setupTemplateEngine() {
		// Create new Plates engine
		$this->template_engine = new \League\Plates\Engine(__DIR__ . '/../Templates/Webity');

		return $this;
	}

	// TODO: Build this out as a real templating engine
	protected function loadTemplate() {

		$template = new \League\Plates\Template($this->getTemplateEngine());
		$template->application = $this;
		$template->content = $this->getBody();
		$template->document = $this->getDocument();
		$template->layout($this->getTemplateLayout());
		$template->path = $this->config->get('uri.base.path');
		$template->media = $this->config->get('uri.media.path');
		$template->user = $this->getUser();

		$template = $this->alterTemplate($template);

		// Render the template
		$this->setBody($template->render('render'));

		return true;
	}

	protected function alterTemplate($template) {
		return $template;
	}

    public function raiseError($message = '', $code = 404)
    {
    	http_response_code($code);

    	// TODO: $method isn't set yet.
   		if ($method == 'json') {
	    	$data = new \stdClass;
	    	$data->error = true;
	    	$data->message = $message;

	    	echo json_encode($data);
	    } else {
	    	$displayData = array(
	    		'message' => $message
	    	);
	    	$layout = new Layout('Error');
			$this->setBody($layout->render($displayData));
			$this->respond();
	    }

		$this->close();
    }

    public function run()
    {
    	try {
	    	$this->route();
	    	$this->execute();
	    } catch (\Exception $e) {
	    	$this->raiseError($e->getMessage(), $e->getCode());
	    }
    }

    //a way to get granular control over whether or not a user can or can't do a particular task
    public function userCan($task = '') {
        if(!array_key_exists($task, $this->rules)) {
            return false;
        }

        $user_group = str_replace(' ', '', strtolower($this->getUser()->group_title));
        $permitted_groups = explode('|', $this->rules[$task]);

        foreach($permitted_groups as $permitted_group) {
        	if($user_group == $permitted_group) {
        		return true;
        	}
        }

        return false;
    }

    //used so if there is a rule that may be applicable to an entire component we can use it there
    public function getRules($task = '') {
    	if(!$task) {
    		return $this->rules;
    	} else {
    		return $this->rules[$task];
    	}
    }

    //very basic way of handling passing messages along (type is based on classes defined in bootstrap 3)
    public function enqueueMessage($message, $type = 'success') {
    	// For empty queue, if messages exists in the session, enqueue them first.

    	$_SESSION['application.queue'] = array('message' => $message,
    										   'type' => strtolower($type));
        // if (!count($this->messageQueue))
        // {
        //     $sessionQueue = $_SESSION['application.queue'];

        //     if (count($sessionQueue))
        //     {
        //         $this->messageQueue = $sessionQueue;
        //         unset($_SESSION['application.queue']);
        //     }
        // }

        // // Enqueue the message.
        // $this->messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
    }

	//a very basic way of getting the message
    public function getMessageQueue() {
		// For empty queue, if messages exists in the session, enqueue them.
        $messageQueue = $_SESSION['application.queue'];
        unset($_SESSION['application.queue']);

        return $messageQueue;
    }
}

// a function that I stole from php.net, to provide support back to php 5.3.10 (like Joomla)
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                	$text = 'Unknown http status code'; break;
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

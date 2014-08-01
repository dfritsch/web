<?php
namespace Webity\Web\Controller;

use Joomla\Controller\AbstractController;
use Webity\Web\Application\WebApp;
use Joomla\Input\Input;
use Webity\Web\Layout\File as Layout;

class Controller extends AbstractController
{
    protected $directory = '';
    protected $namespace = '';
    protected $authorizedGroups = array();

    public function __construct($input = null, $app = null) {
        $rc = new \ReflectionClass(get_class($this));
        $this->directory = dirname($rc->getFileName());
        $this->namespace = $rc->getNamespaceName();

        if (!$app) {
            $app = WebApp::getInstance();
        }

        return parent::__construct($input, $app);
    }

    // set as final to force output buffering
    final public function execute()
    {
        $input = $this->getInput();
        $task = $input->get('task', false);
        $method = $_SERVER['REQUEST_METHOD'];

        ob_start();
        if ($task && method_exists($this, $task)) {
            $reflection = new \ReflectionMethod($this, $task);
            if (!$reflection->isPublic()) {
              throw new \RuntimeException("The called method is not public.");
            }
            $response = $this->$task();
        } else if($method == 'POST') {
            $response = $this->doPost();
        } else {
            $response = $this->doExecute();
        }
        $data = ob_get_clean();

        if ($data) {
            return $data;
        } else {
            return $response;
        }
    }

    protected function doExecute()
    {
        $view = $this->getView();
        echo $view;
    }

    protected function doPost()
    {
        $app = $this->getApplication();

        $saved = $this->getModel()->save();
        $format = $app->input->get('format', 'html');

        if ($format == 'json') {
            $return = array(json_encode($this->getModel()->data));
    		if ($saved) {
    			// require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/ajax.php');
    			// $helper_name = $this->com_name . 'HelperAjax';
    			// $helper = new $helper_name();
    			// $form = $this->model->getForm(array(), true, 'jform', $id);

    			$return['id'] = $saved;
                if (is_scalar($saved)) {
                    $form = $this->getModel()->getForm($saved);
                    $layout = new Layout('SubtableHtml');
                    $return['data'] = $layout->render(
                        array(
                            'link_name' => strtolower(basename($this->directory)),
                            'key' => $saved,
                            'link' => $form,
                            'check_trashed' => false
                        )
                    );
                    if ($field = $form->getField('state', strtolower(basename($this->directory)))) {
                        $return['state'] = $field->__get('value');
                    }
                }
    			//$return['token'] = JSession::getFormToken();
    		} else {
    			$return['error'] = "error";
    			//$return['token'] = JSession::getFormToken();
    		}
    		echo json_encode($return);
    		exit();
        } else {
            if($saved) {
                $app->redirect($app->get('uri.base.full') . strtolower(basename($this->directory)));
            } else {
                //go back to the same view with the data intact
                $_SESSION['form'][strtolower(basename($this->directory))] = $app->input->post->get('jform', array(), 'ARRAY');
                $app->redirect('form');
            }
        }

    }

    protected function getModel() {
        if (!file_exists($this->directory . '/Model.php')) {
            return false;
        }

        $class = $this->namespace . '\\Model';

        return new $class($this->getApplication()->getDbo());
    }

    protected function getView() {
        $model = $this->getModel();

        if (!$model || !file_exists($this->directory . '/View.php')) {
            return false;
        }

        $class = $this->namespace . '\\View';

        return new $class($model);
    }

    protected function dispatch($controller, $vars = array()) {
        if ($vars) {
            $input = new Input($vars);
        } else {
            $input = WebApp::getInstance()->input;
        }
        $class = new $controller($input);
        return $class->execute();
    }

    //allows us to return the authorized groups for the particular component we are requesting (might get sticky and complicated cause this is an hmvc...)
    protected function getAuthorizedGroups() {
        return $this->authorizedGroups;
    }

    //we need to verify what kind of user it is. whether or not it has access.
    protected function accessCheck()
    {
        $app = WebApp::getInstance();

        $user = $app->getUser();

        //so that it makes it easy to write the authorizedGroups array
        $userGroup = str_replace(' ', '', strtolower($user->group_title));

        if(!in_array($userGroup, $this->getAuthorizedGroups())) {
            $app->redirect($app->get('uri.base.full'));
        }
    }
}

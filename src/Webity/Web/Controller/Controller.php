<?php
namespace Webity\Web\Controller;

use Joomla\Controller\AbstractController;
use Webity\Web\Application\WebApp;
use Joomla\Input\Input;

class Controller extends AbstractController
{
    protected $directory = '';
    protected $namespace = '';

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

        if($this->getModel()->save()) {
            $app->redirect($app->get('uri.base.full') . strtolower(basename($this->directory)));
        } else {
            //go back to the same view with the data intact
            $_SESSION['form'] = $app->input->post->get('jform', array(), 'ARRAY');
            $app->redirect('form');
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
}

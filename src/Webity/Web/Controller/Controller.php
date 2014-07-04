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
        ob_start();
        $response = $this->doExecute();
        $data = ob_get_clean();

        if ($data) {
            return $data;
        } else {
            return $response;
        }
    }

    protected function doExecute()
    {
        $displayData = array(
            'message' => 'The default controller is only set to print this message.'
        );
        $layout = new \Webity\Web\Layout\File('Error');
        echo $layout->render($displayData);
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
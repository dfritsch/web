<?php
namespace Webity\Web\View;

use Joomla\View\AbstractHtmlView;
use Webity\Web\Application\WebApp;
use Webity\Web\Layout\File as Layout;

class View extends AbstractHtmlView
{

	protected $layout = '';
	protected $accessRules = array();

	public function __construct($model, $paths = null)
	{
		parent::__construct($model, $paths);
	}

	protected function loadPaths()
	{
		// get current directory for layouts
		$rc = new \ReflectionClass(get_class($this));
        $directory = dirname($rc->getFileName()) . '/tmpl';

		$queue = new \SplPriorityQueue;
		$queue->insert($directory, $directory);

		return $queue;
	}

	protected function getDocument() {
		$app = WebApp::getInstance();
		return $app->getDocument();
	}

	public function render() {

		$app = WebApp::getInstance();
		$input = $app->input;

		if(!$this->layout) {
			//set the correct layout
			if($input->get('id')) {
				$this->layout = $input->get('layout', 'item');
			} else {
				$this->layout = $input->get('layout', 'default');
			}
		}

		if (!$this->getPath($this->getLayout())) {
			$this->layout = 'default';
		}

		//now that the layout has been determined let's make sure the user has access to it
		if (!$this->accessCheck()) {
			$this->layout = 'error';
			$app->enqueueMessage('Permission denied', 'danger');
		}

		//probably will need to check if the method exists...
		$getMethod .= ($this->layout != 'default') ? "get" . ucwords($this->layout) : "getItems";

		if (!$this->data) {
			if(method_exists($this->model, $getMethod)) {
				$this->data = $this->model->$getMethod($input->get('id'));
			} else {
				//throw an error here? maybe just do nothing instead?
				$this->data = array();
			}
		}

		return parent::render();
	}

	protected function renderLayout($name, $data = array()) {
		$layout = new Layout($name);
		return $layout->render($data);
	}

	public function accessCheck() {
		$permitted = true;

		if(in_array($this->layout, $this->accessRules)) {
			$app = WebApp::getInstance();
			$user = $app->getUser();

			$permitted = (!is_null($user) && (int) $user->admin);
		}

		return $permitted;
	}
}

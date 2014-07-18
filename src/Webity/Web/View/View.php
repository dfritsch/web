<?php
namespace Webity\Web\View;

use Joomla\View\AbstractHtmlView;
use Webity\Web\Application\WebApp;
use Webity\Web\Layout\File as Layout;

class View extends AbstractHtmlView
{

	protected $layout = '';

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

		$input = WebApp::getInstance()->input;

		if(!$this->layout) {
			//set the correct layout
			if($input->get('id')) {
				$this->layout = $input->get('layout', 'item');
			} else {
				$this->layout = $input->get('layout', 'default');
			}
		}

		//probably will need to check if the method exists...
		$getMethod .= ($this->layout != 'default') ? "get" . ucwords($this->layout) : "getItems";

		if(method_exists($this->model, $getMethod)) {
			$this->data = $this->model->$getMethod($input->get('id'));
		} else {
			//throw an error here? maybe just do nothing instead?
			exit($getMethod . " method didn't exist");
		}

		return parent::render();
	}

	protected function renderLayout($name, $data = array()) {
		$layout = new Layout($name);
		return $layout->render($data);
	}
}
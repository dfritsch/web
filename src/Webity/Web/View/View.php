<?php
namespace Webity\Web\View;

use Joomla\View\AbstractHtmlView;
use Webity\Web\Application\WebApp;
use Webity\Web\Layout\File as Layout;

class View extends AbstractHtmlView
{

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

	protected function renderLayout($name, $data = array()) {
		$layout = new Layout($name);
		return $layout->render($data);
	}
}
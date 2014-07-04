<?php
namespace Webity\Web\Components\Login;

use Webity\Web\View\View as WebityView;

class View extends WebityView
{
	public function render() {
		$doc = $this->getDocument();
		$doc->setTitle('Login');

		$this->form = $this->model->getForm();
		return parent::render();
	}
}
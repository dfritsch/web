<?php
namespace Webity\Web\Components\Login;

use Webity\Web\Model\Model as WebityModel;
use Webity\Web\Application\WebApp;

class Model extends WebityModel
{
	public function getForm($name = 'login', $data = array()) {
		$app = WebApp::getInstance();
		$session = $app->getSession();
		if (!$data && $_SESSION['form']['data']) {
			$data = $_SESSION['form']['data'];
		}
		return parent::getForm($name, $data);
	}

	public function login($username, $password) {
		$app = WebApp::getInstance();
		$session = $app->getSession();
		$_SESSION['form']['data']['username'] = $username;
		return $app->login($username, $password);
	}
}
<?php
namespace Webity\Web\Components\Login;

use Webity\Web\Model\Model as WebityModel;
use Webity\Web\Application\WebApp;

class Model extends WebityModel
{
	public function getItems() {
		return array();
	}

	public function login($username, $password) {
		$app = WebApp::getInstance();
		$session = $app->getSession();
		$_SESSION['form']['data']['username'] = $username;
		return $app->login($username, $password);
	}
}

<?php
namespace Webity\Web\Components\Login;

use Webity\Web\Controller\Controller as WebityController;

class Controller extends WebityController
{

    public function doExecute()
    {
    	$username = $this->getInput()->post->get('username', '', 'STRING');
    	$password = $this->getInput()->post->get('password', '', 'STRING');

    	if ($username && $password) {
    		$model = $this->getModel();

    		if ($model->login($username, $password)) {
    			return true;
    		}
    	}

    	$view = $this->getView();
        echo $view;
    }
}
<?php
namespace Webity\Web\Components\Login;

use Webity\Web\Controller\Controller as WebityController;

class Controller extends WebityController
{

    public function doExecute()
    {
    	$view = $this->getView();
        echo $view;
    }

    public function doPost() {
        $jform = $this->getInput()->post->get('jform', '', 'ARRAY');

        $username = $jform['username'];
        $password = $jform['password'];

        if ($username && $password) {
            $model = $this->getModel();

            if ($model->login($username, $password)) {
                return true;
            }
        }

        $this->doExecute();
    }
}

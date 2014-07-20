<?php
namespace Webity\Web\Model;

use Joomla\Model\AbstractDatabaseModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\Form\Form;
use Webity\Web\Application\WebApp;

class Model extends AbstractDatabaseModel
{
    protected $directory = '';
    protected $namespace = '';

    public function __construct(DatabaseDriver $db, Registry $state = null) {
        $rc = new \ReflectionClass(get_class($this));
        $this->directory = dirname($rc->getFileName());
        $this->namespace = $rc->getNamespaceName();

        return parent::__construct($db, $state);
    }


    public function getItems($id = null) {
        $app = WebApp::getInstance();
        $api = $app->getApi();

        $return = array();

        $object_name = strtolower(basename($this->directory));
        $url = $object_name . '/' . $id;

        try {
            $return = $api->query($url)->data;
        } catch(\InvalidArgumentException $e) {
        } catch(\RuntimeException $e) {
        }

        return $return;
    }

    //sort of like an alias for getItems (because of the naming conventions of things)
    public function getItem($id = null) {
        return $this->getItems($id);
    }

    public function getForm($id = null, $data = array(), $name = null) {
        $data = (array) $this->getItems($id);

        if($_SESSION['form']) {
            $data = $_SESSION['form'];
            unset($data['password']); //never let the password be passed back
            unset($_SESSION['form']); //don't let the form session data persist
        }

        return $this->loadForm($data, $name);
    }

    protected function loadForm($data = array(), $name = null, $opts = array('control' => 'jform')) {
        //so we don't HAVE to pass the name of the form
        if(!$name) {
            $name = strtolower(basename($this->directory));
        }

        $form = new Form($name, $opts);
        $form->loadFile($this->directory . '/forms/' . $name . '.xml');

        if ($data) {
            $form->bind($data);
        }

        return $form;
    }

    public function save() {
        $app = WebApp::getInstance();
        $api = $app->getApi();
        $input = $app->input;

        $submission = $input->post->get('jform', array(), 'ARRAY');
        $submission = array_merge($submission, $input->files->get('jform'));
        $form = $this->loadForm($submission, null, array());

        $data = $form->processSave();

        if ($data->image) {
            $data->image = '@' . $data->image;
        }

        //because mimic does their naming this way
        $object_name = basename($this->directory);
        $id = strtolower(preg_replace('/(s)$/' ,'', $object_name)) . 'Id';
        $object_id = $data->$id;

        $return = true;
        try {
            $url = $object_name . '/' . $object_id;

            //try saving it
            var_dump($api->query($url, $data, array('Content-Type' => 'multipart/form-data; charset=utf-8'), 'post'));
        } catch(\InvalidArgumentException $e) {
            var_dump($e);
            $return = false;
        } catch(\RuntimeException $e) {
            var_dump($e);
            $return = false;
        }
        if (!$return) {
            exit();
        }
        return $return;
    }


}

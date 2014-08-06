<?php
namespace Webity\Web\Model;

use Joomla\Model\AbstractDatabaseModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Webity\Web\Form\Form;
use Joomla\Form\FormHelper;
use Webity\Web\Application\WebApp;

class Model extends AbstractDatabaseModel
{
    protected $directory = '';
    protected $namespace = '';
    protected $keyField = 'id';

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

        //allows us to pass more get requests to the query (i'm sure there is a joomla framework way of doing this)
        // $request = $app->get('uri');
        // echo '<pre>';
        // var_dump($request);
        // exit();
        $request = substr($app->get('uri.route'), strpos( $app->get('uri.route'), '?' ) );
        //too lazy to actually program this well. it works. back ooff.
        $request = $id ? '/' . $request : $request;

        $object_name = strtolower(basename($this->directory));
        
        $url = (strlen($request) > 1) ? $object_name . '/' . $id . $request : $object_name . '/' . $id;

        // exit($url);

        try {
            $response = $api->query($url)->data;
        } catch(\InvalidArgumentException $e) {
        } catch(\RuntimeException $e) {
        }

        if ($response) {
            // wrap this in its own name to handle the form "fields" attribute
            $return[$object_name] = $response;
        }

        return $return;
    }

    //sort of like an alias for getItems (because of the naming conventions of things)
    public function getItem($id = null) {
        return $this->getItems($id);
    }

    public function getForm($id = null, $data = array(), $name = null) {
        if (!$data) {
            $data = (array) $this->getItems($id);
        }

        $obj = strtolower(basename($this->directory));
        if($_SESSION['form'][$obj]) {
            $data = $_SESSION['form'][$obj];
            unset($data['password']); //never let the password be passed back
            unset($_SESSION['form'][$obj]); //don't let the form session data persist
        }

        return $this->loadForm($data, $name);
    }

    protected function loadForm($data = array(), $name = null, $opts = array('control' => 'jform')) {
        //so we don't HAVE to pass the name of the form
        if(!$name) {
            $name = strtolower(basename($this->directory));
        }

        FormHelper::addFormPath($this->directory . '/forms/');
        $form = new Form($name, $opts);
        $form->loadFile($name);

        if ($data) {
            $form->bind($data);
        }

        return $form;
    }

    public function save() {
        $app = WebApp::getInstance();
        $api = $app->getApi();
        $input = $app->input;
        //because mimic does their naming this way
        $object_name = strtolower(basename($this->directory));

        $submission = $input->post->get('jform', array(), 'ARRAY');
        $form = $this->loadForm($submission, null, array());
        if ($files = $input->files->get('jform')) {
            $form->bind($files);
        }

        $data = $form->processSave();

        if (count($data) == 1 && isset($data->{$object_name})) {
            $data = $data->$object_name;
        }

        if ($data->image) {
            $data->image = '@' . $data->image;
        }

        $id = strtolower(preg_replace('/(s)$/' ,'', $object_name)) . 'Id';
        $object_id = $data->$id;

        $return = true;
        try {
            $this->data = $data;

            $url = $object_name . '/' . $object_id;
            //try saving it
            $return = $api->query($url, $data, array('Content-Type' => 'multipart/form-data; charset=utf-8'), 'post');
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

        if ($return && isset($return->{$this->keyField})) {
            $return = $return->{$this->keyField};
        }

        return $return;
    }


}

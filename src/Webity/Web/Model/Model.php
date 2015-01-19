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
    protected $items = [];

    public function __construct(DatabaseDriver $db, Registry $state = null) {
        $rc = new \ReflectionClass(get_class($this));
        $this->directory = dirname($rc->getFileName());
        $this->namespace = $rc->getNamespaceName();

        return parent::__construct($db, $state);
    }


    public function getItems($id = null) {
        $app = WebApp::getInstance();
        $api = $app->getApi();

        if ($this->items && !$id) {
            return $this->items;
        }

        //allows us to pass more get requests to the query (i'm sure there is a joomla framework way of doing this)
        // $request = $app->get('uri');
        // echo '<pre>';
        // var_dump($request);
        // exit();
        //too lazy to actually program this well. it works. back ooff.
        $route = $app->get('uri.route');
        $strpos = strpos($route, '?');
        $route = ($strpos !== false) ? substr($route, $strpos) : '';

        $request = $id ? '/' . $route : $route;

        $object_name = basename($this->directory);

        $url = (strlen($request) > 1) ? $object_name . '/' . $id . $request : $object_name . '/' . $id;

        $start = $app->input->get('start', 0, 'INT');
        $search = $app->input->get('search', null, 'STRING');
        $public_url = $app->input->get('publicURL', null, 'STRING');

        if ($start) {
            $url .= '?start=' . $start;
        }

        if($search) {
            $url .= $start ? '&search=' . $search : '?search=' . $search;
        }
        
        if($public_url) {
            $url .= $start || $search ? '&publicURL=' . $public_url : '?publicURL=' . $public_url;
        }
        
        try {
            $response = array();
            $i = 0;
            do {
                $resp = $api->query($url);

                if (is_array($resp->data)) {
                    $response = array_merge($response, $resp->data);
                } elseif (is_object($resp->data)) {
                    $response = $resp->data;
                    break;
                }

                if (isset($resp->next)) {
                    $url = $resp->next;
                } else {
                    $url = false;
                }

                // TODO: Add true pagination...
                $i++;
                if ($i > 25) {
                    break;
                }
            } while ($url);
        } catch(\Exception $e) {
//            if ($app->get('debug')) {
//                $app->enqueueMessage($e->getMessage(), 'danger');
//            }
            
            $app->enqueueMessage($e->getMessage(), 'danger');
        }

        if ($response) {
            // wrap this in its own name to handle the form "fields" attribute
            $return[$object_name] = $response;

            // TODO: Fix up the pagination checks, this likely won't work with the above pagination hack
            if ($resp->next) {
                $return['more'] = true;
            }
            $return['start'] = $resp->start;
            $return['limit'] = $resp->limit;
            $return['base_url'] = $object_name;
        }

        if (!$id) {
            $this->items = $return;
        }

        return $return;
    }

    //sort of like an alias for getItems (because of the naming conventions of things)
    public function getItem($id = null) {
        return $this->getItems($id);
    }

    public function getForm($id = null, $data = array(), $name = null, $opts = array('control' => 'jform')) {
        if (!$data) {
            $data = (array) $this->getItems($id);
        }

        //var_dump($data);

        $obj = strtolower(basename($this->directory));
        if($_SESSION['form'][$obj]) {
            $data = $_SESSION['form'][$obj];
            unset($data['password']); //never let the password be passed back
            unset($_SESSION['form'][$obj]); //don't let the form session data persist
        }

        return $this->loadForm($data, $name, $opts);
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
        $object_name = basename($this->directory);

        $submission = $input->post->get('jform', array(), 'ARRAY');

        $form = $this->loadForm($submission, null, array());
        if ($files = $input->files->get('jform')) {
            $form->bind($files);
        }

        $data = $form->processSave();

        // append an '@' for files before they get shipped to the API
        foreach ($form->getFieldsets() as $fieldset) {
            foreach ($form->getFieldset($fieldset->name) as $field) {
                if ($field->type == 'File') {
                    if ($field->group) {
                        if ($data->{$field->group}->{$field->fieldname})
                            $data->{$field->group}->{$field->fieldname} = '@' . $data->{$field->group}->{$field->fieldname};
                    } else {
                        if ($data->{$field->fieldname})
                            $data->{$field->fieldname} = '@' . $data->{$field->fieldname};
                    }
                }
            }
        }

        // var_dump($submission);
        // var_dump($data);
        // exit();

        if (count($data) == 1 && isset($data->{$object_name})) {
            $data = $data->$object_name;
        }

        if ($data->id) {
            $object_id = $data->id;
        } else {
            $id = strtolower(preg_replace('/(s)$/' ,'', $object_name)) . 'Id';
            $object_id = $data->$id;
        }

        //allows us to override the object that get's requested if passed from the form
        if($data->object_name) {
            $object_name = $data->object_name;
        }

        // TODO: Move this back into the mimic files, not in the base
        //we need to attach the extra acting_as parameter if the user is an admin so the api can process it for post requests as well
        if($app->getUser()->admin) {
            $data->acting_as = $app->getUser()->acting_as;
        }

        $id = strtolower(preg_replace('/(s)$/' ,'', $object_name)) . 'Id';
        if (isset($data->$id)) {
            $object_id = $data->$id;
        }

        if (!$object_id) {
            $object_id = $app->input->get('id', '');
        }

        $return = true;
        try {
            $this->data = $data;
            $url = $object_name . '/' . $object_id;

            //try saving it
            $return = $api->query($url, $data, array('Content-Type' => 'multipart/form-data; charset=utf-8'), 'post');

        } catch(\InvalidArgumentException $e) {
            $app->enqueueMessage($e->getMessage());
            // var_dump($e);
            $return = false;
        } catch(\RuntimeException $e) {
            $app->enqueueMessage($e->getMessage());
            // var_dump($e);
            $return = false;
        }

        // var_dump($return);
        // exit();

        if ($return && isset($return->{$this->keyField})) {
            $return = $return->{$this->keyField};
        }

        return $return;
    }

    public function alterState($id, $state = -2)
    {
        $app = WebApp::getInstance();
        $api = $app->getApi();
        $input = $app->input;
        //because mimic does their naming this way
        $object_name = strtolower(basename($this->directory));

        $data = new \stdClass;
        $data->state = $state;

        //we need to attach the extra acting_as parameter if the user is an admin so the api can process it for post requests as well
        if($app->getUser()->admin) {
            $data->acting_as = $app->getUser()->acting_as;
        }

        $return = true;
        try {
            $this->data = $data;

            $url = $object_name . '/' . $id;

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

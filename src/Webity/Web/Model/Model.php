<?php
namespace Webity\Web\Model;

use Joomla\Model\AbstractDatabaseModel;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\Form\Form;

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

    public function getForm($name, $data = array()) {
    	return $this->loadForm($name, $data);
    }

	protected function loadForm($name, $data = array()) {
		$form = new Form($name);
		$form->loadFile($this->directory . '/forms/' . $name . '.xml');

		if ($data) {
			$form->bind($data);
		}

		return $form;
	}
}

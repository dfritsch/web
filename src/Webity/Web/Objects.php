<?php
namespace Webity\Rest;

use Webity\Web\Application\Api;
use Webity\Web\Objects\User;

abstract class Objects
{
	protected $text_fields = array();
	protected static $instances = array();
	protected static $data = array();
	protected static $_db = null;

	public function __construct ()
	{
		$this->_db = Api::getInstance()->getDbo();
	}

	public function execute() {
		$app = Api::getInstance();
		$this->_db = $app->getDbo();

		$id = $app->input->get('id');

		if ($id) {
			return $this->getData($id);
		} else {
			return $this->getList();
		}
	}
	
	/*
	**	Should be loading the data based on the id, so no search
	*/
	protected function getData($id = 0, $check_agency = true)
	{
		if (empty($this->data[$id]) || $this->data[$id]->simple)
		{
			$this->data[$id] = $this->load($id, $check_agency);
		}

		return $this->data[$id];
	}

	/*
	** This checks the input for a few parameters:
	** start - 0
	** limit - 10 (min 1, max 100)
	** sort - created_time
	** direction - desc
	** search - ''
	*/
	protected function getList() {
		$app = Api::getInstance();
		$input = $app->input;

		$data = new \stdClass;
		$data->start = $input->get->get('start', 0, 'INT');
		$data->limit = $input->get->get('limit', 10, 'INT');
		$data->sort = $input->get->get('sort', 'created_time', 'STRING');
		$data->direction = $input->get->get('direction', 'desc', 'STRING');

		if ($data->limit < 1 || $data->limit > 100) {
			throw new \Exception('Limit exceeds allowed bounds. Should be between 1 and 100', 400);
		}

		// TODO return total as well
		// $data->total = $this->getTotal();

		$app->appendBody($data);

		return $this->loadMany($data->start, $data->limit, $data->sort, $data->direction);
	}

	protected function clearData($id = 0) {
		if (isset($this->data[$id])) {
			unset($this->data[$id]);
		}
		return true;
	}

	// function to allow children classes to tweak search string
	protected function parseSearch($search) {
		return $search;
	}

	protected function processSearch($query) {
		$search['search'] = Api::getInstance()->input->get('search', '', 'STRING');

		$search = $this->parseSearch($search);
		
		if (is_array($search)) {
			foreach ($search as $key=>$val) {
				switch($key) {
					case 'search':
						if ($val) {
							$vals = explode(' ', $val);
							foreach ($vals as $val) {
								$where = array();
								$s = $this->_db->quote('%'.$this->_db->escape(trim($val, '-./\\,;:[]{}|`~!@#$%^&*()'), true).'%');
								foreach ($this->text_fields as $field) {
									$where[] = ''.$field.' LIKE ('.$s.')';
								}
								if ($where) {
									$query->where('(('.implode(') OR (', $where).'))');
								}
							}
						}
						break;
					default:
						if ($val) {
							$query->where(''.$key.'='.$this->_db->quote($val));
						}
						break;
				}
			}
		}
	}


	protected function _getListCount($query)
	{
		// Use fast COUNT(*) on JDatabaseQuery objects if there no GROUP BY or HAVING clause:
		if ($query instanceof JDatabaseQuery
			&& $query->type == 'select'
			&& $query->group === null
			&& $query->having === null)
		{
			$query = clone $query;
			$query->clear('select')->clear('order')->select('COUNT(*)');

			$this->_db->setQuery($query);
			return (int) $this->_db->loadResult();
		}

		// Otherwise fall back to inefficient way of counting all results.
		$this->_db->setQuery($query);
		$this->_db->execute();

		return (int) $this->_db->getNumRows();
	}

	abstract protected function load($id, $check_agency);
	abstract protected function loadMany($limitstart, $limit, $orderCol, $orderDirn);
}
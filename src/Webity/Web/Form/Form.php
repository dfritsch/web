<?php
namespace Webity\Web\Form;

use Joomla\Form\Form as JForm;

class Form extends JForm
{
	public static function getInstance($name, $data = null, $options = array(), $replace = true, $xpath = false)
	{
		// Reference to array with form instances
		$forms = &self::$forms;

		// Only instantiate the form if it does not already exist.
		if (!isset($forms[$name]))
		{

			$data = trim($data);

			if (empty($data))
			{
				throw new \Exception('JLIB_FORM_ERROR_NO_DATA');
			}

			// Instantiate the form.
			$forms[$name] = new Form($name, $options);

			// Load the data.
			if (substr(trim($data), 0, 1) == '<')
			{
				if ($forms[$name]->load($data, $replace, $xpath) == false)
				{
					throw new \Exception('JLIB_FORM_ERROR_XML_FILE_DID_NOT_LOAD');

					return false;
				}
			}
			else
			{
				if ($forms[$name]->loadFile($data, $replace, $xpath) == false)
				{
					throw new \Exception('JLIB_FORM_ERROR_XML_FILE_DID_NOT_LOAD');

					return false;
				}
			}
		}

		return $forms[$name];
	}

	protected function syncPaths() {
		if (!parent::syncPaths()) {
			return false;
		}

		$context = ''; //JFactory::getApplication()->input->get('wbtycontext');

		static $paths;

		// currently does not run recursively
		if (!$paths) {
			// Get any loadForm attributes from the form definition.
			$paths = $this->xml->xpath('//fieldset');

			foreach ($paths as $path) {
				foreach ($path->attributes() as $key => $value) {
					if ($key == 'loadForm' && !empty($value)) {
						$dom = dom_import_simplexml($path);
						if ($dom->parentNode) {
							$dom->parentNode->removeChild($dom);
						}
						if ($context != 'save') {
							$this->loadFile($value);
						}
						break;
					}
				}
			}
		}

		return true;
	}

}

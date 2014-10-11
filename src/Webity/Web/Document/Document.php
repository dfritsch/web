<?php
/**
 * Part of the Joomla Framework Document Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Webity\Web\Document;

use Joomla\Application\AbstractApplication;

/**
 * Joomla Framework Base Document Class
 *
 * @since  1.0
 */
class Document
{
	/**
	 * Document title
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $title = '';

	/**
	 * Document description
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $description = '';

	/**
	 * Document full URL
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $link = '';

	/**
	 * Document base URL
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $base = '';

	/**
	 * Contains the document language setting
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $language = 'en-gb';

	/**
	 * Contains the document direction setting
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $direction = 'ltr';

	/**
	 * Document generator
	 *
	 * @var    string
	 */
	public $_generator = '';

	/**
	 * Document modified date
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_mdate = '';

	/**
	 * Tab string
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_tab = "\11";

	/**
	 * Contains the line end string
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_lineEnd = "\12";

	/**
	 * Contains the character encoding string
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_charset = 'utf-8';

	/**
	 * Document mime type
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_mime = 'text/html';

	/**
	 * Document namespace
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_namespace = '';

	/**
	 * Document profile
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_profile = '';

	/**
	 * Array of linked scripts
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_scripts = array();

	/**
	 * Array of scripts placed in the header
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_script = array();

	/**
	 * Array of linked style sheets
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_styleSheets = array();

	/**
	 * Array of included style declarations
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_style = array();

	/**
	 * Array of meta tags
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_metaTags = array();

	/**
	 * The rendering engine
	 *
	 * @var    object
	 * @since  11.1
	 */
	public $_engine = null;

	/**
	 * The document type
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_type = 'html';

	/**
	 * Array of buffered output
	 *
	 * @var    mixed (depends on the renderer)
	 * @since  11.1
	 */
	public static $_buffer = null;

	/**
	 * JDocument instances container.
	 *
	 * @var    array
	 * @since  11.3
	 */
	protected static $instances = array();

	/**
	 * Media version added to assets
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $mediaVersion = null;

	/**
	 * Instance of the application
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $app = null;

	/**
	 * Array of Header <link> tags
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_links = array();

	/**
	 * Array of custom tags
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_custom = array();

	/**
	 * Name of the template
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $template = null;

	/**
	 * Base url
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $baseurl = null;

	/**
	 * Array of template parameters
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $params = null;

	/**
	 * File name
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_file = null;

	/**
	 * String holding parsed template
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_template = '';

	/**
	 * Array of parsed template JDoc tags
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_template_tags = array();

	/**
	 * Integer with caching setting
	 *
	 * @var    integer
	 * @since  11.1
	 */
	protected $_caching = null;

	/**
	 * Set to true when the document should be output as HTML%
	 *
	 * @var    boolean
	 * @since  12.1
	 */
	private $_html5 = null;

	/**
	 * Class constructor.
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		$this->app = $app;

		if (array_key_exists('lineend', $options))
		{
			$this->setLineEnd($options['lineend']);
		}

		if (array_key_exists('charset', $options))
		{
			$this->setCharset($options['charset']);
		}

		if (array_key_exists('language', $options))
		{
			$this->setLanguage($options['language']);
		}

		if (array_key_exists('direction', $options))
		{
			$this->setDirection($options['direction']);
		}

		if (array_key_exists('tab', $options))
		{
			$this->setTab($options['tab']);
		}

		if (array_key_exists('link', $options))
		{
			$this->setLink($options['link']);
		}

		if (array_key_exists('base', $options))
		{
			$this->setBase($options['base']);
		}

		if (array_key_exists('mediaversion', $options))
		{
			$this->setMediaVersion($options['mediaversion']);
		}
	}

	/**
	 * Set the document type
	 *
	 * @param   string  $type  Type document is to set to
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setType($type)
	{
		$this->_type = $type;

		return $this;
	}

	/**
	 * Returns the document type
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Get the contents of the document buffer
	 *
	 * @return  The contents of the document buffer
	 *
	 * @since   11.1
	 */
	public function getBuffer()
	{
		return self::$_buffer;
	}

	/**
	 * Set the contents of the document buffer
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setBuffer($content, $options = array())
	{
		self::$_buffer = $content;

		return $this;
	}

	/**
	 * Gets a meta tag.
	 *
	 * @param   string   $name       Value of name or http-equiv tag
	 * @param   boolean  $httpEquiv  META type "http-equiv" defaults to null
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getMetaData($name, $httpEquiv = false)
	{
		$name = strtolower($name);

		if ($name == 'generator')
		{
			$result = $this->getGenerator();
		}
		elseif ($name == 'description')
		{
			$result = $this->getDescription();
		}
		else
		{
			if ($httpEquiv == true)
			{
				$result = @$this->_metaTags['http-equiv'][$name];
			}
			else
			{
				$result = @$this->_metaTags['standard'][$name];
			}
		}

		return $result;
	}

	/**
	 * Sets or alters a meta tag.
	 *
	 * @param   string   $name        Value of name or http-equiv tag
	 * @param   string   $content     Value of the content tag
	 * @param   boolean  $http_equiv  META type "http-equiv" defaults to null
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setMetaData($name, $content, $http_equiv = false)
	{
		$name = strtolower($name);

		if ($name == 'generator')
		{
			$this->setGenerator($content);
		}
		elseif ($name == 'description')
		{
			$this->setDescription($content);
		}
		else
		{
			if ($http_equiv == true)
			{
				$this->_metaTags['http-equiv'][$name] = $content;
			}
			else
			{
				$this->_metaTags['standard'][$name] = $content;
			}
		}

		return $this;
	}

	/**
	 * Adds a linked script to the page
	 *
	 * @param   string   $url    URL to the linked script
	 * @param   string   $type   Type of script. Defaults to 'text/javascript'
	 * @param   boolean  $defer  Adds the defer attribute.
	 * @param   boolean  $async  Adds the async attribute.
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function addScript($url, $type = "text/javascript", $defer = false, $async = false)
	{
		$this->_scripts[$url]['mime'] = $type;
		$this->_scripts[$url]['defer'] = $defer;
		$this->_scripts[$url]['async'] = $async;

		return $this;
	}

	/**
	 * Adds a linked script to the page with a version to allow to flush it. Ex: myscript.js54771616b5bceae9df03c6173babf11d
	 * If not specified Joomla! automatically handles versioning
	 *
	 * @param   string   $url      URL to the linked script
	 * @param   string   $version  Version of the script
	 * @param   string   $type     Type of script. Defaults to 'text/javascript'
	 * @param   boolean  $defer    Adds the defer attribute.
	 * @param   boolean  $async    [description]
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   3.2
	 */
	public function addScriptVersion($url, $version = null, $type = "text/javascript", $defer = false, $async = false)
	{
		// Automatic version
		if ($version === null)
		{
			$version = $this->getMediaVersion();
		}

		if (!empty($version) && strpos($url, '?') === false)
		{
			$url .= '?' . $version;
		}

		return $this->addScript($url, $type, $defer, $async);
	}

	/**
	 * Adds a script to the page
	 *
	 * @param   string  $content  Script
	 * @param   string  $type     Scripting mime (defaults to 'text/javascript')
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function addScriptDeclaration($content, $type = 'text/javascript')
	{
		if (!isset($this->_script[strtolower($type)]))
		{
			$this->_script[strtolower($type)] = $content;
		}
		else
		{
			$this->_script[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Adds a linked stylesheet to the page
	 *
	 * @param   string  $url      URL to the linked style sheet
	 * @param   string  $type     Mime encoding type
	 * @param   string  $media    Media type that this stylesheet applies to
	 * @param   array   $attribs  Array of attributes
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array())
	{
		$this->_styleSheets[$url]['mime'] = $type;
		$this->_styleSheets[$url]['media'] = $media;
		$this->_styleSheets[$url]['attribs'] = $attribs;

		return $this;
	}

	/**
	 * Adds a linked stylesheet version to the page. Ex: template.css?54771616b5bceae9df03c6173babf11d
	 * If not specified Joomla! automatically handles versioning
	 *
	 * @param   string  $url      URL to the linked style sheet
	 * @param   string  $version  Version of the stylesheet
	 * @param   string  $type     Mime encoding type
	 * @param   string  $media    Media type that this stylesheet applies to
	 * @param   array   $attribs  Array of attributes
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   3.2
	 */
	public function addStyleSheetVersion($url, $version = null, $type = "text/css", $media = null, $attribs = array())
	{
		// Automatic version
		if ($version === null)
		{
			$version = $this->getMediaVersion();
		}

		if (!empty($version) && strpos($url, '?') === false)
		{
			$url .= '?' . $version;
		}

		return $this->addStyleSheet($url, $type, $media, $attribs);
	}

	/**
	 * Adds a stylesheet declaration to the page
	 *
	 * @param   string  $content  Style declarations
	 * @param   string  $type     Type of stylesheet (defaults to 'text/css')
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function addStyleDeclaration($content, $type = 'text/css')
	{
		if (!isset($this->_style[strtolower($type)]))
		{
			$this->_style[strtolower($type)] = $content;
		}
		else
		{
			$this->_style[strtolower($type)] .= chr(13) . $content;
		}

		return $this;
	}

	/**
	 * Sets the document charset
	 *
	 * @param   string  $type  Charset encoding string
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setCharset($type = 'utf-8')
	{
		$this->_charset = $type;

		return $this;
	}

	/**
	 * Returns the document charset encoding.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getCharset()
	{
		return $this->_charset;
	}

	/**
	 * Sets the global document language declaration. Default is English (en-gb).
	 *
	 * @param   string  $lang  The language to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setLanguage($lang = "en-gb")
	{
		$this->language = strtolower($lang);

		return $this;
	}

	/**
	 * Returns the document language.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Sets the global document direction declaration. Default is left-to-right (ltr).
	 *
	 * @param   string  $dir  The language direction to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setDirection($dir = "ltr")
	{
		$this->direction = strtolower($dir);

		return $this;
	}

	/**
	 * Returns the document direction declaration.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getDirection()
	{
		return $this->direction;
	}

	/**
	 * Sets the title of the document
	 *
	 * @param   string  $title  The title to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Return the title of the document.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set the assets version
	 *
	 * @param   string  $mediaVersion  Media version to use
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   3.2
	 */
	public function setMediaVersion($mediaVersion)
	{
		$this->mediaVersion = strtolower($mediaVersion);

		return $this;
	}

	/**
	 * Return the media version
	 *
	 * @return  string
	 *
	 * @since   3.2
	 */
	public function getMediaVersion()
	{
		return $this->mediaVersion;
	}

	/**
	 * Sets the base URI of the document
	 *
	 * @param   string  $base  The base URI to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setBase($base)
	{
		$this->base = $base;

		return $this;
	}

	/**
	 * Return the base URI of the document.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * Sets the description of the document
	 *
	 * @param   string  $description  The description to set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Return the title of the page.
	 *
	 * @return  string
	 *
	 * @since    11.1
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the document link
	 *
	 * @param   string  $url  A url
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setLink($url)
	{
		$this->link = $url;

		return $this;
	}

	/**
	 * Returns the document base url
	 *
	 * @return string
	 *
	 * @since   11.1
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Sets the document generator
	 *
	 * @param   string  $generator  The generator to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setGenerator($generator)
	{
		$this->_generator = $generator;

		return $this;
	}

	/**
	 * Returns the document generator
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getGenerator()
	{
		return $this->_generator;
	}

	/**
	 * Sets the document modified date
	 *
	 * @param   string  $date  The date to be set
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setModifiedDate($date)
	{
		$this->_mdate = $date;

		return $this;
	}

	/**
	 * Returns the document modified date
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getModifiedDate()
	{
		return $this->_mdate;
	}

	/**
	 * Sets the document MIME encoding that is sent to the browser.
	 *
	 * This usually will be text/html because most browsers cannot yet
	 * accept the proper mime settings for XHTML: application/xhtml+xml
	 * and to a lesser extent application/xml and text/xml. See the W3C note
	 * ({@link http://www.w3.org/TR/xhtml-media-types/
	 * http://www.w3.org/TR/xhtml-media-types/}) for more details.
	 *
	 * @param   string   $type  The document type to be sent
	 * @param   boolean  $sync  Should the type be synced with HTML?
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 *
	 * @link    http://www.w3.org/TR/xhtml-media-types
	 */
	public function setMimeEncoding($type = 'text/html', $sync = true)
	{
		$this->_mime = strtolower($type);

		// Syncing with meta-data
		if ($sync)
		{
			$this->setMetaData('content-type', $type . '; charset=' . $this->_charset, true);
		}

		return $this;
	}

	/**
	 * Return the document MIME encoding that is sent to the browser.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function getMimeEncoding()
	{
		return $this->_mime;
	}

	/**
	 * Sets the line end style to Windows, Mac, Unix or a custom string.
	 *
	 * @param   string  $style  "win", "mac", "unix" or custom string.
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setLineEnd($style)
	{
		switch ($style)
		{
			case 'win':
				$this->_lineEnd = "\15\12";
				break;
			case 'unix':
				$this->_lineEnd = "\12";
				break;
			case 'mac':
				$this->_lineEnd = "\15";
				break;
			default:
				$this->_lineEnd = $style;
		}

		return $this;
	}

	/**
	 * Returns the lineEnd
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function _getLineEnd()
	{
		return $this->_lineEnd;
	}

	/**
	 * Sets the string used to indent HTML
	 *
	 * @param   string  $string  String used to indent ("\11", "\t", '  ', etc.).
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setTab($string)
	{
		$this->_tab = $string;

		return $this;
	}

	/**
	 * Returns a string containing the unit for indenting HTML
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function _getTab()
	{
		return $this->_tab;
	}

	/**
	 * Load a renderer
	 *
	 * @param   string  $type  The renderer type
	 *
	 * @return  JDocumentRenderer  Object or null if class does not exist
	 *
	 * @since   11.1
	 * @throws  RuntimeException
	 */
	public function loadRenderer($type)
	{
		$class = '\\Webity\\Web\\Document\\Renderer\\' . $type;

		if (!class_exists($class))
		{
			$path = __DIR__ . '/' . $this->_type . '/renderer/' . $type . '.php';

			if (file_exists($path))
			{
				require_once $path;
			}
			else
			{
				throw new RuntimeException('Unable to load renderer class', 500);
			}
		}

		if (!class_exists($class))
		{
			return null;
		}

		$instance = new $class($this);

		return $instance;
	}

	/**
	 * Parses the document and prepares the buffers
	 *
	 * @param   array  $params  The array of parameters
	 *
	 * @return  JDocument instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function parse($params = array())
	{
		return $this;
	}

	/**
	 * Outputs the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since   11.1
	 */
	public function render($cache = false, $params = array())
	{
		$app = $this->app;

		if ($mdate = $this->getModifiedDate())
		{
			$app->modifiedDate = $mdate;
		}

		$app->mimeType = $this->_mime;
		$app->charSet  = $this->_charset;
	}

	/**
	 * Returns whether the document is set up to be output as HTML5
	 *
	 * @return  Boolean true when HTML5 is used
	 *
	 * @since   12.1
	 */
	public function isHtml5()
	{
		return $this->_html5;
	}

	/**
	 * Sets whether the document should be output as HTML5
	 *
	 * @param   bool  $state  True when HTML5 should be output
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function setHtml5($state)
	{
		if (is_bool($state))
		{
			$this->_html5 = $state;
		}
	}

	public function renderMeta() {
		$this->language = $this->getLanguage();
		// COPY & PASTE: The following code is from Joomla CMS, libraries/joomla/document/html/render/head.php

		// Get line endings
		$lnEnd = $this->_getLineEnd();
		$tab = $this->_getTab();
		$tagEnd = ' />';
		$buffer = '';

		// Generate charset when using HTML5 (should happen first)
		if ($this->isHtml5())
		{
			$buffer .= $tab . '<meta charset="' . $this->getCharset() . '" />' . $lnEnd;
		}

		// Generate base tag (need to happen early)
		$base = $this->getBase();
		if (!empty($base))
		{
			// Disabled to be called directly in the template
			//$buffer .= $tab . '<base href="' . $this->getBase() . '" />' . $lnEnd;
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($this->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv' && !($this->isHtml5() && $name == 'content-type'))
				{
					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
				}
				elseif ($type == 'standard' && !empty($content))
				{
					$buffer .= $tab . '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
				}
			}
		}

		// Don't add empty descriptions
		$documentDescription = $this->getDescription();
		if ($documentDescription)
		{
			$buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription) . '" />' . $lnEnd;
		}

		// Don't add empty generators
		$generator = $this->getGenerator();
		if ($generator)
		{
			$buffer .= $tab . '<meta name="generator" content="' . htmlspecialchars($generator) . '" />' . $lnEnd;
		}

		return $buffer;
	}

	public function renderHeader() {
		$this->language = $this->getLanguage();
		// COPY & PASTE: The following code is from Joomla CMS, libraries/joomla/document/html/render/head.php

		// Get line endings
		$lnEnd = $this->_getLineEnd();
		$tab = $this->_getTab();
		$tagEnd = ' />';
		$buffer = '';

		// Generate charset when using HTML5 (should happen first)
		// if ($this->isHtml5())
		// {
		// 	$buffer .= $tab . '<meta charset="' . $this->getCharset() . '" />' . $lnEnd;
		// }

		// // Generate base tag (need to happen early)
		// $base = $this->getBase();
		// if (!empty($base))
		// {
		// 	$buffer .= $tab . '<base href="' . $this->getBase() . '" />' . $lnEnd;
		// }

		// // Generate META tags (needs to happen as early as possible in the head)
		// foreach ($this->_metaTags as $type => $tag)
		// {
		// 	foreach ($tag as $name => $content)
		// 	{
		// 		if ($type == 'http-equiv' && !($this->isHtml5() && $name == 'content-type'))
		// 		{
		// 			$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
		// 		}
		// 		elseif ($type == 'standard' && !empty($content))
		// 		{
		// 			$buffer .= $tab . '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
		// 		}
		// 	}
		// }

		// // Don't add empty descriptions
		// $documentDescription = $this->getDescription();
		// if ($documentDescription)
		// {
		// 	$buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription) . '" />' . $lnEnd;
		// }

		// // Don't add empty generators
		// $generator = $this->getGenerator();
		// if ($generator)
		// {
		// 	$buffer .= $tab . '<meta name="generator" content="' . htmlspecialchars($generator) . '" />' . $lnEnd;
		// }

		$buffer .= $tab . '<title>' . htmlspecialchars($this->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . $lnEnd;

		// Generate link declarations
		foreach ($this->_links as $link => $linkAtrr)
		{
			$buffer .= $tab . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
			if ($temp = (string)$linkAtrr['attribs'])
			{
				$buffer .= ' ' . $temp;
			}
			$buffer .= ' />' . $lnEnd;
		}

		// Generate stylesheet links
		foreach ($this->_styleSheets as $strSrc => $strAttr)
		{
			$buffer .= $tab . '<link rel="stylesheet" href="' . $strSrc . '"';

			if (!is_null($strAttr['mime']) && (!$this->isHtml5() || $strAttr['mime'] != 'text/css'))
			{
				$buffer .= ' type="' . $strAttr['mime'] . '"';
			}

			if (!is_null($strAttr['media']))
			{
				$buffer .= ' media="' . $strAttr['media'] . '"';
			}

			if ($temp = (string)$strAttr['attribs'])
			{
				$buffer .= ' ' . $temp;
			}

			$buffer .= $tagEnd . $lnEnd;
		}

		// Generate stylesheet declarations
		foreach ($this->_style as $type => $content)
		{
			$buffer .= $tab . '<style type="' . $type . '">' . $lnEnd;

			// This is for full XHTML support.
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . '<![CDATA[' . $lnEnd;
			}

			$buffer .= $content . $lnEnd;

			// See above note
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . ']]>' . $lnEnd;
			}
			$buffer .= $tab . '</style>' . $lnEnd;
		}

		// Generate script file links
		foreach ($this->_scripts as $strSrc => $strAttr)
		{
			$buffer .= $tab . '<script src="' . $strSrc . '"';
			$defaultMimes = array(
				'text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript'
			);

			if (!is_null($strAttr['mime']) && (!$this->isHtml5() || !in_array($strAttr['mime'], $defaultMimes)))
			{
				$buffer .= ' type="' . $strAttr['mime'] . '"';
			}

			if ($strAttr['defer'])
			{
				$buffer .= ' defer="defer"';
			}

			if ($strAttr['async'])
			{
				$buffer .= ' async="async"';
			}

			$buffer .= '></script>' . $lnEnd;
		}

		// Generate script declarations
		foreach ($this->_script as $type => $content)
		{
			$buffer .= $tab . '<script type="' . $type . '">' . $lnEnd;

			// This is for full XHTML support.
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . '<![CDATA[' . $lnEnd;
			}

			$buffer .= $content . $lnEnd;

			// See above note
			if ($this->_mime != 'text/html')
			{
				$buffer .= $tab . $tab . ']]>' . $lnEnd;
			}
			$buffer .= $tab . '</script>' . $lnEnd;
		}

		foreach ($this->_custom as $custom)
		{
			$buffer .= $tab . $custom . $lnEnd;
		}

		return $buffer;
	}
}

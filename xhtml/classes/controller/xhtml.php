<?php defined('SYSPATH') or die('No direct script access.');

/**
* Abstract controller class for automatic xhtml templating.
*
* @package    Xhtml
* @author     Andrew Clarke
* @copyright  (c) 2010 Andrew Clarke
*/

abstract class Controller_Xhtml extends Controller_Template {
	
	// Public properties
	
	public $template = 'xhtml/template';
	
	// Protected properties
	
	/**
	* @var Xhtml Singleton instance
	*/
	protected $xhtml;

	/**
	* @var string HTML document type, one of xhtml::DOCTYPE_* constants
	*/
	protected $doctype;
	
	/**
	* @var string HTML language code, defaults to default kohana language
	*/
	protected $langcode;
	
	/**
	* @var array Attributes of the html tag as an array
	*/
	protected $htmlatts;

	/**
	* @var string Head title
	*/
	protected $title;

	/**
	* @var array Head meta tags
	*/
	protected $meta = array();

	/**
	* @var array Head link tags
	*/
	protected $links = array();
	
	/**
	* @var array Head style tags
	*/
	protected $styles = array();
	
	/**
	* @var array Head script tags
	*/
	protected $scripts = array();

	/**
	* @var array Head script blocks
	*/
	protected $codes = array();

	public function before()
	{
		parent::before();

		// Get an instance the Xhtml class
		$this->xhtml = Xhtml::instance();

		// Fixed arrays of property names
		$xhtml_properties = array('doctype', 'langcode', 'htmlatts');
		$head_properties = array('title', 'meta', 'links', 'styles', 'scripts', 'codes');

		// Only use controller defaults for external requests
		if ($this->request === Request::instance())
		{
			// Override Xhtml and Head singleton defaults with controller defaults
			// NOTE: All singleton defaults are over written, arrays are not merged
			foreach ($xhtml_properties as $key)
			{
				if (isset($this->{$key}) AND !empty($this->{$key}))
					Xhtml::${$key} = $this->{$key};
			}
			foreach ($head_properties as $key)
			{
				if (isset($this->{$key}) AND !empty($this->{$key}))
					Head::${$key} = $this->{$key};
			}
		}

		// Bind controller properties to Xhtml and Head singleton properties
		foreach ($xhtml_properties as $key)
		{
			$this->{$key} = & Xhtml::${$key};
		}
		foreach ($head_properties as $key)
		{
			$this->{$key} = & Head::${$key};
		}
		
		if ($this->auto_render)
		{
			// NOTE: Not sure if the settings above should go here or not
		}
	}

	public function after()
	{
		if ($this->auto_render)
		{
			// Move the template into the Xhtml body variable
			$this->xhtml->body = $this->template;
			
			// NOTE: Using templates
			$this->template = View::factory('xhtml/template')
				->bind('xhtml', $this->xhtml);
			$this->xhtml->head = View::factory('xhtml/head')
				->bind('head', $this->xhtml->head);
			
			// NOTE: Using strings
			//return $this->request->response = $this->xhtml;
		}
		return parent::after();
	}

} // End Controller_Xhtml
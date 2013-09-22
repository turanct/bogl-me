<?php
namespace Blog;


/**
 * Page class
 */
class Page {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected $raw;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $titleshort;

	/**
	 * @var int
	 */
	public $date;



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app, $filename) {
		// Assign
		$this->app = $app;
		$this->filename = (string) $filename;

		// Get data
		$this->init();
	}


	/**
	 * Init method
	 */
	public function init() {
		// Get raw file contents
		$this->raw = @file_get_contents($this->app['basedir'].'/markdown/pages/'.$this->filename.'.md');

		// Get title
		$html = $this->content();
		$this->title = preg_replace('/.*?<h1>(.*?)<\/h1>.*/ims', '$1', $html);

		// Get short title
		$this->titleshort = preg_replace('/[^a-z]+/i', '-', strtolower($this->filename));

		// Get date
		$this->date = strtotime(preg_replace('/(.*<\!\-\-\sDATE\:\s)([\s\d\-\:]*)\s\-\->(.*)/ims', '$2', $this->raw));
	}


	/**
	 * Content method
	 */
	public function content() {
		// Get rendered html from markdown
		return $this->app['markdown']->transformMarkdown($this->raw);
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get rendered html from twig
		return $this->app['twig']->render('page.html', array('blog' => $this->app['blog'], 'item' => $this));
	}


	/**
	 * Categories method
	 */
	public function categories() {
		// Extract
		$categories = preg_replace('/(.*<\!\-\-\sCATEGORIES\:\s)([\s\d\w\-\,]*)\s\-\->(.*)/ims', '$2', $this->raw);
		$categories = explode(', ', strtolower($categories));

		// Walk through categories
		foreach ($categories as $key => $category) {
			$categories[$key] = new Category($this->app, $category);
		}

		// Return
		return $categories;
	}
}

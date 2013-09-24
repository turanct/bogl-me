<?php
namespace Blog;


/**
 * Post class
 */
class Post {
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
		$this->raw = @file_get_contents($this->app['markdowndir'].'/posts/'.$this->filename.'.md');

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
		return $this->app['twig']->render('post.html', array('blog' => $this->app['blog'], 'item' => $this));
	}


	/**
	 * Tags method
	 */
	public function tags() {
		// Extract
		$match = preg_match_all('/<\!\-\-\sTAG\:\s([\s\d\w\-\/]*)\s\-\->/ims', $this->raw, $matches);

		// Did we get matches?
		if ($match === false) {
			return array();
		}

		// Prepare array
		$tags = array();

		// Walk through tags
		foreach ($matches[1] as $key => $tag) {
			$tags[] = new Tag($this->app, strtolower($tag));
		}

		// Return
		return $tags;
	}


	/**
	 * Categories method
	 */
	public function categories() {
		// Extract
		$match = preg_match_all('/<\!\-\-\sCATEGORY\:\s([\s\d\w\-\/]*)\s\-\->/ims', $this->raw, $matches);

		// Did we get matches?
		if ($match === false) {
			return array();
		}

		// Prepare array
		$categories = array();

		// Walk through categories
		foreach ($matches[1] as $key => $category) {
			$categories[] = new Category($this->app, strtolower($category));
		}

		// Return
		return $categories;
	}
}

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
	protected $filename;

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
	 * Magic getter method
	 */
	public function __get($value) {var_dump($value);
		if (isset($this->$value)) {
			return $this->$value;
		}
		elseif (method_exists($this, $value)) {
			return $this->$value();
		}
		else {
			return false;
		}
	}


	/**
	 * Init method
	 */
	public function init() {
		// Get raw file contents
		$this->raw = @file_get_contents($this->app['basedir'].'/markdown/posts/'.$this->filename.'.md');

		// Get title
		$raw = preg_replace('/(.*?)\n(?:\=){3,}/i', '<h1>$1</h1>', $this->raw);
		$raw = preg_replace('/\#\s(.*?)$/ims', '<h1>$1</h1>', $raw);
		$this->title = preg_replace('/.*?<h1>(.*?)<\/h1>.*/ims', '$1', $raw);

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
		return array();
	}


	/**
	 * Categories method
	 */
	public function categories() {
		return array();
	}

}

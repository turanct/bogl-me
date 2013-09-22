<?php
namespace Blog;


/**
 * Category class
 */
class Category {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $titleshort;



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app, $title) {
		// Assign
		$this->app = $app;
		$this->title = (string) $title;

		// Get short title
		$this->titleshort = preg_replace('/[^a-z]+/i', '-', strtolower($this->title));
	}


	/**
	 * Magic getter method
	 */
	public function __get($value) {
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
	 * Render method
	 */
	public function render() {
		// Get rendered html from twig
		return $this->app['twig']->render('index.html', array('blog' => $this->app['blog'], 'item' => $this, 'posts' => $this->getPosts(), 'pages' => $this->getPages()));
	}


	/**
	 * getPosts method
	 */
	public function getPosts() {
		// Prepare filenames array
		$filenames = array();

		// Get posts with this category in iterator
		$DataIterator = new DataIterator($this->app, 'category');

		// Walk through iterator values
		foreach ($DataIterator->entries[$this->title]['posts'] as $postfile) {
			$filenames[] = $postfile;
		}

		// Return FileIterator with post list
		return new FileIterator($this->app, 'post', 'ASC', $filenames);
	}


	/**
	 * getPages method
	 */
	public function getPages() {
		// Prepare filenames array
		$filenames = array();

		// Get pages with this category in iterator
		$DataIterator = new DataIterator($this->app, 'category');

		// Walk through iterator values
		foreach ($DataIterator->entries[$this->title]['pages'] as $postfile) {
			$filenames[] = $postfile;
		}

		// Return FileIterator with post list
		return new FileIterator($this->app, 'page', 'ASC', $filenames);
	}
}

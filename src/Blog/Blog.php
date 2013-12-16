<?php
namespace Blog;


/**
 * Blog class
 */
class Blog {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;


	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app) {
		// Assign
		$this->app = $app;

		// Get config data
		$config = @json_decode(file_get_contents($this->app['markdowndir'].'/config.json'));

		// Assign
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}


	/**
	 * Posts method
	 */
	public function posts() {
		return new FileIterator($this->app, 'post');
	}


	/**
	 * Pages method
	 */
	public function pages() {
		return new FileIterator($this->app, 'page');
	}


	/**
	 * Tags method
	 */
	public function tags() {
		return new DataIterator($this->app, 'tag');
	}


	/**
	 * Categories method
	 */
	public function categories() {
		return new DataIterator($this->app, 'category');
	}


	/**
	 * Archive method
	 *
	 * @param string        $type       The archive type
	 */
	public function archive($type) {
		// Typecast
		$type = (string) $type;

		// Return archive
		return new Archive($this->app, $this->$type());
	}


	/**
	 * Method to get the latest x posts
	 */
	public function latest() {
		return new Latest($this->app, $this->posts());
	}


	/**
	 * Rss method
	 */
	public function rss() {
		// Return archive
		return new Rss($this->app, $this->posts());
	}
}

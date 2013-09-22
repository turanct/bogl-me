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
}

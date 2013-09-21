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
		$config = @json_decode(file_get_contents($this->app['basedir'].'/config.json'));

		// Assign
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}


	/**
	 * Magic getter method
	 */
	public function __get($value) {
		switch ($value) {
			case 'posts':
				return new FileIterator($this->app, 'post');
				break;

			case 'pages':
				return new FileIterator($this->app, 'page');
				break;

			case 'tags':
				return new DataIterator($this->app, 'tag');
				break;

			case 'categories':
				return new DataIterator($this->app, 'category');
				break;

			default:
				return false;
				break;
		}
	}
}

<?php
namespace Blog;


/**
 * Latest class
 */
class Latest {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var \Iterator
	 */
	protected $items;



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app, $itemlist) {
		// Assign
		$this->app = $app;
		$this->items = $itemlist;

		// Fallback for when latest number is not set
		if (!isset($this->app['blog']->latest)) {
			$this->app['blog']->latest = 5;
		}
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get rendered html from twig
		return $this->app['twig']->render('latest.html', array('blog' => $this->app['blog'], 'items' => $this->items, 'item' => array('title' => 'Home')));
	}
}

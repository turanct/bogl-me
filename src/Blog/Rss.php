<?php
namespace Blog;


/**
 * Rss class
 */
class Rss {
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
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get rendered html from twig
		return $this->app['twig']->render('rss.xml', array('blog' => $this->app['blog'], 'items' => $this->items));
	}
}

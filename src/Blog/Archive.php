<?php
namespace Blog;


/**
 * Archive class
 */
class Archive {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var \Iterator
	 */
	protected $items;

	/**
	 * @var string
	 */
	public $name;


	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app, $itemlist) {
		// Assign
		$this->app = $app;
		$this->items = $itemlist;

		// Get the first item, to get the name of the archive
		$this->items->rewind();
		$name = strtolower(str_replace('Blog\\', '', get_class($this->items->current())));
		$this->name = ucfirst($this->app['types'][$name]);
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get template
		$template = (file_exists($this->app['themedir'].'/archive-'.strtolower($this->name).'.html')) ? 'archive-'.strtolower($this->name).'.html' : 'archive.html';

		// Get rendered html from twig
		return $this->app['twig']->render($template, array('blog' => $this->app['blog'], 'item' => array('title' => $this->name), 'items' => $this->items));
	}
}

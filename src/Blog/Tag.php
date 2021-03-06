<?php
namespace Blog;


/**
 * Tag class
 */
class Tag {
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
		$this->type = str_replace('Blog\\', '', __CLASS__);

		// Get short title
		$this->titleshort = preg_replace('/[^a-z]+/i', '-', strtolower($this->title));
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get template
		$template = (file_exists($this->app['themedir'].'/tag.html')) ? 'tag.html' : 'category.html';

		// Get rendered html from twig
		return $this->app['twig']->render($template, array('blog' => $this->app['blog'], 'item' => $this, 'posts' => $this->getPosts()));
	}


	/**
	 * getPosts method
	 */
	public function getPosts() {
		// Prepare filenames array
		$filenames = array();

		// Get posts with this tag in iterator
		$DataIterator = new DataIterator($this->app, 'tag');

		// Walk through iterator values
		foreach ($DataIterator->entries[$this->title]['posts'] as $postfile) {
			$filenames[] = $postfile;
		}

		// Empty list?
		if (empty($filenames)) {
			return array();
		}

		// Return FileIterator with post list
		return new FileIterator($this->app, 'post', 'ASC', $filenames);
	}
}

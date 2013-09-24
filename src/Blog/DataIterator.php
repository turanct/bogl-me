<?php
namespace Blog;


/**
 * DataIterator class
 */
class DataIterator implements \Iterator {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var int
	 */
	protected $position = 0;

	/**
	 * @var array
	 */
	public $entries = array();

	/**
	 * @var array
	 */
	protected $keys = array();

	/**
	 * @var string
	 */
	protected $type = '';



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 *
	 * @param \Pimple       $app    The application container
	 * @param string        $type   The data type
	 */
	public function __construct($app, $type) {
		// Assign
		$this->app = $app;
		$this->type = (string) $type;

		// Prepare entries array
		$entries = array();

		// Set search variables
		if ($type == 'tag') {
			$filetypes = array('post');
		}
		else {
			$filetypes = array('post', 'page');
		}

		// Set method
		$method = $this->app['types'][$type];

		// Walk through files, get tags/categories
		foreach ($filetypes as $filetype) {
			$posts = new FileIterator($this->app, $filetype);
			foreach ($posts as $post) {
				foreach ($post->$method() as $category) {
					if (!isset($entries[$category->title])) {
						$entries[$category->title] = array('posts' => array(), 'pages' => array());
					}
					$entries[$category->title][$filetype.'s'][] = $post->filename;
				}
			}
		}

		// Order by name
		ksort($entries);

		// Assign
		$this->entries = $entries;
		$this->keys = array_keys($this->entries);
	}


	/**
	 * Rewind method
	 */
	public function rewind() {
		// Reset the position to zero
		$this->position = 0;
	}


	/**
	 * Current method
	 */
	public function current() {
		// Prepare type
		$type = 'Blog\\'.ucfirst($this->type);

		// Return the current element
		return new $type($this->app, $this->keys[$this->position]);
	}


	/**
	 * Key method
	 */
	public function key() {
		// Return the current position
		return $this->position;
	}


	/**
	 * Next method
	 */
	public function next() {
		// Go to the next position
		++$this->position;
	}


	/**
	 * Valid method
	 */
	public function valid() {
		// Check if this position exists
		return isset($this->keys[$this->position]);
	}
}

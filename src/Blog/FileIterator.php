<?php
namespace Blog;


/**
 * FileIterator class
 */
class FileIterator implements \Iterator {
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
	protected $keys = array();

	/**
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var string
	 */
	protected $order = 'DESC';



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 *
	 * @param \Pimple       $app    The application container
	 * @param string        $type   The file type
	 * @param string        $order  The sort order
	 */
	public function __construct($app, $type, $order='DESC') {
		// Assign
		$this->app = $app;
		$this->type = (string) $type;
		$this->order = (string) $order;

		// List the directory
		$this->keys = glob($this->app['basedir'].'/markdown/'.$this->type.'s/*.md');

		// Order the list
		usort($this->keys, array($this, 'sort'));
	}


	/**
	 * Sort method
	 */
	public function sort($a, $b) {
		// Prepare type
		$type = ucfirst($this->type);

		// Create objects
		$a = new $type($this->app, basename($a, '.md'));
		$b = new $type($this->app, basename($b, '.md'));

		// Compare dates
		if ($this->order == 'DESC') {
			return ((int) $b->date - (int) $a->date);
		}
		else {
			return ((int) $a->date - (int) $b->date);
		}
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
		return new $type($this->app, basename($this->keys[$this->position], '.md'));
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

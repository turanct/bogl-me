<?php
namespace Blog;


/**
 * DataIterator class
 */
class DataIterator implements Iterator {
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

		// Walk through files, get tags/categories
		// Clean up doubles
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
		$type = ucfirst($this->type);

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

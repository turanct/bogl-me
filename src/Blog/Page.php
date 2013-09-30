<?php
namespace Blog;


/**
 * Page class
 */
class Page {
	// --------------------------------------------------------- Variables ---------------------------------------------------------
	/**
	 * @var \Pimple
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected $raw;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $titleshort;

	/**
	 * @var int
	 */
	public $date;



	// ---------------------------------------------------------- Methods ----------------------------------------------------------
	/**
	 * Constructor Method
	 */
	public function __construct($app, $filename) {
		// Assign
		$this->app = $app;
		$this->filename = (string) $filename;

		// Get data
		$this->init();
	}


	/**
	 * Init method
	 */
	public function init() {
		// Get raw file contents
		$this->raw = @file_get_contents($this->app['markdowndir'].'/pages/'.$this->filename.'.md');

		// Get title
		$html = $this->content();
		$this->title = preg_replace('/.*?<h1>(.*?)<\/h1>.*/ims', '$1', $html);

		// Get short title
		$this->titleshort = preg_replace('/[^a-z]+/i', '-', strtolower($this->filename));

		// Get date
		$this->date = strtotime(preg_replace('/(.*<\!\-\-\sDATE\:\s)([\s\d\-\:]*)\s\-\->(.*)/ims', '$2', $this->raw));
	}


	/**
	 * Content method
	 */
	public function content() {
		// Get rendered html from markdown
		return $this->app['markdown']->transformMarkdown($this->raw);
	}


	/**
	 * Description method
	 */
	public function description() {
		// Strip tags, title, and trim
		$description = strip_tags($this->content());
		$description = str_replace($this->title, '', $description);
		$ellipsis = (strlen($description)>280) ? '...' : '';
		$description = substr($description, 0, 280).$ellipsis;

		// Return
		return $description;
	}


	/**
	 * Render method
	 */
	public function render() {
		// Get template
		$template = (file_exists($this->app['themedir'].'/page.html')) ? 'page.html' : 'post.html';

		// Get rendered html from twig
		return $this->app['twig']->render($template, array('blog' => $this->app['blog'], 'item' => $this));
	}


	/**
	 * Categories method
	 */
	public function categories() {
		// Extract
		$match = preg_match_all('/<\!\-\-\sCATEGORY\:\s([\s\d\w\-\/\.\$]*)\s\-\->/ims', $this->raw, $matches);

		// Did we get matches?
		if ($match === false) {
			return array();
		}

		// Prepare array
		$categories = array();

		// Walk through categories
		foreach ($matches[1] as $key => $category) {
			$categories[] = new Category($this->app, strtolower($category));
		}

		// Return
		return $categories;
	}
}

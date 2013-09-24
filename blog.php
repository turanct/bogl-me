<?php
// Require the composer autoloader
require_once(__DIR__ . '/vendor/autoload.php');

// Create new pimple instance
$app = new Pimple();

// Use the Markdown parsers
use dflydev\markdown\MarkdownExtraParser;


/**
 * Services
 */
// Create shared markdown parser
$app['markdown'] = $app->share(function() use ($app) {
	return new MarkdownExtraParser();
});

// Create shared twig filesystem loader
$app['twig.loader'] = $app->share(function() use ($app) {
	return new Twig_Loader_Filesystem($app['themedir']);
});

// Create shared twig template engine
$app['twig'] = $app->share(function() use ($app) {
	return new Twig_Environment($app['twig.loader'], array('debug' => true));
});

// Create blog object
$app['blog'] = $app->share(function() use ($app) {
	return new Blog\Blog($app);
});

// Type names
$app['types'] = array('post' => 'posts', 'page' => 'pages', 'tag' => 'tags', 'category' => 'categories');


/**
 * Initialize the application
 */
$app['init'] = $app->protect(function() use ($app) {
	// Get command line options
	$options = getopt('i::t::o::', array('input::', 'theme::', 'output::'));

	// Set input directory
	if (isset($options['i'])) {
		$app['markdowndir'] = realpath($options['i']);
	}
	elseif (isset($options['input'])) {
		$app['markdowndir'] = realpath($options['input']);
	}
	elseif (getenv('BLOGDOWN_INPUT')) {
		$app['markdowndir'] = realpath(getenv('BLOGDOWN_INPUT'));
	}
	else {
		$app['markdowndir'] = __DIR__.'/content';
	}

	// Set theme directory
	if (isset($options['t'])) {
		$app['themedir'] = realpath($options['t']);
	}
	elseif (isset($options['theme'])) {
		$app['themedir'] = realpath($options['theme']);
	}
	elseif (getenv('BLOGDOWN_THEME')) {
		$app['themedir'] = realpath(getenv('BLOGDOWN_THEME'));
	}
	else {
		$app['themedir'] = __DIR__.'/theme';
	}

	// Set output directory
	if (isset($options['o'])) {
		$app['htmldir'] = realpath($options['o']);
	}
	elseif (isset($options['output'])) {
		$app['htmldir'] = realpath($options['output']);
	}
	elseif (getenv('BLOGDOWN_OUTPUT')) {
		$app['htmldir'] = realpath(getenv('BLOGDOWN_OUTPUT'));
	}
	else {
		$app['htmldir'] = __DIR__.'/html';
	}
});


/**
 * Render all types (posts, pages, tags, categories)
 */
$app['render.types'] = $app->protect(function() use ($app) {
	// Walk through data types
	foreach (array('posts', 'pages', 'tags', 'categories') as $type) {
		// Set directory name
		$typeDir = $app['htmldir'].'/'.$type;

		// Create the directory if it doesn't exist
		if (!is_dir($typeDir)) {
			mkdir($typeDir);
		}

		// Walk through items of this type
		foreach ($app['blog']->$type() as $item) {
			// Set directory name
			$itemDir = $typeDir.'/'.$item->titleshort;

			// Create the directory if it doesn't exist
			if (!is_dir($itemDir)) {
				mkdir($itemDir);
			}

			// Render the html file
			file_put_contents($itemDir.'/index.html', $item->render());
		}

		// Create the directory index file
		$archive = $app['blog']->archive($type);
		file_put_contents($typeDir.'/index.html', $archive->render());
	}
});


/**
 * Render the special pages and assets
 */
$app['render.special'] = $app->protect(function() use ($app) {
	// Render the home page
	switch ($app['blog']->home) {
		case 'page':
			$page = new Blog\Page($app, 'index.md');
			$rendered = $page->render();
			break;

		case 'post':
			$posts = $app['blog']->posts();
			$posts->rewind();
			$post = $posts->current();
			$rendered = $post->render();
			break;

		case 'static':
		default:
			$rendered = $app['twig']->render('index.html', array('blog' => $app['blog'], 'item' => array('title' => 'Home')));
			break;
	}
	file_put_contents($app['htmldir'].'/index.html', $rendered);

	// Render the 404 page
	file_put_contents($app['htmldir'].'/404.html', $app['twig']->render('404.html', array('blog' => $app['blog'], 'item' => array('title' => '404'))));

	// Render the RSS feed
	if ($app['blog']->rss === true) {
		$rss = $app['blog']->rss();
		file_put_contents($app['htmldir'].'/feed.xml', $rss->render());
	}

	// Copy the assets
	passthru('cd "'.__DIR__.'" && cp -R "'.realpath($app['themedir']).'/assets" "'.realpath($app['htmldir']).'/"');
});


/**
 * Main
 */
$app['run'] = $app->protect(function() use ($app) {
	$app['init']();

	// Create the html directory if it doesn't exist
	if (!is_dir($app['htmldir'])) {
		mkdir($app['htmldir']);
	}

	// Render files
	$app['render.types']();
	$app['render.special']();
});


/**
 * Run the main routine
 */
$app['run']();

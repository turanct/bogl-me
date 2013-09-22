<?php
// Require the composer autoloader
require_once(__DIR__ . '/vendor/autoload.php');

// Create new pimple instance
$app = new Pimple();

// Use the Markdown parsers
use dflydev\markdown\MarkdownExtraParser;

// Create basedir property
$app['basedir'] = __DIR__;

// Create shared markdown parser
$app['markdown'] = $app->share(function() use ($app) {
	return new MarkdownExtraParser();
});

// Create shared twig filesystem loader
$app['twig.loader'] = $app->share(function() use ($app) {
	return new Twig_Loader_Filesystem(__DIR__.'/theme');
});

// Create shared twig template engine
$app['twig'] = $app->share(function() use ($app) {
	return new Twig_Environment($app['twig.loader'], array('debug' => true));
});

// Create blog object
$app['blog'] = $app->share(function() use ($app) {
	return new Blog\Blog($app);
});


// Create the html directory if it doesn't exist
if (!is_dir(__DIR__ . '/html')) {
	mkdir(__DIR__ . '/html');
}

// Walk through data types
// foreach (array('posts') as $type) {
foreach (array('posts', 'pages', 'tags', 'categories') as $type) {
	// Set directory name
	$typeDir = __DIR__ . '/html/'.$type;

	// Create the directory if it doesn't exist
	if (!is_dir($typeDir)) {
		mkdir($typeDir);
	}

	// Walk through items of this type
	foreach ($app['blog']->$type as $item) {
		// Set directory name
		$itemDir = $typeDir.'/'.$item->titleshort;

		// Create the directory if it doesn't exist
		if (!is_dir($itemDir)) {
			mkdir($itemDir);
		}

		// Render the html file
		file_put_contents($itemDir.'/index.html', $item->render());
	}
}

// Render the home page

// Render the 404 page
file_put_contents(__DIR__.'/html/404.html', $app['twig']->render('404.html', array('blog' => $app['blog'], 'item' => array('title' => '404'))));

// Copy the assets
passthru('cd "'.__DIR__.'" && cp -R theme/assets html/');

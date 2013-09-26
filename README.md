Bogl
========================================


Bogl is a simple static blog generator in php. It uses plain Markdown files as posts and pages. Tags and Categories are generated. Themes can be applied in a simple manner. The result is a blog with only statically serverd pages (html, css, javascript).



1. Features
----------------------------------------

* Write your content in Markdown, on your local computer.
* Create your own theme using html, css and javascript, using the [Twig](http://twig.sensiolabs.org/documentation) template language.
* Get static html pages as an end-result, never panic about server load again!
* Version tracking of your posts is easy, just make your `markdown` directory a git repository.
* Deploy your blog with git
* ...



2. Configuration
----------------------------------------

### The `config.json` file

	{
		"title": "Bogl, the simple static blog generator",
		"titleshort": "Bogl",
		"url": "http://bogl.dev",
		"rss": false,
		"home": "last-post"
	}

The `title` and `titleshort` values are self-explanatory.

The `url` value is the base url of your blog. Don't add a trailing slash.

The `rss` value defines if an rss feed will be generated. For this to work, you'll need an `rss.xml` template present in your theme directory.

The `home` value defines what your homepage should look like. Possible values:

* `static` This will take the 'index.html' template from the theme directory and render it.
* `page` This will use the 'index.md' page file, and render it using the 'page.html' template.
* `post` This will render the last post using the post.html template.
* `posts` This will render all posts using the posts.html template file. **To be implemented**

Make sure that if you provide a value, you also make sure the necessary files are present. If not, we'll show the static front-page.



### The `content` directory

The directory in which the `config.json` file resides is called the `content` directory. This is where your blog gets written. The directory structure looks like this:

	/content
		/pages
			/page1.md
			/page2.md

		/posts
			/post1.md
			/post2.md

		/config.json

As you can see, you can just create pages and posts in the appropriate directories. For all text, we'll use Markdown files with filenames ending in `.md`. You can read more about this in chapter 2 'Creating posts and pages' of this README.



### The `theme` directory

This is where you define what your blog looks like. You can use html, css & javascript. Templating is done via [Twig](http://twig.sensiolabs.org/documentation), so you can harness the power of the Twig template engine. Some files **have** to be present in this directory, make sure they are:

* `index.html` This file contains a static index page for your site. It should be present, even if you don't use a static index page, as it is used as a fallback.
* `post.html` This file contains the template for blog posts. It will also be used as a fallback for pages.
* `category.html` This file contains the template for category overviews. It will also be used as a fallback for tags.
* `archive.html` This file contains the template for type overviews.
* `404.html` The 404 page for your blog.
* `assets` directory. This directory should be present, you can put `.css` and `.js` files here, they will be copied over to the html directory when we render our blog.

These are optional:

* `page.html` This file contains the template for pages.
* `tag.html` This file contains the template for tag overviews.
* `posts.html` This file contains the template for the home page 'posts' mode.
* `rss.xml` This file contains the template for an rss feed.



3. Creating posts and pages
----------------------------------------

1. To create a post or a page, just create a new Markdown document in the appropriate subdirectory of your `content` directory. The filename of the post should not contain spaces or uppercase letters, only `a-z` and hyphen(`-`) are allowed. The filename will be used as the *short title* for this post or page.
2. Your post should start with a Markdown header one. If you don't know how to do that, check out the [Markdown Spec](http://daringfireball.net/projects/markdown/). The first header one will be used as post or page title by Blog.
3. You can now write the post or page contents in regular Markdown.
4. At the bottom of your page or post, attach some specifications about that page or post:

```
	<!-- DATE: 2013-09-21 13:36 -->
	<!-- TAG: welcome -->
	<!-- TAG: intro -->
	<!-- CATEGORY: intro -->
```

DATE should be in the `YYYY-MM-DD HH:MM` format, and use 24-hour times.

There can be multiple TAG & CATEGORY comments, to attach multiple tags or categories to your posts.

*Please note that pages cannot have the DATE or TAG entries, only the CATEGORY entries.*



4. Compiling your blog
----------------------------------------

Bogl has to know three paths to compile your blog:

1. The `content` directory's path
2. The `theme` directory's path
3. The `html` directory's path


### Using long command line arguments

	bogl --input={path to content directory} --theme={path to theme directory} --output={path to html directory}

### Using short command line arguments

	bogl -i={path to content directory} -t={path to theme directory} -o={path to html directory}

### Using environment variables ($ENV)

#### Using `.bashrc`

You can use your `.bashrc` file to set these variables:

	export BOGL_INPUT={path to content directory}
	export BOGL_THEME={path to theme directory}
	export BOGL_OUTPUT={path to html directory}

Then, run Bogl without arguments:

	bogl



5. Installing Bogl on your system
----------------------------------------

Assuming you're on Mac OSX or GNU/Linux with `git`, `php` and `curl` installed:

	BOGL_PWD=`pwd` && git clone https://github.com/turanct/bogl-me.git /usr/local/bogl && cd /usr/local/bogl && curl -sS https://getcomposer.org/installer | php && ln -s /usr/local/bogl/bogl.php /usr/local/bin/bogl && cd $BOGL_PWD



6. License
----------------------------------------

Bogl-me is licensed under the *Modified BSD License*

<?php
/**
 * Stupid simple dispatcher 
 */

/**
 * Takes a url (or the current one) and "parses" it. Everything after
 * the first `/` is considered a param
 * 
 * @param string $url
 * @return array 
 */
function parseurl($url = null) {
	if (!$url) {
		$url = $_GET['url'];
	}
	$url = explode('/', $_GET['url']);
	$url = array_filter($url);
	if (empty($url)) {
		$url = array(
			'poll'
		);
	}
	return array(
		'page' => $url[0],
		'params' => array_slice($url, 1)
	);
}

/**
 * Quick redirect helper
 * 
 * @param string $location
 */
function redirect($location) {
	$base = $_SERVER['HTTP_HOST'];
	if ($location === '404') {
		header("Location: $base/$location", true, 404);
		exit();
	}
	header("Location: $base/$location");
	exit();
}

/**
 * Fairly useless debug function
 * 
 * @param mixed $obj 
 */
function debug($obj) {
	$out = null;
	if (is_string($obj)) {
		$out = $obj;
	} else {
		$out = var_export($obj, true);
	}
	echo "<pre>$out</pre>";
}

$url = parseurl();

if (!file_exists($url['page'].'.php')) {
	$url['page'] = '404';
}

require $url['page'].'.php';
<?php
/**
 * Stupid simple dispatcher 
 */
$url = parseurl();
$config = null;

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
	$host = $_SERVER['HTTP_HOST'];
	$path = trim(str_replace($_GET['url'], '', $_SERVER['REQUEST_URI']), '/');
	$url = explode('/', $_GET['url']);
	$url = array_filter($url);
	if (empty($url)) {
		$url = array(
			'poll'
		);
	}
	return array(
		'base' => "http://$host/$path",
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
	global $url;
	$location = trim($location, '/');
	if ($location === '404') {
		header("Location: {$url['base']}/$location", true, 404);
		exit();
	}
	header("Location: {$url['base']}/$location");
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

/**
 * Create connection 
 */
$db = config('database');
$connection = null;
try {
	$connection = new PDO(
		"sqlite:$db",
		null,
		null,
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
	);
} catch (PDOException $e) {
	debug($e);
	$url['page'] = 'error';
	$url['params'] = array('database');
}

/**
 * Helper function for getting config keys
 * 
 * @param string $key The config key. If null, the object is returned 
 */
function config($key = null) {
	global $config;
	if (!$config) {
		$config = json_decode(file_get_contents('config.json'));
	}
	if ($key) {
		return $config->{$key};
	}
	return $config;
}

if (!file_exists($url['page'].'.php')) {
	$url['page'] = '404';
}

require $url['page'].'.php';
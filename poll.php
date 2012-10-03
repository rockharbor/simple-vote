<?php

$url = parseurl();

if (empty($url['params'][0])) {
	redirect('404');
}

$config = json_decode(file_get_contents('config.json'));

try {
	$connection = new PDO(
		"sqlite:$config->database",
		null,
		null,
		array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
	);
} catch (PDOException $e) {
	debug($e);
}

try {
	$query = $connection->prepare("SELECT * FROM polls WHERE slug = :slug LIMIT 1;");
} catch (PDOException $e) {
	debug($e);
}

if (!$query) {
	redirect('404');
}

$params = array(
	':slug' => $url['params'][0]
);
if (!$query->execute($params)) {
	redirect('404');
}

$poll = $query->fetchObject();

$expired = $poll->expires && (strtotime($poll->expires) - strtotime() <= 0);
if (!$poll->enabled || ($expired)) {
	redirect('404');
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>RH Vote!</title>
		<link rel="stylesheet" href="styles.css" />
	</head>
	<body>
		<section>
		</section>
	</body>
</html>
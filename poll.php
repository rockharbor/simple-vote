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
	redirect("error/database");
}

$params = array(
	':slug' => $url['params'][0]
);
if (!$query->execute($params)) {
	redirect("error/database");
}

$poll = $query->fetchObject();

$expired = $poll->expires && (strtotime($poll->expires) - strtotime() <= 0);
if (!$poll->enabled || $expired) {
	$msg = $expired ? 'expired' : 'off';
	redirect("error/$msg");
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>RH Vote!</title>
		<link rel="stylesheet" href="/assets/styles.css" />
	</head>
	<body>
		<section>
		</section>
	</body>
</html>
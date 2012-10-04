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
	$query = $connection->prepare("SELECT `rowid`, * FROM `polls` WHERE `slug` = :slug LIMIT 1;");
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

// get existing votes
$votes = array();
if (isset($_COOKIE['votes'])) {
	$votes = unserialize($_COOKIE['votes']);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>RH Vote!</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" href="/css/styles.css" />
	</head>
	<body>
		<section>
			<header class="page-header">
				<h1><?php echo $poll->title; ?></h1>
			</header>
			<?php
			$query = $connection->prepare("SELECT `rowid`, * FROM `questions` WHERE `poll_id` = :poll_id ORDER BY `order` ASC;");
			if ($query->execute(array(':poll_id' => $poll->rowid))):
				while ($question = $query->fetchObject()): ?>
			<form action="/vote" method="post">
				<div class="question"><?php echo $question->question; ?></div>
				<input type="hidden" name="question" value="<?php echo $question->rowid; ?>" />
				<?php if (!isset($votes[$question->rowid])): ?>
					<button class="btn btn-success" type="submit"><i class="icon-thumbs-up icon-white"></i> Yes</button>
				<?php else: ?>
					<button class="btn" type="submit" value="Yes" disabled><i class="icon-ok"></i> Yes</button>
				<?php endif; ?>
			</form>
				<?php endwhile; ?>
			<?php endif; ?>
		</section>
	</body>
</html>
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

$query = $connection->prepare("SELECT `rowid`, * FROM `polls` WHERE `slug` = :slug LIMIT 1;");

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
		<?php if ($config->refresh): ?>
		<meta http-equiv="refresh" content="<?php echo $config->refresh; ?>" />
		<?php endif; ?>
		<title>RH Vote!</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" href="/css/styles.css" />
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	</head>
	<body>
		<section>
			<header class="page-header">
				<h1><?php echo $poll->title; ?></h1>
			</header>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Question</th>
						<th>Votes</th>
					</tr>
				</thead>
				<tbody>
			<?php
			$query = $connection->prepare("SELECT `rowid`, * FROM `questions` WHERE `poll_id` = :poll_id ORDER BY `order` ASC;");
			if ($query->execute(array(':poll_id' => $poll->rowid))):
				while ($question = $query->fetchObject()): ?>
					<tr>
						<td>
							<?php echo $question->question; ?>
						</td>
						<td>
							<?php echo $question->votes; ?>
						</td>
					</tr>
				<?php endwhile; ?>
			<?php endif; ?>
				</tbody>
			</table>
		</section>
	</body>
</html>
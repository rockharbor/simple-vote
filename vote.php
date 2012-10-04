<?php
header('Content-type: application/json');

if (empty($_POST) || empty($_POST['question'])) {
	redirect('404');
}

$response = array(
	'success' => false,
	'double' => false,
	'message' => 'Something went wrong when trying to process your vote.'
);

// get existing votes
$votes = array();
if (isset($_COOKIE['votes'])) {
	$votes = unserialize($_COOKIE['votes']);
}

if ($votes[$_POST['question']]) {
	$response = array(
		'success' => true,
		'double' => true,
		'message' => 'You already voted for that question!'
	);
	echo json_encode($response);
	return;
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

// make sure this is a valid poll
$query = $connection->prepare("SELECT `poll_id` FROM `questions` WHERE `rowid` = {$_POST['question']} LIMIT 1;");
if (!$query->execute()) {
	echo json_encode($response);
	return;
}

$question = $query->fetchObject();

$query = $connection->prepare("SELECT * FROM `polls` WHERE `rowid` = $question->poll_id LIMIT 1;");
if (!$query->execute()) {
	echo json_encode($response);
	return;
}

$poll = $query->fetchObject();
$expired = $poll->expires && (strtotime($poll->expires) - strtotime() <= 0);
if (!$poll->enabled || $expired) {
	$response['message'] = 'Invalid poll!';
	echo json_encode($response);
	return;
}

$query = $connection->prepare("UPDATE `questions` SET `votes` = (`votes` + 1) WHERE `rowid` = {$_POST['question']};");
if ($query->execute()) {
	$response = array(
		'success' => true,
		'double' => false,
		'message' => 'Thanks for voting!'
	);
	$votes[$_POST['question']] = true;
	setcookie('votes', serialize($votes), strtotime('+20 years'));
} else {
	$response['message'] = 'Something went wrong when trying to process your vote.';
}

echo json_encode($response);
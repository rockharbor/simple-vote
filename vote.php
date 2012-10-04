<?php
header('Content-type: application/json');

if (empty($_POST) || empty($_POST['question'])) {
	redirect('404');
}

$response = array();

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
} else {
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
	
	$query = $connection->prepare('UPDATE `questions` SET `votes` = (`votes` + 1) WHERE `rowid` = :rowid;');
	if ($query->execute(array(':rowid' => $_POST['question']))) {
		$response = array(
			'success' => true,
			'double' => false,
			'message' => 'Thanks for voting!'
		);
		$votes[$_POST['question']] = true;
		setcookie('votes', serialize($votes), strtotime('+20 years'));
	} else {
		$response = array(
			'success' => false,
			'double' => false,
			'message' => 'Something went wrong when trying to process your vote.'
		);
	}
}

echo json_encode($response);
<?php

$url = parseurl();

switch ($url['params'][0]) {
	case 'database':
		$msg = 'There was an error with the database.';
		break;
	case 'off':
		$msg = 'This poll is no longer active.';
		break;
	case 'expired':
		$msg = 'This poll has expired.';
		break;
	default:
		$msg = 'An unknown error occured.';
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Error</title>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/css/bootstrap-combined.min.css" />
		<link rel="stylesheet" href="/css/styles.css" />
	</head>
	<body>
		<section>
			<header class="page-header">
				<h1><?php echo $msg; ?></h1>
			</header>
		</section>
	</body>
</html>
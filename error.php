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
		<link rel="stylesheet" href="/assets/styles.css" />
	</head>
	<body>
		<section>
			<header>
				<h1><?php echo $msg; ?></h1>
			</header>
		</section>
	</body>
</html>
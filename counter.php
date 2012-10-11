<?php

global $url, $connection;

if (empty($url['params'][0])) {
	redirect('404');
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

function format_question($question) {
	echo "<span class=\"question\">$question->question</span>";
	$votes = str_split($question->votes);
	echo '<span class="votes">';
	foreach ($votes as $v) {
		echo "<span title=\"$v\" class=\"number\">$v</span>";
	}
	echo '</span>';
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>RH Vote!</title>
		<link rel="stylesheet" href="<?php echo $url['base']; ?>/css/fonts.css" />
		<link rel="stylesheet" href="<?php echo $url['base']; ?>/css/styles.css" />
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<?php if (config('refresh')): ?>
		<script>
			function refresh() {
				console.log(window.location);
				$('#page').load(window.location.href+' #counts');
				setTimeout(refresh, <?php echo config('refresh')*1000; ?>);
			}
			
			$(document).ready(function() {
				setTimeout(refresh, <?php echo config('refresh')*1000; ?>);
			});
		</script>
		<?php endif; ?>
	</head>
	<body>
		<section>
			<header class="page-header">
				<img src="<?php echo $url['base']; ?>/img/header.jpg" />
			</header>
			<div id="page">
				<table id="counts" class="table table-striped">
					<tbody>
				<?php
				$query = $connection->prepare("SELECT `rowid`, * FROM `questions` WHERE `poll_id` = :poll_id ORDER BY `order` ASC;");
				if ($query->execute(array(':poll_id' => $poll->rowid))):
					$questions = $query->fetchAll(PDO::FETCH_CLASS);
					$questions = array_chunk($questions, ceil(count($questions)/2));
					foreach ($questions[0] as $row => $obj): ?>
						<tr>
							<td>
								<?php 
								echo format_question($questions[0][$row]);
								?>
							</td>
							<td>
								<?php
								if (isset($questions[1][$row])) {
									echo format_question($questions[1][$row]);
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
					</tbody>
				</table>
			</div>
		</section>
	</body>
</html>
<?php include('imanager.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Page Title</title>

	<meta name="description" content="">

	<!-- Mobile-friendly viewport -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<!-- js stuff -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

</head>
<body>
<main role="main">
	<h1>Easy and Simple to Use</h1>
	<p>If you're seeing the date format here <strong><?php echo $imanager->config->systemDateFormat; ?></strong>,
		it says IManager has been properly included.</p>
</main>
<footer role="contentinfo">
	<small>Copyright &copy;
		<time datetime="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></time> Ehret Studio</small>
</footer>
</body>
</html>

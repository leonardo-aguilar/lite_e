<html>
	<head>
		<title>Titulo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>

<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	echo 'Versión actual de PHP: ' . phpversion();

	echo Doctrine_Core::getPath();

	echo "<p>un párrafo</p>";

	echo "<p>otro párrafo</p>";

?>
	</body>
</html>

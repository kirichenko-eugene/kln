<?php
	include '../config/config.php';
	include '../config/roots.php';
	session_destroy();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Выход</title>
	<link rel="stylesheet" href="<?=$cssroot?>">
</head>
<body>
	<div class="content">
		<div class="row justify-content-center m-2">
			<div class="col-sm-12 col-md-12 col-lg-12 text-center"><h1>До свидания!</h1></div>
			<a href="<?=$siteroot?>" class="btn btn-warning m-2">К логину</a>
		</div>
	</div>
</body>
</html>
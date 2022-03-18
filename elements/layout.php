<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$title?></title>
	<link rel="stylesheet" href="<?=$cssroot?>">
</head>
<body>
	<div class="content">
		
		<?php include "header.php" ?>
		
		<?php include 'info.php'; ?>

		<div class="row justify-content-center m-2">
			<?=$content?>
		</div>
		
		<?php include "footer.php" ?>
		
	</div>
	<script src="<?=$siteroot?>/assets/js/jquery-3.5.1.min.js"></script>
	<script src="<?=$siteroot?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
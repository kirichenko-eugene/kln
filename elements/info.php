<?php
	'<div class="row justify-content-center m-2">';
		if(isset($_SESSION['message'])) {
			$status = $_SESSION['message']['status'];
			$text = $_SESSION['message']['text'];

			if ($status == 'success') {
				$status = 'text-success';
			} else {
				$status = 'text-danger';
			}

			echo "<div class=\"col-sm-12 col-md-12 col-lg-12 text-center display-4\"><p class=\"$status font-weight-bold\">$text</p></div>";
			unset($_SESSION['message']);
		}
	'</div>';
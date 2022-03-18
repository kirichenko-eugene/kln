<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRestaurant($siteroot, $cssroot)
		{
			$title = 'Добавить ресторан';
			$topInfo = 'Новый ресторан';

			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
			} else {
				$name = '';
			}

			$content .= '<form method="POST">
					<div class="form-group">
					<label for="name">Новый ресторан</label>';
			$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
			$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Создать</button>
						</form>';

			
			include '../elements/layout.php';
		} 

		function addRestaurant($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = $_POST['name'];

				$query = "SELECT COUNT(*) as count FROM restaurants WHERE name = :name";
				$params = [
					':name' => $name
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$isName = $stmt->fetchColumn();

				if ($isName != 0) {
					$_SESSION['message'] = ['text' => 'Ресторан с таким названием уже был добавлен', 
					'status' => 'error'];
				} else {
					$query = "INSERT INTO `restaurants` (`name`) VALUES (:name)";
					$params = [
						':name' => $name
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Ресторан успешно добавлен', 
					'status' => 'success'];

					header("Location: $siteroot/pages/restaurants.php");
					die();
				}	

			} else {
				return '';
			}
		}

		addRestaurant($pdo, $siteroot);
		getRestaurant($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


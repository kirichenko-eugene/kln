<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRestaurant($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать ресторан';
			$topInfo = 'Редактировать ресторан';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM restaurants WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$restaurant = $stmt->fetch();	

				if($restaurant) {
					if (isset($_POST['name'])) {
						$name = $_POST['name'];
					} else {
						$name = $restaurant['name'];
					}

					$content = '<form method="POST">
					<div class="form-group">
					<label for="name">Редактировать ресторан</label>';
					$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
					$content .=	'</div>
						<button type="submit" class="btn btn-primary" name="submit">Редактировать</button>
						</form>';
	
				} else {
					$content = 'Данный ресторан не найден';
				}		
			} else {
				$content = 'Данный ресторан не найден';
			}

			include '../elements/layout.php';
		}

		function addRestaurant($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = ($_POST['name']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM restaurants WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$restaurant = $stmt->fetch();	

					if ($restaurant['name'] !== $name) {

						$query = "SELECT COUNT(*) as count FROM restaurants WHERE name = :name";
						$params = [
							':name' => $name
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isName = $stmt->fetchColumn();

						if ($isName !== 0) {
							$_SESSION['message'] = ['text' => 'Ресторан с таким названием уже существует', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `restaurants` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "Ресторан '{$restaurant['name']}' успешно обновлен", 
													'status' => 'success'];

							header("Location: $siteroot/pages/restaurants.php");
							die();
						}
					} else {
						$query = "UPDATE `restaurants` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

						$_SESSION['message'] = ['text' => "Ресторан '{$restaurant['name']}' успешно обновлен", 
													'status' => 'success'];

						header("Location: $siteroot/pages/restaurants.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addRestaurant($pdo, $siteroot);
		getRestaurant($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


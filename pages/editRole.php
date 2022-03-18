<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRole($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать роль';
			$topInfo = 'Редактировать роль';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM roles WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$role = $stmt->fetch();	

				if($role) {
					if (isset($_POST['name'])) {
						$name = $_POST['name'];
					} else {
						$name = $role['role'];
					}

					$content = '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="name">Редактировать роль</label>';
					$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
					$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Редактировать</button>
						</form>';
	
				} else {
					$content = 'Даннная роль не найдена';
				}		
			} else {
				$content = 'Даннная роль не найдена';
			}

			include '../elements/layout.php';
		}

		function addRole($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = ($_POST['name']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM roles WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$role = $stmt->fetch();	

					if ($role['role'] !== $name) {

						$query = "SELECT COUNT(*) as count FROM roles WHERE role = :role";
						$params = [
							':role' => $name
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isRole = $stmt->fetchColumn();

						if ($isRole !== 0) {
							$_SESSION['message'] = ['text' => 'Роль с таким названием уже существует', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `roles` SET `role` = :role WHERE `id` = :id";
							$params = [
								':id' => $id,
								':role' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "Роль '{$role['role']}' успешно обновлена", 
													'status' => 'success'];

							header("Location: $siteroot/pages/roles.php");
							die();
						}
					} else {
						$query = "UPDATE `roles` SET `role` = :role WHERE `id` = :id";
							$params = [
								':id' => $id,
								':role' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

						$_SESSION['message'] = ['text' => "Роль '{$role['role']}' успешно обновлена", 
													'status' => 'success'];

						header("Location: $siteroot/pages/roles.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addRole($pdo, $siteroot);
		getRole($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


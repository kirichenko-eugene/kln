<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRole($siteroot, $cssroot)
		{
			$title = 'Добавить роль';
			$topInfo = 'Новая роль';

			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
			} else {
				$name = '';
			}

			$content .= '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="name">Новая роль</label>';
			$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
			$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Создать</button>
						</form>';

			
			include '../elements/layout.php';
		} 

		function addRole($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = $_POST['name'];

				$query = "SELECT COUNT(*) as count FROM roles WHERE role = :role";
				$params = [
					':role' => $name
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$isRole = $stmt->fetchColumn();

				if ($isRole != 0) {
					$_SESSION['message'] = ['text' => 'Роль с таким названием уже была добавлена', 
					'status' => 'error'];
				} else {
					$query = "INSERT INTO `roles` (`role`) VALUES (:role)";
					$params = [
						':role' => $name
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Роль успешно добавлена', 
					'status' => 'success'];

					header("Location: $siteroot/pages/roles.php");
					die();
				}	

			} else {
				return '';
			}
		}

		addRole($pdo, $siteroot);
		getRole($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


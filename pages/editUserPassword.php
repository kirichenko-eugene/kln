<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getUser($pdo, $siteroot, $cssroot)
		{
			if (isset($_GET['id'])) {
				$title = 'Смена пароля';
				$topInfo = 'Пароль';

				$id = $_GET['id'];

				$query = "SELECT * FROM users WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$user = $stmt->fetch();	

				if($user) {

					$password = '';

					$content = '<form method="POST">
					<div class="form-group">
					<label for="password">Новый пароль</label>
					<input type="password" class="form-control" id="password" aria-describedby="password" name="password" required>
					</div>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Изменить</button>
					</form>';
				} else {
					$content = 'Данный пользователь не найден';
				}	
			}else {
				$content = 'Данный пользователь не найден';
			}

			include '../elements/layout.php';
		}

		function changePassword($pdo, $siteroot)
		{
			if (isset($_POST['password'])) {
				$password = $_POST['password'];
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {

						$_SESSION['message'] = ['text' => 'Пароль не удовлетворяет требованиям! Нужно хотя бы 1 число, 1 буква, 8-12 символов', 
						'status' => 'error'];

					} else {
						if($_POST['password'] == '') {
							$password = password_hash(mt_rand(999999, 999999999), PASSWORD_DEFAULT);
						} else {
							$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
						}

						$query = "UPDATE `users` SET `password` = :password WHERE `id` = :id";
						$params = [
							':id' => $id,
							':password' => $password

						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => 'Пароль успешно обновлен', 
						'status' => 'success'];

						header("Location: $siteroot/pages/users.php");
						die();
					}	
				}
			} else {
				return '';
			}
		}

		changePassword($pdo, $siteroot);
		getUser($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


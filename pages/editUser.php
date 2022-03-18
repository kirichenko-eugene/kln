<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getUser($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать пользователя';
			$topInfo = 'Редактировать пользователя';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM users WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$user = $stmt->fetch();	

				if($user) {
					if (isset($_POST['login']) and isset($_POST['name'])) {
						$login = $_POST['login'];
						$name = $_POST['name'];
						$telegram = $_POST['telegram'];
					} else {
						$login = $user['login'];
						$name = $user['russian_name'];
						$telegram = $user['telegram'];
					}

					ob_start();
					include '../elements/userForm.php';
					$content = ob_get_clean();
					
				} else {
					$content = 'Данный пользователь не найден';
				}		
			} else {
				$content = 'Данный пользователь не найден';
			}

			include '../elements/layout.php';
		}

		function addUser($pdo, $siteroot)
		{
			if (isset($_POST['login']) and isset($_POST['name'])) {
				$login = ($_POST['login']);
				$name = ($_POST['name']);
				$telegram = ($_POST['telegram']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM users WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$user = $stmt->fetch();	

					if ($user['login'] !== $login) {

						$query = "SELECT COUNT(*) as count FROM users WHERE login = :login";
						$params = [
							':login' => $login
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isLogin = $stmt->fetchColumn();

						if ($isLogin !== 0) {
							$_SESSION['message'] = ['text' => 'Пользователь с таким логином уже зарегистрирован', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `users` SET `login` = :login, `russian_name` = :russian_name, `telegram` = :telegram WHERE `id` = :id";
							$params = [
								':id' => $id,
								':login' => $login,
								':russian_name' => $name,
								':telegram' => $telegram
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "Пользователь '{$user['login']}' успешно обновлен", 
													'status' => 'success'];

							header("Location: $siteroot/pages/users.php");
							die();
						}
					} else {
						$query = "UPDATE `users` SET `login` = :login, `russian_name` = :russian_name, `telegram` = :telegram WHERE `id` = :id";
						$params = [
							':id' => $id,
							':login' => $login,
							':russian_name' => $name,
							':telegram' => $telegram
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => "Пользователь '{$user['login']}' успешно обновлен", 
												'status' => 'success'];

						header("Location: $siteroot/pages/users.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addUser($pdo, $siteroot);
		getUser($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


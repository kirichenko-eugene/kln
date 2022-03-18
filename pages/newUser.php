<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getUser($siteroot, $cssroot)
		{
			$title = 'Добавить пользователя';
			$topInfo = 'Новый пользователь';

			if (isset($_POST['submit'])) {
				$login = $_POST['login'];
				$name = $_POST['name'];
				$telegram = $_POST['telegram'];
				$password = '';
				$adminCheck = $_POST['adminCheck'];
			} else {
				$login = '';
				$name = '';
				$telegram = '';
				$password = '';
				$adminCheck = '';
			}

			ob_start();
			include '../elements/newUserForm.php';
			$content = ob_get_clean();
			
			include '../elements/layout.php';
		} 

		function addUser($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$login = $_POST['login'];
				$name = $_POST['name'];
				$telegram = $_POST['telegram'];
				
				if ($_POST['adminCheck'] == true) {
					$adminCheck = 1;
				} else {
					$adminCheck = 0;
				}

				if(!preg_match("/^[a-zA-Z0-9\s]{3,30}$/", $login)){

					$_SESSION['message'] = ['text' => 'Логин может состоять только из букв английского алфавита, цифр и иметь длину от 3 до 30 символов', 
					'status' => 'error'];

				} else {
					if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $_POST['password'])) {

						$_SESSION['message'] = ['text' => 'Пароль не удовлетворяет требованиям! Нужно хотя бы 1 число, 1 буква, 8-12 символов', 
						'status' => 'error'];

					} else {
						if($_POST['password'] == '') {
							$password = password_hash(mt_rand(999999, 999999999), PASSWORD_DEFAULT);
						} else {
							$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
						}

						$query = "SELECT COUNT(*) as count FROM users WHERE login = :login";
						$params = [
							':login' => $login
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isLogin = $stmt->fetchColumn();

						if ($isLogin != 0) {
							$_SESSION['message'] = ['text' => 'Пользователь с таким логином уже зарегистрирован', 
							'status' => 'error'];
						} else {
							$query = "INSERT INTO `users` (`login`, `russian_name`, `telegram`, `password`, `usersign`) 
							VALUES (:login, :russian_name, :telegram, :password, :usersign)";
							$params = [
								':login' => $login,
								':russian_name' => $name,
								':telegram' => $telegram,
								':password' => $password,
								':usersign' => $adminCheck
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => 'Пользователь успешно добавлен', 
							'status' => 'success'];

							header("Location: $siteroot/pages/users.php");
							die();
						}	
					}
				}
			} else {
				return '';
			}
		}

		addUser($pdo, $siteroot);
		getUser($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


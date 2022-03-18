<?php 
	include '../config/config.php';
	include '../config/roots.php';

	function getUser($pdo, $siteroot)
	{

		if (isset($_POST['submit']) and isset($_POST['login']) and isset($_POST['password'])) {
			$login = $_POST['login'];
			$password = $_POST['password'];
			$query = "SELECT * FROM users WHERE login = :login and status = 1 LIMIT 1";
			$params = [
				':login' => $login
			];
			$stmt = $pdo->prepare($query);
			$stmt->execute($params);
			$userVerify = $stmt->fetch();

			if($userVerify) {
				if($userVerify['login'] == $login AND $userVerify['password'] == password_verify($_POST['password'], $userVerify['password'])){

					$_SESSION['auth'] = true;

					$_SESSION['user'] = ['login' => $userVerify['login'],
										'russian' => $userVerify['russian_name'],
										'id' => $userVerify['id'],
										'superuser' => $userVerify['usersign']];

					$_SESSION['message'] = ['text' => 'Логин пользователя выполнен успешно!', 
											'status' => 'success'];

					header("Location: $siteroot");
					die();
				} else {
					echo '<div class="text-center"><p class="font-weight-bold text-danger">Логин или пароль неверны!</p></div>';
				}
			} else {
				echo '<div class="text-center"><p class="font-weight-bold text-danger">Логин или пароль неверны!</p></div>';
			}
		} 
	}

	if (isset($_SESSION['auth'])) {
		header("Location: $siteroot");
		die();
	}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Логин</title>
	<link rel="stylesheet" href="<?=$cssroot?>">
</head>
<body>
	<div class="container d-flex h-100 justify-content-center">
		<div class="row align-self-center">
			<div class="col-xl-10 col-lg-10 mx-auto mt-5">
				<div class="jumbotron text-center">
					<h1>GoodCity <span class="badge badge-warning">КЛН</span></h1>
					<h3>Войдите в учетную запись</h3>
					<form method="post">
						<div class="form-group">
							<label for="username">Имя пользователя</label>
							<input type="text" class="form-control" id="username" aria-describedby="loginHelp" placeholder="Введите логин" name="login">
							<small id="loginHelp" class="form-text text-muted">Данные для входа можно получить у администратора</small>
						</div>
						<div class="form-group">
							<label for="userpass">Пароль</label>
							<input type="password" class="form-control" id="userpass" placeholder="Введите пароль" name="password">
						</div>
						<button type="submit" class="btn btn-primary btn-lg" name="submit">Войти</button>
					</form>

					<?php getUser($pdo, $siteroot); ?>

				</div>
			</div>
		</div>
	</div>
</body>
</html>
		

	

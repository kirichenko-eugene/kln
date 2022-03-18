<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getKln($siteroot, $cssroot)
		{
			$title = 'Добавить КЛН';
			$topInfo = 'Новый КЛН';

			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
			} else {
				$name = '';
			}

			$content .= '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="name">Новый КЛН</label>';
			$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
			$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Создать</button>
						</form>';

			
			include '../elements/layout.php';
		} 

		function addKln($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = $_POST['name'];

				$query = "SELECT COUNT(*) as count FROM klns WHERE name = :name";
				$params = [
					':name' => $name
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$isName = $stmt->fetchColumn();

				if ($isName != 0) {
					$_SESSION['message'] = ['text' => 'КЛН с таким названием уже был добавлен', 
					'status' => 'error'];
				} else {
					$query = "INSERT INTO `klns` (`name`) VALUES (:name)";
					$params = [
						':name' => $name
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'КЛН успешно добавлен', 
					'status' => 'success'];

					header("Location: $siteroot/pages/klns.php");
					die();
				}	

			} else {
				return '';
			}
		}

		addKln($pdo, $siteroot);
		getKln($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


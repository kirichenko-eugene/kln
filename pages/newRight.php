<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRight($siteroot, $cssroot)
		{
			$title = 'Добавить право';
			$topInfo = 'Новое право';

			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
			} else {
				$name = '';
			}

			$content .= '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="name">Новое право</label>';
			$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
			$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Создать</button>
						</form>';

			
			include '../elements/layout.php';
		} 

		function addRight($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = $_POST['name'];

				$query = "SELECT COUNT(*) as count FROM rights WHERE name = :name";
				$params = [
					':name' => $name
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$isRight = $stmt->fetchColumn();

				if ($isRight != 0) {
					$_SESSION['message'] = ['text' => 'Право с таким названием уже было добавлено', 
					'status' => 'error'];
				} else {
					$query = "INSERT INTO `rights` (`name`) VALUES (:name)";
					$params = [
						':name' => $name
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Право успешно добавлено', 
					'status' => 'success'];

					header("Location: $siteroot/pages/rights.php");
					die();
				}	

			} else {
				return '';
			}
		}

		addRight($pdo, $siteroot);
		getRight($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


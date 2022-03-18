<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getKln($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать КЛН';
			$topInfo = 'Редактировать КЛН';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM klns WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$kln = $stmt->fetch();	

				if($kln) {
					if (isset($_POST['name'])) {
						$name = $_POST['name'];
					} else {
						$name = $kln['name'];
					}

					$content = '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="name">Редактировать КЛН</label>';
					$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
					$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Редактировать</button>
						</form>';
	
				} else {
					$content = 'Данный КЛН не найден';
				}		
			} else {
				$content = 'Данный КЛН не найден';
			}

			include '../elements/layout.php';
		}

		function addKln($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = ($_POST['name']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM klns WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$kln = $stmt->fetch();	

					if ($kln['name'] !== $name) {

						$query = "SELECT COUNT(*) as count FROM klns WHERE name = :name";
						$params = [
							':name' => $name
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isName = $stmt->fetchColumn();

						if ($isName !== 0) {
							$_SESSION['message'] = ['text' => 'КЛН с таким названием уже существует', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `klns` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "КЛН '{$kln['name']}' успешно обновлен", 
													'status' => 'success'];

							header("Location: $siteroot/pages/klns.php");
							die();
						}
					} else {
						$query = "UPDATE `klns` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

						$_SESSION['message'] = ['text' => "КЛН '{$kln['name']}' успешно обновлен", 
													'status' => 'success'];

						header("Location: $siteroot/pages/klns.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addKln($pdo, $siteroot);
		getKln($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


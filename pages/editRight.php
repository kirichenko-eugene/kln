<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getRight($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать право';
			$topInfo = 'Редактировать право';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM rights WHERE id = :id LIMIT 1";
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
					<label for="name">Редактировать право</label>';
					$content .=	"<input type=\"text\" class=\"form-control\" id=\"name\" aria-describedby=\"name\" name=\"name\" value=\"$name\" required>";
					$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Редактировать</button>
						</form>';
	
				} else {
					$content = 'Данное право не найдено';
				}		
			} else {
				$content = 'Данное право не найдено';
			}

			include '../elements/layout.php';
		}

		function addRight($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = ($_POST['name']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM rights WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$right = $stmt->fetch();	

					if ($right['name'] !== $name) {

						$query = "SELECT COUNT(*) as count FROM rights WHERE name = :name";
						$params = [
							':name' => $name
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isRight = $stmt->fetchColumn();

						if ($isRight !== 0) {
							$_SESSION['message'] = ['text' => 'Право с таким названием уже существует', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `rights` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "Право '{$right['name']}' успешно обновлено", 
													'status' => 'success'];

							header("Location: $siteroot/pages/rights.php");
							die();
						}
					} else {
						$query = "UPDATE `rights` SET `name` = :name WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

						$_SESSION['message'] = ['text' => "Право '{$right['name']}' успешно обновлено", 
													'status' => 'success'];

						header("Location: $siteroot/pages/rights.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addRight($pdo, $siteroot);
		getRight($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


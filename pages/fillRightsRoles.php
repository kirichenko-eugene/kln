<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRightsTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Назначить право для роли';

			$content = '';

			$query = "SELECT * FROM rights WHERE status = 1";
			$stmt = $pdo->query($query);
			while ($row = $stmt->fetch()) {
					$rights[] = $row;
			}	

			$content .= '<div class="row justify-content-center m-2 col-12">
						<form>
						<div class="form-group">
		   				<label for="selectRight">Выберите право</label>
					    <select class="form-control" id="selectRight" name="selectRight">';
					    	foreach ($rights as $right) {
					    		$content .= "<option value=\"{$right['id']}\">{$right['name']}</option>";
					    	} 
					    $content .= '</select>
						</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto">Выбрать</button>
						</form>
						</div>';


			if (isset($_GET['selectRight'])) {

				$rightId = $_GET['selectRight'];

				$query = "SELECT * FROM rights WHERE id = :id LIMIT 1";
				$params = [
					':id' => $rightId
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$rightName = $stmt->fetch();

				if (!$rightName) {
					$content .= '<div class="row justify-content-center m-2 col-12">Такого права не существует</div>';
				} else {
					$content .= "<div class=\"row justify-content-center m-2 col-12\"><h3>{$rightName['name']}</h3></div>";

					$query = "SELECT * FROM roles WHERE status = 1 ORDER BY role";
					$stmt = $pdo->query($query);
					while ($row = $stmt->fetch()) {
							$roles[] = $row;
					}	

					$content .= '<form method="POST"><table class="table table-striped m-2 table-sm">
								<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>';
					$content .= '<thead><tr>
									<th scope="col">Выбрать</th>
									<th scope="col">Роль</th>
								</tr></thead><tbody>';
					foreach ($roles as $role) {
						
						$content .= "<tr>
										<td><input type=\"checkbox\" class=\"form-check-input d-block mr-auto ml-auto\" name=\"checkRole[]\" value=\"{$role['id']}\" id=\"{$role['id']}\"></td>
										<td><label for=\"{$role['id']}\">{$role['role']}</label></td>
									</tr>";
					}
					$content .= '</tbody></table>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>
					</form>';
				}	
			}
			
			$title = 'Назначить право для роли';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_POST['checkRole'])) {
				$rightId = $_GET['selectRight'];

				foreach($_POST['checkRole'] as $role){

					$query = "SELECT COUNT(*) as count FROM rightsrole WHERE roles = :roles and rights = :rights";
					$params = [
						':roles' => $role, 
						':rights' => $rightId
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$isRole = $stmt->fetchColumn();

					if ($isRole == 0) {
						$query = "INSERT INTO `rightsrole` (`roles`, `rights`) VALUES (:roles, :rights)";
						$params = [
							':roles' => $role,
							':rights' => $rightId
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => 'Права применены к роли!', 
												'status' => 'success'];
					}
				}
			} 
		}
	
		changeStatus($pdo, $siteroot);

		showRightsTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
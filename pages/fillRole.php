<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRoleTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Закрепить пользователя за ролью';

			$content = '';

			$query = "SELECT * FROM roles WHERE status = 1";
			$stmt = $pdo->query($query);
			while ($row = $stmt->fetch()) {
					$roles[] = $row;
			}	

			$content .= '<div class="row justify-content-center m-2 col-12">
						<form>
						<div class="form-group">
		   				<label for="selectRole">Выберите роль</label>
					    <select class="form-control" id="selectRole" name="selectRole">';
					    	foreach ($roles as $role) {
					    		$content .= "<option value=\"{$role['id']}\">{$role['role']}</option>";
					    	} 
					    $content .= '</select>
						</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto">Выбрать</button>
						</form>
						</div>';


			if (isset($_GET['selectRole'])) {

				$roleId = $_GET['selectRole'];

				$query = "SELECT * FROM roles WHERE id = :id LIMIT 1";
				$params = [
					':id' => $roleId
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$roleName = $stmt->fetch();

				if (!$roleName) {
					$content .= '<div class="row justify-content-center m-2 col-12">Такой роли не существует</div>';
				} else {
					$content .= "<div class=\"row justify-content-center m-2 col-12\"><h3>{$roleName['role']}</h3></div>";

					$query = "SELECT * FROM users WHERE status = 1 ORDER BY russian_name";
					$stmt = $pdo->query($query);
					while ($row = $stmt->fetch()) {
							$users[] = $row;
					}	

					$content .= '<form method="POST"><table class="table table-striped m-2 table-sm">
								<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>';
					$content .= '<thead><tr>
									<th scope="col">Выбрать</th>
									<th scope="col">ФИО</th>
								</tr></thead><tbody>';
					foreach ($users as $user) {
						
						$content .= "<tr>
										<td><input type=\"checkbox\" class=\"form-check-input d-block mr-auto ml-auto\" name=\"checkUser[]\" value=\"{$user['id']}\" id=\"{$user['id']}\"></td>
										<td><label for=\"{$user['id']}\">{$user['russian_name']}</label></td>
									</tr>";
					}
					$content .= '</tbody></table>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>
					</form>';
				}	
			}
			
			$title = 'Закрепить пользователя за ролью';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_POST['checkUser'])) {
				$roleId = $_GET['selectRole'];

				foreach($_POST['checkUser'] as $user){

					$query = "SELECT COUNT(*) as count FROM fillrole WHERE user = :user and role = :role";
					$params = [
						':user' => $user, 
						':role' => $roleId
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$isUser = $stmt->fetchColumn();

					if ($isUser == 0) {
						$query = "INSERT INTO `fillrole` (`user`, `role`) VALUES (:user, :role)";
						$params = [
							':role' => $roleId,
							':user' => $user
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => 'Пользователи применены к роли!', 
												'status' => 'success'];
					}
				}
			} 
		}
	
		changeStatus($pdo, $siteroot);

		showRoleTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
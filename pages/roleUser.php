<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRoleTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Просмотр роли';

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

					$query = "SELECT fr.id AS roleid, u.russian_name AS username 
					FROM fillrole fr 
					inner join users u on fr.user = u.id 
					WHERE role = ?";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array($roleId));
					while ($row = $stmt->fetch()) {
							$users[] = $row;
					}	

					$content .= '<table class="table table-striped">';
					$content .= '<thead><tr>
									<th scope="col">Пользователь</th>
									<th scope="col">Удалить</th>
								</tr></thead><tbody>';
					foreach ($users as $user) {
						
						$content .= "<tr>
										<td>{$user['username']}</td>
										<td><a href=\"?changeStatus={$user['roleid']}\">Удалить</a></td>
									</tr>";
					}
					$content .= '</tbody></table>';
				}	
			}
			
			$title = 'Просмотр роли';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_GET['changeStatus'])) {
				$id = $_GET['changeStatus'];

				$query = "DELETE FROM fillrole WHERE `id` = :id";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);

				$_SESSION['message'] = ['text' => 'Роль пользователя была удалена', 
				'status' => 'success'];
			} 
		}
	
		changeStatus($pdo, $siteroot);

		showRoleTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
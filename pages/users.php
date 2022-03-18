<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showLoginTable($pdo, $page, $count, $from, $numOfPages, $siteroot, $cssroot)
		{
			$topInfo = 'Пользователи';

			$pagesCount = ceil($count / $numOfPages);
			$prev = $page - 1;
			$next = $page + 1;

			$content = '';

			$content .= '<div class="row justify-content-center m-2">
							<nav>
				  			<ul class="pagination">';
			if($page == 1) {
				  	$disClass = ' disabled';
			} else {
				  	$disClass = '';
			}
			$content .= "<li class=\"page-item$disClass\">
							<a href=\"?page=$prev\" aria-label=\"Previous\" class=\"page-link\">
							&laquo;
							</a>
							</li>";

			for ($i = 1; $i <= $pagesCount; $i++) {
				if($page == $i) { 
					$class = ' active';
				} else {
					$class = '';
				}
				$content .= "<li class=\"page-link page-item$class\"><a href=\"?page=$i\">$i</a></li>";
			} 
					
			if($page == $pagesCount) {
				$disClass = ' disabled';
			} else {
				$disClass = '';
			}
							
			$content .= "<li class=\"page-item$disClass\">
							<a href=\"?page=$next\" aria-label=\"Next\" class=\"page-link\">
							&raquo;
							</a>
							</li>
				  			</ul>
							</nav>
							</div>";

			$query = "SELECT id, login, russian_name, telegram, usersign, status FROM users LIMIT ?, ?";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array($from, $numOfPages));
			while ($row = $stmt->fetch()) {
					$users[] = $row;
			}	

			$content .= '<table class="table table-striped">';
			$content .= '<thead><tr>
							<th scope="col">Логин</th>
							<th scope="col">ФИО</th>
							<th scope="col">Контакты</th>
							<th scope="col">Админ</th>
							<th scope="col">Статус</th>
							<th scope="col">Редактировать</th>
							<th scope="col">Пароль</th>
							<th scope="col">Права админа</th>
							<th scope="col">Удалить/восстановить</th>
						</tr></thead><tbody>';
			foreach ($users as $user) {
				if ($user['status'] == 1) {
					$textStatus = 'Активен';
				} else {
					$textStatus = 'Удален';
				}

				if ($user['usersign'] == 1) {
					$textSign = 'Админ';
				} else {
					$textSign = 'Обычный';
				}

				$content .= "<tr>
								<td>{$user['login']}</td>
								<td>{$user['russian_name']}</td>
								<td>{$user['telegram']}</td>
								<td>$textSign</td>
								<td>$textStatus</td>
								<td><a href=\"editUser.php?id={$user['id']}\">Редактировать</a></td>
								<td><a href=\"editUserPassword.php?id={$user['id']}\">Сменить</a></td>
								<td><a href=\"?changeSign={$user['id']}&status={$user['usersign']}\">Выключить/Включить</a></td>
								<td><a href=\"?changeStatus={$user['id']}&status={$user['status']}\">Удалить/восстановить</a></td>
							</tr>";
			}
			$content .= '</tbody></table>';
			
			$title = 'Пользователи';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_GET['changeStatus'])) {
				$id = $_GET['changeStatus'];
				$status = $_GET['status'];
				if($status == 1) {
					$newstatus = 0;

					$query = "UPDATE `users` SET `status` = :status WHERE `id` = :id";
					$params = [
						':id' => $id,
						':status' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Пользователь был отключен!', 
											'status' => 'success'];
				} elseif ($status == 0) {
					$newstatus = 1;

					$query = "UPDATE `users` SET `status` = :status WHERE `id` = :id";
					$params = [
						':id' => $id,
						':status' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Пользователь снова активен!', 
											'status' => 'success'];

				}
				
			}
		}

		function changeAdmin($pdo, $siteroot)
		{
			if (isset($_GET['changeSign'])) {
				$id = $_GET['changeSign'];
				$status = $_GET['status'];
				if($status == 1) {
					$newstatus = 0;

					$query = "UPDATE `users` SET `usersign` = :usersign WHERE `id` = :id";
					$params = [
						':id' => $id,
						':usersign' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Пользователю назначены обычные права!', 
											'status' => 'success'];

				} elseif ($status == 0) {
					$newstatus = 1;

					$query = "UPDATE `users` SET `usersign` = :usersign WHERE `id` = :id";
					$params = [
						':id' => $id,
						':usersign' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Пользователю назначены права админа!', 
											'status' => 'success'];
				}
				
			}
		}

		if (isset($_GET['page'])) {
			if($_GET['page'] > 0 OR is_int($_GET['page']) == true) {
				$page = htmlspecialchars($_GET['page']);
			} else {
				$page = 1;
			}
		} else {
			$page = 1;
		}

		$numOfPages = 12;
		$from = ($page - 1) * $numOfPages;

		// для пагинации
		$query = "SELECT COUNT(*) as count FROM users";
		$stmt = $pdo->query($query);
		$count = $stmt->fetchColumn();

		changeStatus($pdo, $siteroot);

		changeAdmin($pdo, $siteroot);

		showLoginTable($pdo, $page, $count, $from, $numOfPages, $siteroot, $cssroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
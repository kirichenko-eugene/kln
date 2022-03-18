<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showKlnTable($pdo, $page, $count, $from, $numOfPages, $cssroot, $siteroot)
		{
			$topInfo = 'Список КЛН';

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

			$query = "SELECT * FROM klns LIMIT ?, ?";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array($from, $numOfPages));
			while ($row = $stmt->fetch()) {
					$klns[] = $row;
			}	

			$content .= '<table class="table table-striped">';
			$content .= '<thead><tr>
							<th scope="col">Название</th>
							<th scope="col">Статус</th>
							<th scope="col">Редактировать</th>
							<th scope="col">Удалить/восстановить</th>
						</tr></thead><tbody>';
			foreach ($klns as $kln) {
				if ($kln['status'] == 1) {
					$textStatus = 'Активен';
				} else {
					$textStatus = 'Удален';
				}

				$content .= "<tr>
								<td>{$kln['name']}</td>
								<td>$textStatus</td>
								<td><a href=\"editKln.php?id={$kln['id']}\">Редактировать</a></td>
								<td><a href=\"?changeStatus={$kln['id']}&status={$kln['status']}\">Удалить/восстановить</a></td>
							</tr>";
			}
			$content .= '</tbody></table>';
			
			$title = 'КЛНы';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_GET['changeStatus'])) {
				$id = $_GET['changeStatus'];
				$status = $_GET['status'];
				if($status == 1) {
					$newstatus = 0;

					$query = "UPDATE `klns` SET `status` = :status WHERE `id` = :id";
					$params = [
						':id' => $id,
						':status' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Клн был отключен!', 
											'status' => 'success'];

				} elseif ($status == 0) {
					$newstatus = 1;

					$query = "UPDATE `klns` SET `status` = :status WHERE `id` = :id";
					$params = [
						':id' => $id,
						':status' => $newstatus
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Клн снова активен!', 
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

		$numOfPages = 10;
		$from = ($page - 1) * $numOfPages;

		// для пагинации
		$query = "SELECT COUNT(*) as count FROM klns";
		$stmt = $pdo->query($query);
		$count = $stmt->fetchColumn();

		changeStatus($pdo, $siteroot);

		showKlnTable($pdo, $page, $count, $from, $numOfPages, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRightTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Просмотр прав ресторанов';

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

					$query = "SELECT rrest.id AS rightrestaurantId, r.name AS restaurant 
					FROM rightsrestaurant rrest 
					inner join restaurants r on rrest.restaurants = r.id 
					WHERE rights = ?";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array($rightId));
					while ($row = $stmt->fetch()) {
							$restaurants[] = $row;
					}	

					$content .= '<table class="table table-striped">';
					$content .= '<thead><tr>
									<th scope="col">Ресторан</th>
									<th scope="col">Удалить</th>
								</tr></thead><tbody>';
					foreach ($restaurants as $restaurant) {
						
						$content .= "<tr>
										<td>{$restaurant['restaurant']}</td>
										<td><a href=\"?changeStatus={$restaurant['rightrestaurantId']}\">Удалить</a></td>
									</tr>";
					}
					$content .= '</tbody></table>';
				}	
			}
			
			$title = 'Просмотр прав ресторанов';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_GET['changeStatus'])) {
				$id = $_GET['changeStatus'];

				$query = "DELETE FROM rightsrestaurant WHERE `id` = :id";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);

				$_SESSION['message'] = ['text' => 'Право ресторана было удалено', 
				'status' => 'success'];
			} 
		}
	
		changeStatus($pdo, $siteroot);

		showRightTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
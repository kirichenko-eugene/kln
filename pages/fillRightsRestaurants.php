<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRightsTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Назначить право для ресторана';

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

					$query = "SELECT * FROM restaurants WHERE status = 1 ORDER BY name";
					$stmt = $pdo->query($query);
					while ($row = $stmt->fetch()) {
							$restaurants[] = $row;
					}	

					$content .= '<form method="POST"><table class="table table-striped m-2 table-sm">
								<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>';
					$content .= '<thead><tr>
									<th scope="col">Выбрать</th>
									<th scope="col">Ресторан</th>
								</tr></thead><tbody>';
					foreach ($restaurants as $restaurant) {
						
						$content .= "<tr>
										<td><input type=\"checkbox\" class=\"form-check-input d-block mr-auto ml-auto\" name=\"checkRestaurant[]\" value=\"{$restaurant['id']}\" id=\"{$restaurant['id']}\"></td>
										<td><label for=\"{$restaurant['id']}\">{$restaurant['name']}</label></td>
									</tr>";
					}
					$content .= '</tbody></table>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>
					</form>';
				}	
			}
			
			$title = 'Назначить право для ресторана';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_POST['checkRestaurant'])) {
				$rightId = $_GET['selectRight'];

				foreach($_POST['checkRestaurant'] as $restaurant){

					$query = "SELECT COUNT(*) as count FROM rightsrestaurant WHERE restaurants = :restaurants and rights = :rights";
					$params = [
						':restaurants' => $restaurant, 
						':rights' => $rightId
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$isRestaurant = $stmt->fetchColumn();

					if ($isRestaurant == 0) {
						$query = "INSERT INTO `rightsrestaurant` (`restaurants`, `rights`) VALUES (:restaurants, :rights)";
						$params = [
							':restaurants' => $restaurant,
							':rights' => $rightId
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => 'Права применены к ресторану!', 
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
	
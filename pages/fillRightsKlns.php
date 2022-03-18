<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showRightsTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Назначить право для КЛН';

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

					$query = "SELECT * FROM klns WHERE status = 1 ORDER BY name";
					$stmt = $pdo->query($query);
					while ($row = $stmt->fetch()) {
							$klns[] = $row;
					}	

					$content .= '<form method="POST"><table class="table table-striped m-2 table-sm">
								<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>';
					$content .= '<thead><tr>
									<th scope="col">Выбрать</th>
									<th scope="col">КЛН</th>
								</tr></thead><tbody>';
					foreach ($klns as $kln) {
						
						$content .= "<tr>
										<td><input type=\"checkbox\" class=\"form-check-input d-block mr-auto ml-auto\" name=\"checkKln[]\" value=\"{$kln['id']}\" id=\"{$kln['id']}\"></td>
										<td><label for=\"{$kln['id']}\">{$kln['name']}</label></td>
									</tr>";
					}
					$content .= '</tbody></table>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>
					</form>';
				}	
			}
			
			$title = 'Назначить право для КЛН';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_POST['checkKln'])) {
				$rightId = $_GET['selectRight'];

				foreach($_POST['checkKln'] as $kln){

					$query = "SELECT COUNT(*) as count FROM rightskln WHERE klns = :klns and rights = :rights";
					$params = [
						':klns' => $kln, 
						':rights' => $rightId
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$isKln = $stmt->fetchColumn();

					if ($isKln == 0) {
						$query = "INSERT INTO `rightskln` (`klns`, `rights`) VALUES (:klns, :rights)";
						$params = [
							':klns' => $kln,
							':rights' => $rightId
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);

						$_SESSION['message'] = ['text' => 'Права применены к КЛН!', 
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
	
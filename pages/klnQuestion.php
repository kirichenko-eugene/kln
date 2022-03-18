<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showKlnTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Просмотр КЛН';

			$content = '';

			$query = "SELECT * FROM klns WHERE status = 1";
			$stmt = $pdo->query($query);
			while ($row = $stmt->fetch()) {
					$klns[] = $row;
			}	

			$content .= '<div class="row justify-content-center m-2 col-12">
						<form>
						<div class="form-group">
		   				<label for="selectKln">Выберите КЛН</label>
					    <select class="form-control" id="selectKln" name="selectKln">';
					    	foreach ($klns as $kln) {
					    		$content .= "<option value=\"{$kln['id']}\">{$kln['name']}</option>";
					    	} 
					    $content .= '</select>
						</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto">Выбрать</button>
						</form>
						</div>';


			if (isset($_GET['selectKln'])) {

				$klnId = $_GET['selectKln'];

				$query = "SELECT * FROM klns WHERE id = :id LIMIT 1";
				$params = [
					':id' => $klnId
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$klnName = $stmt->fetch();

				if (!$klnName) {
					$content .= '<div class="row justify-content-center m-2 col-12">Такого КЛН не существует</div>';
				} else {
					$content .= "<div class=\"row justify-content-center m-2 col-12\"><h3>{$klnName['name']}</h3></div>";

					$query = "SELECT fk.id AS klnid, q.name AS name 
					FROM fillkln fk 
					inner join questions q on fk.question = q.id 
					WHERE kln = ?";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array($klnId));
					while ($row = $stmt->fetch()) {
							$questions[] = $row;
					}	

					$content .= '<table class="table table-striped">';
					$content .= '<thead><tr>
									<th scope="col">Вопрос</th>
									<th scope="col">Удалить</th>
								</tr></thead><tbody>';
					foreach ($questions as $question) {
						
						$content .= "<tr>
										<td>{$question['name']}</td>
										<td><a href=\"?changeStatus={$question['klnid']}\">Удалить</a></td>
									</tr>";
					}
					$content .= '</tbody></table>';
				}	
			}
			
			$title = 'Просмотр КЛН';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_GET['changeStatus'])) {
				$id = $_GET['changeStatus'];

				$query = "DELETE FROM fillkln WHERE `id` = :id";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);

				$_SESSION['message'] = ['text' => 'Вопрос был удален из КЛНа!', 
				'status' => 'success'];
			} 
		}
	
		changeStatus($pdo, $siteroot);

		showKlnTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
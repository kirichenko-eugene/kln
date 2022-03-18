<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function showKlnTable($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Добавить вопросы к КЛН';

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
				
					$query = "SELECT * FROM questions WHERE status = 1";
					$stmt = $pdo->query($query);
					while ($row = $stmt->fetch()) {
							$questions[] = $row;
					}	

					$content .= '<form method="POST"><table class="table table-striped m-2">';
					$content .= '<thead><tr>
									<th scope="col">Выбрать</th>
									<th scope="col">Вопрос</th>
									<th scope="col">Оценка</th>
								</tr></thead><tbody>';
					foreach ($questions as $question) {
						
						$content .= "<tr>
										<td><input type=\"checkbox\" class=\"form-check-input d-block mr-auto ml-auto\" name=\"checkQuestion[]\" value=\"{$question['id']}\"></td>
										<td>{$question['name']}</td>
										<td class=\"text-center\">{$question['mark']}</td>
									</tr>";
					}
					$content .= '</tbody></table>
					<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Добавить</button>
					</form>';
				}	
			}
			
			$title = 'Добавить вопросы к КЛН';

			include '../elements/layout.php';
		}

		function changeStatus($pdo, $siteroot)
		{
			if (isset($_POST['checkQuestion'])) {
				$klnId = $_GET['selectKln'];

				foreach($_POST['checkQuestion'] as $question){
					
					$query = "SELECT COUNT(*) as count FROM fillkln WHERE kln = :kln and question = :question";
					$params = [
						':kln' => $klnId, 
						':question' => $question
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$isQuestion = $stmt->fetchColumn();

					if ($isQuestion == 0) {

						$query = "INSERT INTO `fillkln` (`kln`, `question`) VALUES (:kln, :question)";
						$params = [
							':kln' => $klnId,
							':question' => $question
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$_SESSION['message'] = ['text' => 'Вопросы добавлены к КЛНу!', 
												'status' => 'success'];
					}
				}
				
				
			} 
		}

		changeStatus($pdo, $siteroot);

		showKlnTable($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
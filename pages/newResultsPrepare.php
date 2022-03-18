<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'])) {

		function showFormFields($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Редактировать результаты';

			if(isset($_POST['submit']) and isset($_POST['dateStart']) and isset($_POST['dateEnd']) and isset($_POST['restaurant']) and isset($_POST['user'])) {
				$dateStart = $_POST['dateStart'] . ' 00:00:00';
				$dateEnd = $_POST['dateEnd'] . ' 23:59:59';
				$user = $_POST['user'];
				$restaurant = $_POST['restaurant'];
				$resultAllFilter = [];

				$query = "SELECT r.id, date_format(r.time,'%d.%m.%Y %H:%i:%s') AS time, u.russian_name AS russianname, rest.name AS restaurantname, k.name AS klnName, q.name AS questionName, r.mark
				FROM results r 
				inner join users u on r.user = u.id 
				inner join restaurants rest on r.restaurant = rest.id 
				inner join klns k on r.kln = k.id 
				inner join questions q on r.question = q.id";
				$query = $query . " WHERE r.time >= :startDate 
			    AND r.time <= :endDate 
			    AND r.user = :user 
			    AND r.restaurant = :restaurant";

			    $params = [
							':startDate' => $dateStart,
							':endDate' => $dateEnd,
							':restaurant' => $restaurant,
							':user' => $user
						];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);

				while ($row = $stmt->fetch()) {
					$resultAllFilter[] = $row;
				}	

				$content = '';

				$content .= '<form method="POST">';
				$content .= '<table class="table table-striped">';
				$content .= '<thead><tr>
								<th scope="col">Выбрать</th>
								<th scope="col">Время</th>
								<th scope="col">Пользователь</th>
								<th scope="col">Ресторан</th>
								<th scope="col">КЛН</th>
								<th scope="col">Вопрос</th>
								<th scope="col">Оценка</th>
							</tr></thead><tbody>';
				foreach ($resultAllFilter as $result) {
					$content .= "<tr>
								<td class=\"text-center\"><div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input\" value=\"{$result['id']}\" name=\"update[]\"></div></td>
								<td>{$result['time']}</td>
								<td>{$result['russianname']}</td>
								<td>{$result['restaurantname']}</td>
								<td>{$result['klnName']}</td>
								<td>{$result['questionName']}</td>
								<td>";
									if($result['mark'] == 1) { 
						              $content .= '<div class="text-danger">Да</div>';
						            } elseif ($result['mark'] == 0) {
						              $content .= '<div class="text-danger">Нет</div>';
						            }
						            $content .=	"<select class=\"form-control\" name=\"mark_{$result['id']}\">";
									$content .=	'<option value="9">Положительный результат?</option>
									            <option value="0">Нет</option>
									            <option value="1">Да</option> 
												</select>
												</td>
												</tr>';
				}

				$content .= '</tbody></table>';
				$content .= '<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Изменить данные</button>';	
				$content .= '</form>';
			}


			$title = 'Редактировать результаты';

			include '../elements/layout.php';
		}

		function changeResults($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				if(isset($_POST['update'])) { 
					foreach($_POST['update'] as $updateid){
						$mark = $_POST['mark_'.$updateid];

						if($mark != 9){
							$query = "UPDATE `results` SET `mark` = :mark WHERE `id` = :id";
							$params = [
								':id' => $updateid,
								':mark' => $mark
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);
						
						}
					}
					$_SESSION['message'] = ['text' => 'Выбранные ответы были обновлены', 
												'status' => 'success'];
					header("Location: $siteroot/pages/newResults.php");
					die();
				}	
			}
		}
				
	
		changeResults($pdo, $siteroot);

		showFormFields($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
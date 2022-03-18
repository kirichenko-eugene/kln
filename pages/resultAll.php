<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'])) {

		function showFormFields($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Отчет по пользователям';

			$content = '';
			$content .= '<form method="POST" class="col-xl-8">
					<div class="form-group">
					<label for="dateStart">Выберите дату начала периода для формирования отчета</label>';
			$content .=	"<input name=\"dateStart\" type=\"date\" class=\"form-control\" id=\"dateStart\" aria-describedby=\"dateStart\" required>";
			$content .=	'</div>';
			$content .=	'<div class="form-group">
						<label for="dateEnd">Выберите конец периода для формирования отчета</label>';
			$content .=	"<input name=\"dateEnd\" type=\"date\" class=\"form-control\" id=\"dateEnd\" aria-describedby=\"dateEnd\" required>";
			$content .=	'</div>
						<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Выгрузить в xls</button>
						</form>';
			
			$title = 'Отчет по пользователям в xls';

			include '../elements/layout.php';
		}

		function toExcel($pdo)
		{
			if (isset($_POST['dateStart']) and isset($_POST['dateEnd'])) {

				date_default_timezone_set('Europe/Moscow');

				$dateStart = $_POST['dateStart'] . ' 00:00:00';
				$dateEnd = $_POST['dateEnd'] . ' 23:59:59';

				if ($_SESSION['user']['superuser'] == 1) {
					$query = "SELECT ANY_VALUE(r.id), a.time AS time, ANY_VALUE(u.login) AS user, ANY_VALUE(us.login) AS manager, ANY_VALUE(u.russian_name) AS russianname, ANY_VALUE(us.russian_name) AS manager_rus, ANY_VALUE(rest.name) AS restaurantname, ANY_VALUE(k.name) AS kln_name, ANY_VALUE(q.name), 
					sum(CASE 
						WHEN r.mark = 0 THEN 0
						WHEN r.mark = 1 THEN q.mark
					END) mark, 
					sum(q.mark) AS max_value 
					FROM results r 
					inner join users u on r.user = u.id 
					inner join users us on r.manager = us.id 
					inner join restaurants rest on r.restaurant = rest.id 
					inner join attempts a on r.attempt = a.id 
					inner join klns k on r.kln = k.id 
					inner join questions q on r.question = q.id";
					$query = $query . " WHERE a.time >= ? AND a.time <= ? GROUP BY r.attempt";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array($dateStart, $dateEnd));
					while ($row = $stmt->fetch()) {
						$resultAll[] = $row;
					}	
				} else {
					$userRights = $_SESSION['userRights'];
					$in  = str_repeat('?,', count($userRights) - 1) . '?';
					$query = "SELECT klns FROM rightskln WHERE rights IN ($in)";
					$stmt = $pdo->prepare($query);
					$stmt->execute($userRights);
					$klnsId = $stmt->fetchAll();


					foreach($klnsId as $klnId) {
						$allKlnsId[] = $klnId['klns'];
					}

					$allKlnsId = array_unique(array_values($allKlnsId));
					$inKln  = str_repeat('?,', count($allKlnsId) - 1) . '?';

					$query = "SELECT ANY_VALUE(r.id), a.time AS time, ANY_VALUE(u.login) AS user, ANY_VALUE(us.login) AS manager, ANY_VALUE(u.russian_name) AS russianname, ANY_VALUE(us.russian_name) AS manager_rus, ANY_VALUE(rest.name) AS restaurantname, ANY_VALUE(k.name) AS kln_name, ANY_VALUE(q.name), 
						sum(CASE 
							WHEN r.mark = 0 THEN 0
							WHEN r.mark = 1 THEN q.mark
						END) mark, 
						sum(q.mark) AS max_value 
					FROM results r 
					inner join users u on r.user = u.id 
					inner join users us on r.manager = us.id 
					inner join restaurants rest on r.restaurant = rest.id 
					inner join attempts a on r.attempt = a.id 
					inner join klns k on r.kln = k.id 
					inner join questions q on r.question = q.id";
					$query = $query . " WHERE a.time >= ? AND a.time <= ? AND kln IN ($inKln) GROUP BY r.attempt";
					$stmt = $pdo->prepare($query);
					$params = array_merge([$dateStart, $dateEnd], $allKlnsId);
					$stmt->execute($params);
					while ($row = $stmt->fetch()) {
						$resultAll[] = $row;
					}	
				}
					

				/** PHPExcel */
				require_once '../PHPExcel/Classes/PHPExcel.php';

				$phpexcel = new PHPExcel(); 
				$phpexcel->setActiveSheetIndex(0); 
				$page = $phpexcel->getActiveSheet(); 
				$page->setTitle("Results_all"); 

				$page->getColumnDimension("A")->setAutoSize(true);
				$page->getColumnDimension("B")->setAutoSize(true);
				$page->getColumnDimension("C")->setAutoSize(true);
				$page->getColumnDimension("D")->setAutoSize(true);
				$page->getColumnDimension("E")->setAutoSize(true);
				$page->getColumnDimension("F")->setAutoSize(true);

				$page->setCellValue("A1", "Пользователь"); 
				$page->setCellValue("B1", "Менеджер");
				$page->setCellValue("C1", "Время");
				$page->setCellValue("D1", "Ресторан");
				$page->setCellValue("E1", "КЛН");    
				$page->setCellValue("F1", "Оценка, %");  

				$s = 1;
				
				foreach($resultAll as $result)
				{
					$s++;
					$page->setCellValue("A$s", $result['russianname']); 
					$page->setCellValue("B$s", $result['manager_rus']);
					$page->setCellValue("C$s", $result['time']);
					$page->setCellValue("D$s", $result['restaurantname']);    
					$page->setCellValue("E$s", $result['kln_name']);
					$page->setCellValue("F$s", round(($result['mark'] * 100) / $result['max_value'], 2));

				} 

				// Redirect output to a client’s web browser (Excel2007)
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Cache-Control: max-age=0');
				header('Content-Disposition: attachment;filename="ResultsAll.xlsx"');
				$objWriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel2007');
				$objWriter->save('php://output');
				exit;
			}
		}
	
		toExcel($pdo);

		showFormFields($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
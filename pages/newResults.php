<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'])) {

		function showFormFields($pdo, $cssroot, $siteroot)
		{
			$topInfo = 'Редактировать результаты';

			$content = '';
			$content .= "<form method=\"POST\" action=\"$siteroot/pages/newResultsPrepare.php\" class=\"col-xl-8\">";
			$content .= '<div class="form-group">
			<label for="dateStart">Выберите дату начала периода для редактирования</label>';
			$content .=	"<input name=\"dateStart\" type=\"date\" class=\"form-control\" id=\"dateStart\" aria-describedby=\"dateStart\" required>";
			$content .=	'</div>';
			$content .=	'<div class="form-group">
			<label for="dateEnd">Выберите конец периода для редактирования</label>';
			$content .=	"<input name=\"dateEnd\" type=\"date\" class=\"form-control\" id=\"dateEnd\" aria-describedby=\"dateEnd\" required>";
			$content .=	'</div>';

			// РЕСТОРАН ************************************************
			$content .= '<div class="form-group">
			<label for="restaurant">Выберите ресторан</label>
			<select class="form-control" id="restaurant" name="restaurant" size="4" required>';

			$query = "SELECT * FROM restaurants ORDER BY name";
			$stmt = $pdo->query($query);
			while ($row = $stmt->fetch()) {
				$restaurants[] = $row;
			}

			if($restaurants){
				foreach($restaurants as $restaurant){
					$idRestaurant = $restaurant['id'];
					$nameRestaurant = $restaurant['name'];
					$content .= "<option value=\"$idRestaurant\" >$nameRestaurant</option>";
				}
			}	

			$content .= '</select>
			</div>';

			// ПОЛЬЗОВАТЕЛИ ************************************************
			$content .= '<div class="form-group">
			<label for="user">Выберите пользователя</label>
			<select class="form-control" id="user" name="user" size="10" required>';

			$query = "SELECT * FROM users ORDER BY russian_name";
			$stmt = $pdo->query($query);
			while ($row = $stmt->fetch()) {
				$users[] = $row;
			}

			if($users){
				foreach($users as $user){
					$idUser = $user['id'];
					$userName = $user['russian_name'];
					if($user['status'] == 1) {
						$content .= "<option value=\"$idUser\" >$userName</option>";
					} else {
						$content .= "<option class=\"text-danger\" value=\"$idUser\" >$userName</option>";
					}
					
				}
			}	

			$content .= '</select>
			</div>
			<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Далее</button>
			</form>';
			
			$title = 'Редактировать результаты';

			include '../elements/layout.php';
		}

	
		showFormFields($pdo, $cssroot, $siteroot);

	} else { 
		header("Location: $loginroot");
		die();
	} 
	
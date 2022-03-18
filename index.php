<?php
	include 'config/config.php';
	include 'config/roots.php';

	if (!empty($_SESSION['auth'])) {
		$title = 'GoodCity КЛН';
		$topInfo = $_SESSION['user']['russian'];
		$content = '';

		// права для пользователя, если он не администратор
		$userId = $_SESSION['user']['id'];
		
		$query = "SELECT * FROM fillrole WHERE user = :user";
		$params = [
					':user' => $userId
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
			while ($row = $stmt->fetch()) {
				$roles[] = $row;
			}	

		foreach ($roles as $role) {
			$roleId = $role['role'];
			$query = "SELECT * FROM rightsrole WHERE roles = :roles";
			$params = [
					':roles' => $roleId
				];
			$stmt = $pdo->prepare($query);
			$stmt->execute($params);
			while ($row = $stmt->fetch()) {
				$rights[] = $row;
			}	
		}

		$rightsArray = [];
		$rightsArrayUnique = [];

		foreach($rights as $right) {
			$rightsArray[] = $right['rights'];
		}

		$rightsArrayUnique = array_unique($rightsArray);

		$_SESSION['userRights'] = array_values($rightsArrayUnique);
		//***********************************************

		include 'elements/layout.php';
	} else { 
		header("Location: $loginroot");
		die();
	} 

?>


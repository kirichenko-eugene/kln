<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getQuestion($siteroot, $cssroot)
		{
			$title = 'Добавить вопрос';
			$topInfo = 'Новый вопрос';

			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
				$mark = $_POST['mark'];
			} else {
				$name = '';
				$mark = '';
			}

			ob_start();
			include '../elements/questionForm.php';
			$content = ob_get_clean();
			
			include '../elements/layout.php';
		} 

		function addQuestion($pdo, $siteroot)
		{
			if (isset($_POST['submit'])) {
				$name = $_POST['name'];
				$mark = $_POST['mark'];

				$query = "SELECT COUNT(*) as count FROM questions WHERE name = :name";
				$params = [
					':name' => $name
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$isName = $stmt->fetchColumn();

				if ($isName != 0) {
					$_SESSION['message'] = ['text' => 'Такой вопрос уже существует', 
					'status' => 'error'];
				} else {
					$query = "INSERT INTO `questions` (`name`, `mark`) 
					VALUES (:name, :mark)";
					$params = [
						':name' => $name,
						':mark' => $mark
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);

					$_SESSION['message'] = ['text' => 'Вопрос успешно добавлен', 
					'status' => 'success'];

					header("Location: $siteroot/pages/questions.php");
					die();
				}	

			} else {
				return '';
			}
		}

		addQuestion($pdo, $siteroot);
		getQuestion($siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


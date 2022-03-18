<?php
	include '../config/config.php';
	include '../config/roots.php';

	if (!empty($_SESSION['auth'] and $_SESSION['user']['superuser'] == 1)) {

		function getQuestion($pdo, $siteroot, $cssroot)
		{
			$title = 'Редактировать вопрос';
			$topInfo = 'Редактировать вопрос';

			if (isset($_GET['id'])) {
				$id = $_GET['id'];

				$query = "SELECT * FROM questions WHERE id = :id LIMIT 1";
				$params = [
					':id' => $id
				];
				$stmt = $pdo->prepare($query);
				$stmt->execute($params);
				$question = $stmt->fetch();	

				if($question) {
					if (isset($_POST['name']) and isset($_POST['mark'])) {
						$name = $_POST['name'];
						$mark = $_POST['mark'];
					} else {
						$name = $question['name'];
						$mark = $question['mark'];
					}

					ob_start();
					include '../elements/questionForm.php';
					$content = ob_get_clean();
					
				} else {
					$content = 'Данный вопрос не найден';
				}		
			} else {
				$content = 'Данный вопрос не найден';
			}

			include '../elements/layout.php';
		}

		function addQuestion($pdo, $siteroot)
		{
			if (isset($_POST['name']) and isset($_POST['mark'])) {
				$name = ($_POST['name']);
				$mark = ($_POST['mark']);
				
				if (isset($_GET['id'])) {
					$id = $_GET['id'];

					$query = "SELECT * FROM questions WHERE id = :id LIMIT 1";
					$params = [
						':id' => $id
					];
					$stmt = $pdo->prepare($query);
					$stmt->execute($params);
					$question = $stmt->fetch();	

					if ($question['name'] !== $name) {

						$query = "SELECT COUNT(*) as count FROM questions WHERE name = :name";
						$params = [
							':name' => $name
						];
						$stmt = $pdo->prepare($query);
						$stmt->execute($params);
						$isName = $stmt->fetchColumn();

						if ($isName !== 0) {
							$_SESSION['message'] = ['text' => 'Такой вопрос уже есть в списке', 
													'status' => 'error'];
						} else {
							$query = "UPDATE `questions` SET `name` = :name, `mark` = :mark WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name,
								':mark' => $mark
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

							$_SESSION['message'] = ['text' => "Вопрос '{$question['name']}' успешно обновлен", 
													'status' => 'success'];

							header("Location: $siteroot/pages/questions.php");
							die();
						}
					} else {
						$query = "UPDATE `questions` SET `name` = :name, `mark` = :mark WHERE `id` = :id";
							$params = [
								':id' => $id,
								':name' => $name,
								':mark' => $mark
							];
							$stmt = $pdo->prepare($query);
							$stmt->execute($params);

						$_SESSION['message'] = ['text' => "Вопрос '{$question['name']}' успешно обновлен", 
													'status' => 'success'];

						header("Location: $siteroot/pages/questions.php");
						die();
					}
				}	

			} else {
				return '';
			}
		}

		addQuestion($pdo, $siteroot);
		getQuestion($pdo, $siteroot, $cssroot); 
	} else {
		header("Location: $loginroot");
		die();
	}


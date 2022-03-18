<form method="POST">
	<div class="form-group">
		<label for="login">Логин</label>
		<input type="text" class="form-control" id="login" aria-describedby="login" value="<?=$login?>" name="login" required>
	</div>

	<div class="form-group">
		<label for="name">ФИО</label>
		<input type="text" class="form-control" id="name" aria-describedby="name" value="<?=$name?>" name="name" required>
	</div>

	<div class="form-group">
		<label for="name">Телеграм</label>
		<input type="text" class="form-control" id="telegram" aria-describedby="telegram" value="<?=$telegram?>" name="telegram">
	</div>

	<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Редактировать</button>
</form>
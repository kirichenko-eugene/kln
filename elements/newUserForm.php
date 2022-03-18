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

	<div class="form-group">
		<label for="name">Пароль</label>
		<input type="password" class="form-control" id="password" aria-describedby="password" name="password">
	</div>

	<div class="form-group form-check">
    <input type="checkbox" class="form-check-input" id="adminCheck" name="adminCheck">
    <label class="form-check-label" for="adminCheck">Права администратора</label>
  </div>

	<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Создать</button>
</form>
<form method="POST" class="col-xl-8">
	<div class="form-group">
    	<label for="name">Вопрос</label>
    	<textarea class="form-control" id="name" name="name" rows="4" required><?=$name?></textarea>
  	</div>

	<div class="form-group">
		<label for="mark">Оценка</label>
		<input type="number" class="form-control" id="mark" aria-describedby="mark" value="<?=$mark?>" name="mark" required placeholder="Числовая оценка">
	</div>

	<button type="submit" class="btn btn-primary d-block mr-auto ml-auto" name="submit">Применить</button>
</form>
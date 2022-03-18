<div class="row justify-content-center m-2">
	<div class="row col-xl-12 col-l-12 m-3 justify-content-center">
		<div class="col text-center">
			<a class="btn btn-secondary btn-lg" href="<?=$siteroot?>/pages/logout.php" role="button">Выйти</a>
		</div>
		<div class="col text-center">
			<h1><a class="text-decoration-none" href="<?=$siteroot?>">GoodCity <span class="badge badge-warning">КЛН</span></a></h1>
		</div>
		<div class="col text-center">
			<h1><span class="badge badge-primary"><?=$topInfo?></span></h1>
		</div>
	</div>
	<div class="dropdown m-2">
	  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    Отчеты
	  </button>
	  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
	  	<?php if ($_SESSION['user']['superuser'] == 1) { ?>
	  		<a class="dropdown-item" href="<?=$siteroot?>/pages/newResults.php">Редактировать результаты КЛН</a>
	  	<?php } ?>
	    <a class="dropdown-item" href="<?=$siteroot?>/pages/resultAll.php">Отчет по всем пользователям за период</a>
	    <?php if ($_SESSION['user']['superuser'] == 1) { ?>
	    	<a class="dropdown-item" href="<?=$siteroot?>/pages/resultFilter.php">Отчет с фильтром за период</a>
	    <?php } ?>
	  </div>
	</div>
	<?php if ($_SESSION['user']['superuser'] == 1) { ?>
	
		<div class="dropdown m-2">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Пользователи и рестораны
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		  	<a class="dropdown-item" href="<?=$siteroot?>/pages/newUser.php">Создать пользователя</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/users.php">Все пользователи</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/newRestaurant.php">Создать ресторан</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/restaurants.php">Все рестораны</a>
		  </div>
		</div>

		<div class="dropdown m-2">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Клн
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		  	<a class="dropdown-item" href="<?=$siteroot?>/pages/newQuestion.php">Создать вопрос</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/questions.php">Список вопросов</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/newKln.php">Создать КЛН</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/klns.php">Список КЛН</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillKln.php">Наполнить КЛН вопросами</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/klnQuestion.php">Просмотр КЛН</a>
		  </div>
		</div>

		<div class="dropdown m-2">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Роли
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		  	<a class="dropdown-item" href="<?=$siteroot?>/pages/newRole.php">Создать роль</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/roles.php">Список ролей</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillRole.php">Применить роль к пользователю</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/roleUser.php">Просмотр роли</a>
		  </div>
		</div>

		<div class="dropdown m-2">
		  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    Права
		  </button>
		  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
		  	<a class="dropdown-item" href="<?=$siteroot?>/pages/newRight.php">Создать право</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/rights.php">Список прав</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillRightsUsers.php">Добавить в список Право->Пользователь</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/rightUser.php">Просмотреть права пользователей</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillRightsRoles.php">Добавить в список Право->Роль</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/rightRole.php">Просмотреть права ролей</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillRightsKlns.php">Добавить в список Право->КЛН</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/rightKln.php">Просмотреть права КЛН</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/fillRightsRestaurants.php">Добавить в список Право->Ресторан</a>
		    <a class="dropdown-item" href="<?=$siteroot?>/pages/rightRestaurant.php">Просмотреть права ресторанов</a>
		  </div>
	<?php } ?>
	</div>
	<?php include 'info.php'; ?>
</div>
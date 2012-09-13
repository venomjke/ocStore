<?php echo $header; ?>
<h1>Шаг 3 - Конфигурация</h1>
<div id="column-right">
  <ul>
    <li>Лицензия</li>
    <li>Подготовка</li>
    <li><b>Конфигурация</b></li>
    <li>Окончание</li>
  </ul>
</div>
<div id="content">
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
  	<p>1 . Введите настройки БД.</p>
  	<fieldset>
	<table>
	<tr>
		<td width="185"><span class="required">*</span>Драйвер БД:</td>
		<td><select name="db_driver">
		<option value="mysql" <?php if ($db_driver == "mysql") { echo 'selected'; } ?>>MySQL</option>
		<option value="mysql_cached" <?php if ($db_driver == "mysql_cached") { echo 'selected'; } ?>>MySQL с кэшированием</option>
		<option value="pgsql" <?php if ($db_driver == "pgsql") { echo 'selected'; } ?>>PostgreSQL</option>
		</select></td>
	</tr>
	<tr>
		<td width="185"><span class="required">*</span>Сервер БД:</td>
		<td><input type="text" name="db_host" value="<?php echo $db_host; ?>" />
	  	<br />
	  	<?php if ($error_db_host) { ?>
	  	<span class="required"><?php echo $error_db_host; ?></span>
	  	<?php } ?></td>
	</tr>
	<tr>
		<td><span class="required">*</span>Пользователь:</td>
		<td><input type="text" name="db_user" value="<?php echo $db_user; ?>" />
	  	<br />
	  	<?php if ($error_db_user) { ?>
	  	<span class="required"><?php echo $error_db_user; ?></span>
	  	<?php } ?></td>
	</tr>
	<tr>
		<td><span class="required">&nbsp;</span>Пароль:</td>
		<td><input type="text" name="db_password" value="<?php echo $db_password; ?>" /></td>
	</tr>
	<tr>
		<td><span class="required">*</span>Имя БД:</td>
		<td><input type="text" name="db_name" value="<?php echo $db_name; ?>" />
	  	<br />
	  	<?php if ($error_db_name) { ?>
	  	<span class="required"><?php echo $error_db_name; ?></span>
	  	<?php } ?></td>
	</tr>
	<tr>
		<td><span class="required">&nbsp;</span>Префикс БД:</td>
		<td><input type="text" name="db_prefix" value="<?php echo $db_prefix; ?>" /></td>
	</tr>
	</table>
  	</fieldset>
  	<p>2. Введите логин и пароль администратора.</p>
  	<fieldset>
	<table>
	<tr>
		<td width="185"><span class="required">*</span>Логин:</td>
		<td><input type="text" name="username" value="<?php echo $username; ?>" />
	  	<?php if ($error_username) { ?>
	  	<span class="required"><?php echo $error_username; ?></span>
	  	<?php } ?></td>
	</tr>
	<tr>
		<td><span class="required">*</span>Пароль:</td>
		<td><input type="text" name="password" value="<?php echo $password; ?>" />
	  	<?php if ($error_password) { ?>
	  	<span class="required"><?php echo $error_password; ?></span>
	  	<?php } ?></td>
	</tr>
	<tr>
		<td><span class="required">*</span>E-Mail:</td>
		<td><input type="text" name="email" value="<?php echo $email; ?>" />
	  	<?php if ($error_email) { ?>
	  	<span class="required"><?php echo $error_email; ?></span>
	  	<?php } ?></td>
	</tr>
	</table>
  	</fieldset>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button">Back</a></div>
      <div class="right">
        <input type="submit" value="Continue" class="button" />
      </div>
   </div>
  </form>
</div>
<?php echo $footer; ?>
<?php echo $header; ?>
<style type="text/css">
.ems_calc {color: #000; background-color: #fff; border: 1px dotted #bebebe; text-align: left; padding: 5px;}
hr {border: 0px; border-bottom: 1px dotted #999; }
</style>

<script language="javascript" type="text/javascript">
<!--
function test_calc() {
	var link = 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/admin/controller/shipping/ems_ajax/';
	var form = document.getElementById('test_calc-form');
	$("#ytr").html("<b>Рассчитываю...</b>");
	$.post(link + "ems_ajax.php", { city_from: form.city_from.value, city_to: form.city_to.value, weight: form.weight.value, val: form.val.value }, function(data)
	{
	$("#ytr").html('Результат:');
	$("#yt").html('<b>' + data + '</b>');
	});
};
//-->
</script>


<div id="content">

  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>

  <div class="box">
    <div class="heading">
    <div class="heading">
    <h1><?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
   </div>


  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
	<tr>
        <td>Название метода:</td> <td><input type="text" name="ems_mname" value="<?php echo $ems_mname; ?>"></td>
	</tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="ems_status">
              <?php if ($ems_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
		<tr>
          <td><?php echo $entry_max_weight; ?></td>
          <td><input type="text" name="ems_max_weight" value="<?php echo $ems_max_weight; ?>" size="5" readonly /></td>
        </tr>
        <tr>
          <td><?php echo $entry_sort_order; ?></td>
          <td><input type="text" name="ems_sort_order" value="<?php echo $ems_sort_order; ?>" size="1" /></td>
        </tr>
        <tr>
<td><?php echo 'Пункт отправления'; ?></td>
<td>
			<? if ($locations) { ?>
				<select name="ems_city_from">
					<option value="0"><?php echo 'Не выбрано:'; ?></option>
					<? foreach($locations AS $location) { ?>
						<option value="<? echo $location['value']; ?>" <? if ($location['value'] == $ems_city_from) echo 'selected'; ?>>
<?

 //$rez = mb_strtoupper(mb_substr($location['name'], 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($location['name'], MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($location['name']), 'UTF-8');
$rez = mb_convert_case($location['name'], MB_CASE_TITLE, 'UTF-8');
$rez = str_replace(array('Район', 'Край', 'Область', 'Автономный Округ', 'Автономная Область', 'Республика', 'Ао'), array('район', 'край', 'область', 'автономный округ', 'автономная область', 'республика', 'АО'), $rez);
echo $rez;
?>
</option>
					<? } ?>
				</select>
			<? } else { ?>
				<? echo 'Нет соединения с EMS'; ?>
				<input type="hidden" name="ems_city_from" value="<? echo $ems_city_from; ?>" />
			<? } ?>
</td>
        </tr>
        <tr>
          <td>Разрешить отправку внутри региона?</td>
          <td><select name="ems_in">
              <?php if ($ems_in) { ?>
              <option value="1" selected="selected">Да</option>
              <option value="0">Нет</option>
              <?php } else { ?>
              <option value="1">Да</option>
              <option value="0" selected="selected">Нет</option>
              <?php } ?>
            </select></td>
        </tr>
	<tr>
        <td>Добавлять дней к периоду доставки:</td> <td><input type="text" name="ems_plus" value="<?php echo $ems_plus; ?>" size="1"></td>
	</tr>

	<tr>
        <td>Сумма, добавляемая за доставку (к каждому товару в чеке):</td> <td><input type="text" name="ems_dopl" value="<?php echo $ems_dopl; ?>" size="3"></td>
	</tr>
        <tr>
          <td>Разрешить объявление ценности (объявляется на товары не более 50000 руб.)?</td>
          <td><select name="ems_ob">
              <?php if ($ems_ob) { ?>
              <option value="1" selected="selected">Да</option>
              <option value="0">Нет</option>
              <?php } else { ?>
              <option value="1">Да</option>
              <option value="0" selected="selected">Нет</option>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
	<td class="center" colspan="8"><?php echo $entry_vid.'<small><br>*для правильной работы модуля используйте выражения дней только из этих видов: "дня, дней, дн." </small>'; ?><br><input type="text" name="ems_vid" value="<?php echo $ems_vid; ?>" size="160" /></td>
        </tr>
        <tr>
	<td class="center" colspan="8"><?php echo $entry_vid_out; ?><br><input type="text" name="ems_vid_out" value="<?php echo $ems_vid_out; ?>" size="160" /></td>
        </tr>
</form>
<table width="100%" class="ems_calc">
<tr>
<td><b>Тестовый калькулятор EMS:</b> <br><br><br> Пункт отправления: </td>
<td>
<form method=POST id="test_calc-form" name="test_calc-form" action="">
<? if ($locations) { ?>
<br><br><br><select name="city_from">
<option value="0"><?php echo 'Не выбрано:'; ?></option>
<? foreach($locations AS $location) { ?>
<option value="<? echo $location['value']; ?>" <? if ($location['value'] == $ems_city_from) echo 'selected'; ?>>
<?
$rez = mb_convert_case($location['name'], MB_CASE_TITLE, 'UTF-8');
$rez = str_replace(array('Район', 'Край', 'Область', 'Автономный Округ', 'Автономная Область', 'Республика', 'Ао'), array('район', 'край', 'область', 'автономный округ', 'автономная область', 'республика', 'АО'), $rez);
echo $rez;
?>
</option>
<? } ?>
</select>
<? } else { ?>
<input type="hidden" name="city_from" value="" />
<? } ?>
</td>
</tr>
<tr>
<td>  Пункт доставки: </td>
<td>
<? if ($locations) { ?>
<select name="city_to">
<option value="0"><?php echo 'Не выбрано:'; ?></option>
<? foreach($locations AS $location) { ?>
<option value="<? echo $location['value']; ?>" <? if ($location['value'] == $ems_city_from) echo 'selected'; ?>>
<?
$rez = mb_convert_case($location['name'], MB_CASE_TITLE, 'UTF-8');
$rez = str_replace(array('Район', 'Край', 'Область', 'Автономный Округ', 'Автономная Область', 'Республика', 'Ао'), array('район', 'край', 'область', 'автономный округ', 'автономная область', 'республика', 'АО'), $rez);
echo $rez;
?>
</option>
<? } ?>
</select>
<? } else { ?>
<input type="hidden" name="city_to" value="" />
<? } ?>
</td>
</tr>
<tr>
<td>Вес товара:</td>
<td><input type="text" name="weight" value="" size="1" /> кг.</td>
</tr>
<tr>
<td>Объявить ценность:</td>
<td><input type="text" name="val" size="3"></td>
</tr>
<tr>
<td><span id="ytr">Результат:</span></td>
<td><div id='yt'>--</div></td>
</tr>
<script language="javascript" type="text/javascript">
<!--
$(function(){

	$('#test_calc-form').submit(function() {
	  test_calc();
	  return false;
	});

});

//-->
</script>
<tr>
<td>
<br><input type="submit" name="submit" value="Рассчитать">
</form>
</td>
</tr>
</table>
      </table>
    </div>
  </div>
</div>
<?php echo $footer; ?>

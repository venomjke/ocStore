<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <div id="tabs" class="htabs">
        <a href="#tab-1"><span><?php echo $entry_area1; ?></span></a>
    </div>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
    <div id="tab-1" style="clear: both;">
      <table class="form">
        <tr>
          <td><?php echo $entry_enabled; ?></td>
          <td><select name="cr2gridenabler_status">
                <?php if ($cr2gridenabler_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
        </tr>
        <tr style="display: none; ">
          <td><?php echo $entry_mode; ?></td>
          <td><select name="cr2gridenabler_mode">
                <?php if ($cr2gridenabler_mode) { ?>
                <option value="0"><?php echo $entry_function0; ?></option>
                <option value="1" selected="selected"><?php echo $entry_function1; ?></option>
                <?php } else { ?>
                <option value="0" selected="selected"><?php echo $entry_function0; ?></option>
                <option value="1"><?php echo $entry_function1; ?></option>
                <?php } ?>
              </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_moduleposition; ?></td>
          <td><select name="cr2gridenabler_moduleposition" onchange="redo_modules(this.value);">
                <option value="content_top" <?php echo ($cr2gridenabler_moduleposition=="content_top"?"selected=\"selected\"":""); ?>><?php echo $entry_contenttop; ?></option>
                <option value="content_bottom" <?php echo ($cr2gridenabler_moduleposition=="content_bottom"?"selected=\"selected\"":""); ?>><?php echo $entry_contentbottom; ?></option>
                <option value="column_left" <?php echo ($cr2gridenabler_moduleposition=="column_left"?"selected=\"selected\"":""); ?>><?php echo $entry_columnleft; ?></option>
                <option value="column_right" <?php echo ($cr2gridenabler_moduleposition=="column_right"?"selected=\"selected\"":""); ?>><?php echo $entry_columnright; ?></option>
          </select><br />
          <span class="help"><?php echo $help_moduleposition; ?></span></td>
        </tr>
        <tr>
          <td><?php echo $entry_moduleselector; ?></td>
          <td><select name="cr2gridenabler_selector">
                <?php if ($cr2gridenabler_selector) { ?>
                <option value="0"><?php echo $entry_selector0; ?></option>
                <option value="1" selected="selected"><?php echo $entry_selector1; ?></option>
                <?php } else { ?>
                <option value="0" selected="selected"><?php echo $entry_selector0; ?></option>
                <option value="1"><?php echo $entry_selector1; ?></option>
                <?php } ?>
              </select></td>
        </tr>
        <tr>
           <td><?php echo $entry_layouts; ?></td>
           <td><select name="cr2gridenabler_layouts[]" multiple size=10>
           <?php foreach($layouts as $layout){ ?>
                <option value="<?php echo $layout['layout_id']; ?>" <?php if (in_array($layout['layout_id'],$cr2gridenabler_layouts)) { ?> selected="selected" <?php }; ?>><?php echo ucwords($layout['name']); ?></option>
           <?php }?>
           </select><br /><span class="help"><?php echo $help_layouts; ?></span></td>
        </tr>
        <tr>
           <td><?php echo $entry_layoutcount; ?></td>
           <td><b><?php echo $cr2gridenabler_layoutcount; ?></b>
           <?php if ($cr2gridenabler_layoutcount<11) {?><br /><span class="help" style="color: red;"><?php echo $entry_layoutwarning; ?></span> <?php }; ?></td>
        </tr>
      </table>
     </div><!--area1-->
      <div id="modulesdiv">
      <?php
      foreach( $modules as $id => $module){ ?>
      <input type="hidden" name="cr2gridenabler_module[<?php echo $id; ?>][layout_id]" value="<?php echo $module['layout_id']; ?>">
      <input type="hidden" name="cr2gridenabler_module[<?php echo $id; ?>][position]" value="<?php echo $module['position']; ?>">
      <input type="hidden" name="cr2gridenabler_module[<?php echo $id; ?>][status]" value="<?php echo $module['status']; ?>">
      <input type="hidden" name="cr2gridenabler_module[<?php echo $id; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>">
      <?php } ?>
      </div>
    </form>
  </div>
	<div style="text-align:center; color:#666666;">
		CR2 Grid Enabler v<?php echo $_version; ?> - <a href="http://www.riotreactions.eu/cat/opencart-modules/grid-enabler/" target="_blank">Support</a>
	</div>
</div>
<script type="text/javascript"><!--
$(function() { $('#tabs a').tabs();  });
function redo_modules(newpos){
var mods = ''; var j = 0;
for (var i=0;i<<?php echo count($modules); ?>;i++) {
      mods += '<input type="hidden" name="cr2gridenabler_module['+j+'][status]" value="1"> ';
      mods += '<input type="hidden" name="cr2gridenabler_module['+j+'][sort_order]" value="0"> ';
      mods += '<input type="hidden" name="cr2gridenabler_module['+j+'][position]" value="'+newpos+'"> ';
      mods += '<input type="hidden" name="cr2gridenabler_module['+j+'][layout_id]" value="'+i+'"> ';
      j++;
      };
$('#modulesdiv').html(mods);
}
function generate_preview(){
      var sel = $('#pre_sel').val();
      if (sel.match(/#/)) {sel = sel.replace(/^#/,'id="')+'"'; } else {sel = sel.replace(/^./gi,'class="')+'"'; };
      var mstruct = '%menuitems%';
      var lwrap = $('#pre_lwrap').val();
      var lstruct = $('#pre_lstruct').val();

      var final = lstruct.replace(/%target%/,'http://www.example1.com/');
      final = final.replace(/%label%/,'Example Link 1');
      final = final+"\n"+final.replace(/1/g,'2')+"\n"+final.replace(/1/g,'3');
      final = "\n"+final+"\n";
      final = lwrap.replace(/%menuitems%/,final);
      final = "\n"+final+"\n";
      final = mstruct.replace(/%menuitems%/,final);
      final = final.replace(/%id%/,sel);
      $('#pre_view').val(final);
}

generate_preview();
//--></script>
<script type="text/javascript"><!--
var items = <?php echo $iters; ?>;

function addItem() {
	html  = '<tbody id="row' + items + '">';
	html += '  <tr>';
	html += '    <td class="left">';
<?php foreach ($languages as $language) { ?>
	html += '    <input name="cr2gridenabler_item[' + items + '][label_<?php echo $language['language_id']; ?>]" size="25">';
	html += '    <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" style="vertical-align: top;" /><br/>';
<?php }; ?>
	html += '    </td>';
	html += '    <td class="left"><input name="cr2gridenabler_item[' + items + '][url]" size="80"></td>';
	html += '    <td class="left"><select name="cr2gridenabler_item[' + items + '][status]">';
     html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
     html += '      <option value="0"><?php echo $text_disabled; ?></option>';
     html += '    </select></td>';
	html += '    <td class="right"><input type="text" name="cr2gridenabler_item[' + items + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#row' + items + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
	html += '  </tr>';
	html += '</tbody>';

	$('#module tfoot').before(html);

	items++;
}
//--></script>
<?php echo $footer; ?>
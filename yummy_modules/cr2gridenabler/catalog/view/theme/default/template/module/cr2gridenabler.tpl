<?php /* cr2 grid enabler v0.5.1 */
if ($status) { ?>
<script type="text/javascript"><!--
// cr2 grid enabler v<?php echo $version; ?> //
$(document).ready(function() {
if(typeof display == 'function') { display('grid'); }
<?php if ($selector) { ?>$('.display').hide();
<?php }; ?>
});
--></script>
<?php }; // if status ?>

<?php
if(isset($scroller_text)) {
	echo '<div id="scroller_container"><div id="scroller">'.$scroller_text.'</div></div>';
}

foreach ($modules as $module) {
	echo $module;
}
?>

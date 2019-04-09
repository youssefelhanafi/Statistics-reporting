<script language="javascript" type="text/javascript">
<!--
function popitup(url) {
    newwindow=window.open(url,'name','height=1200,width=1500');
    if (window.focus) {newwindow.focus()}
    return false;
}

// -->
</script>
<?php
$var = 1;

if ($var == 1) {
?>
	<script type="text/javascript">
		popitup('index.php')
	</script>
<?php	
}
?>

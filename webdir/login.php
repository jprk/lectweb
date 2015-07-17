<?php
    $server = $_SERVER["SERVER_NAME"];
	$base   = dirname ($_SERVER["REQUEST_URI"])."/";
	header ("Location: http://".$server.$base."ctrl.php?act=show,login,42");
	exit;
?>
<?php
	session_start();
	$name = $_SESSION['fileName'];
	$type = $_SESSION['fileType'];
	$document = $_SESSION['fileDocument'];
	
	header('Content-Type:' . $type);
	header('Content-Disposition: attachment; filename=' . $name);
	echo $document;
?>
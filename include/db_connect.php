<?php
	include_once 'config.php';
	try {
		$database = new PDO('mysql:host=' . HOST . ';dbname=' . DATABASE, USER, PASSWORD);
	}
	catch (PDOException $e) {
			echo $e-getMessage();
	}
?>

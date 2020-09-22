<?php 
	require 'model/FillDB.php';
	require 'model/Timer.php';
	
	use model\FillDB;
	use model\Timer;

	Timer::start();
	$fillDBClass = new FillDB();
  	$fillDBClass->fill();	
	

	echo Timer::finish() . ' сек.  ';
?>

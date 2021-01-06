<?php

//place this before any script you want to calculate time
$time_start = microtime(true); 

$sec = $_GET['sec'] ? $_GET['sec'] : 10;

//sample script
for($i=0; $i<$sec; $i++){
 sleep(1);
}

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
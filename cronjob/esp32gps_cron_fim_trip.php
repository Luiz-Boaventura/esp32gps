<?php 
include_once(__DIR__.'/../models/Cronjob.php');

use \models\Cronjob;

$cronjob = new Cronjob();
$cronjob->setarFimTrip();
?>
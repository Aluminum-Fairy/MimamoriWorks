<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/DevInfo.php");
require_once(__DIR__."/../../../lib/MW/ACInfo.php");
header("Content-Type: application/json; charset=utf-8");
$Response;
$acInfo = new ACInfo($dsn, $db_user, $db_pass);
$devInfo = new DevInfo($dsn, $db_user, $db_pass);

if(filter_input(INPUT_POST,'devID',FILTER_VALIDATE_FLOAT)){
	$devInfo->inputDevID($_POST['devID']);
}
if(filter_input(INPUT_POST,'devToken')){
	$devInfo->inputDevToken($_POST['devToken']);
}

if($devInfo->devAuth()){
	$acInfo->inputSettingID(1);
	$acStatus=$acInfo->acStatus();

	$Response = array('acName'=>$acStatus['ACname']);
	$Response +=array('temp'=>$acStatus['temp']);
	$Response +=array('mode'=>$acStatus['mode']);
	$Response +=array('volume'=>$acStatus['volume']);

	echo json_encode($Response);
}


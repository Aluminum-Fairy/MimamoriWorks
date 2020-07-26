<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/DevInfo.php");
require_once(__DIR__."/../../../lib/MW/ACInfo.php");
header("Content-Type: application/json; charset=utf-8");
$Response;
$acInfo = new ACInfo($dsn, $db_user, $db_pass);
$devInfo = new DevInfo($dsn, $db_user, $db_pass);

if(filter_input(INPUT_POST,'devID',FILTER_VALIDATE_FLOAT) && filter_input(INPUT_POST,'devToken')){
	$devInfo->inputDevID($_POST['devID']);
	$devInfo->inputDevToken($_POST['devToken']);
}

if($devInfo->devAuth()){
	$Response = array('Auth_Result'=>true);
	if($acInfo->srchSettingID($devInfo->getDevID())){
		$settingIDrow = $acInfo->getSettingIDrow();
		$Response+=array('AC_Statuses'=>array());
		foreach($settingIDrow as $settingID){
			if($acStatus=$acInfo->acStatus($settingID['settingID'])){
				$Response['AC_Statuses']+=array($settingID['settingID']=>$acStatus);
			}
		}
	}
}else{
	$Response = array('Auth_Result'=>false);
}

echo json_encode($Response);


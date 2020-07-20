<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/UserInfo.php");
require_once(__DIR__."/../../../lib/MW/DevInfo.php");
header("Content-Type: application/json; charset=utf-8");

class RegDev extends DevInfo{

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
		$this->genToken();
	}

	public function setDev2db(){
		$regDevsql="INSERT INTO Device (`DevID`,`DevName`,`DevToken`,`Expl`) values (:DevID,:DevName,:DevToken,:Expl)";
		if(is_null($this->devExpl)){
			$regDevsql="INSERT INTO Device (`DevID`,`DevName`,`DevToken`) values (:DevID,:DevName,:DevToken)";
		}
		$regDevpre=$this->dbh->prepare($regDevsql);
		$regDevpre->bindValue(":DevID",$this->devID,PDO::PARAM_STR);
		$regDevpre->bindValue(":DevName",$this->devName,PDO::PARAM_STR);
		$regDevpre->bindValue(":DevToken",$this->devToken,PDO::PARAM_STR);
		if(!(is_null($this->devExpl))){
			$regDevpre->bindValue(":Expl",$this->devExpl,PDO::PARAM_STR);
		}
		return  $regDevpre->execute();
	}

	public function devDesc(){
		$descDevsql = "UPDATE Device SET Expl=:devExpl WHERE DevID=:DevID";
		$descDevpre = $this->dbh->prepare($descDevsql);
		$descDevpre->bindValue(":DevID",$this->devID,PDO::PARAM_INT);
		$descDevpre->bindValue(":devExpl",$this->devExpl,PDO::PARAM_STR);
		return $descDevpre->execute();
	}
}

$regDev = new RegDev($dsn, $db_user, $db_pass);
$userInfo = new UserInfo($dsn, $db_user, $db_pass);
$Response;																		//結果出力用配列

if(filter_input(INPUT_POST,'devID',FILTER_VALIDATE_INT)){
	$regDev->inputDevID($_POST['devID']);
	$Response = array('devID'=>true);
}else{
	$Response = array('devID'=>false);
}

if(filter_input(INPUT_POST,'devName')){
	$regDev->inputDevName($_POST['devName']);
	$Response = array_merge($Response,array('devName'=>true));
}else{
	$Response = array_merge($Response,array('devName'=>false));
}

if(filter_input(INPUT_POST,'devExpl')){
	$regDev->inputDevExpl($_POST['devExpl']);
	$Response = array_merge($Response,array('devExpl'=>true));
}else{
	$Response = array_merge($Response,array('devExpl'=>false));
}

if(filter_input(INPUT_POST,'mailAddr',FILTER_VALIDATE_EMAIL)){
	$userInfo->inputMail($_POST['mailAddr']);
	$Response = array_merge($Response,array('Mail'=>true));
}else{
	$Response = array_merge($Response,array('Mail'=>false));
}

if(filter_input(INPUT_POST,'Passwd')){
	$userInfo->inputPasswd($_POST['Passwd']);
	$Response = array_merge($Response,array('Passwd'=>true));
}else{
	$Response = array_merge($Response,array('Passwd'=>false));
}

if($userInfo->userAuth()){
	$Response = array_merge($Response,array('DB_Result'=>array('Auth'=>true)));
	if($regDev->dbDevCheck()){
		$Response['DB_Result'] +=array('Duplication'=>false);
		$Response['DB_Result'] +=array('RegDev'=>$regDev->setDev2db());
		$Response['DB_Result'] +=array('DevToken'=>$regDev->getDevToken());
		$Response['DB_Result'] +=array('getToken'=>true);
	}else{
		$Response['DB_Result'] +=array('Duplication'=>true);
		$Response['DB_Result'] +=array('AddDevDescriptions'=>$regDev->devDesc());
		$Response['DB_Result'] +=array('getToken'=>false);
	}
}else{
	$Response = array_merge($Response,array('DB_Result'=>array('Auth'=>false)));
}

$ResJ = json_encode($Response);
echo $ResJ;
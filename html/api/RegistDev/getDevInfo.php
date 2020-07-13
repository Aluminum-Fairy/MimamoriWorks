<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/UserInfo.php");
header("Content-Type: application/json; charset=utf-8");

class RegDev extends UserInfo{
	private $devExpl;
	private $devID;
	private $devName;
	private $devToken;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
		$this->genToken();
	}

	public function inputDevExpl($input){
		$this->devExpl = $input;
	}

	public function inputDevID($input){
		$this->devID = (float)$input;
	}

	public function inputDevName($input){
		$this->devName = $input;
	}

	public function getDevToken(){
		return $this->devToken;
	}

	public function setDev2db(){
		$regDevsql="INSERT INTO Device (`DevID`,`DevName`,`DevToken`,`Expl`) values (:DevID,:DevName,:DevToken,:Expl)";
		if(is_null($this->devExpl)){
			$regDevsql="INSERT INTO Device (`DevID`,`DevName`,`DevToken`) values (:DevID,:DevName,:DevToken)";
		}
		$regDevpre=$this->dbh->prepare($regDevsql);
		$regDevpre->bindvalue(":DevID",$this->devID,PDO::PARAM_STR);
		$regDevpre->bindvalue(":DevName",$this->devName,PDO::PARAM_STR);
		$regDevpre->bindvalue(":DevToken",$this->devToken,PDO::PARAM_STR);
		if(!(is_null($this->devExpl))){
			$regDevpre->bindValue(":Expl",$this->devExpl,PDO::PARAM_STR);
		}
		return  $regDevpre->execute();
	}

	public function dbDevCheck(){
		$Checksql = "SELECT COUNT(DevID) FROM Device WHERE DevID = :DevID";
		$Checkpre = $this->dbh->prepare($Checksql);
		$Checkpre->bindvalue(":DevID",$this->devID,PDO::PARAM_INT);
		if($Checkpre->execute()){
			if($Checkpre->fetchColumn() == 0){
				return true;
			}
		}
		return false;
	}

	public function devDesc(){
		$descDevsql = "UPDATE Device SET Expl=:devExpl WHERE DevID=:DevID";
		$descDevpre = $this->dbh->prepare($descDevsql);
		$descDevpre->bindvalue(":DevID",$this->devID,PDO::PARAM_INT);
		$descDevpre->bindvalue(":devExpl",$this->devExpl,PDO::PARAM_STR);
		return $descDevpre->execute();
	}

	private function genToken(){
		$this->devToken = rand(1000000,9999999);
	}
}

$regDev = new RegDev($dsn, $db_user, $db_pass);
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
	$regDev->inputMail($_POST['mailAddr']);
	$Response = array_merge($Response,array('Mail'=>true));
}else{
	$Response = array_merge($Response,array('Mail'=>false));
}

if(filter_input(INPUT_POST,'Passwd')){
	$regDev->inputPasswd($_POST['Passwd']);
	$Response = array_merge($Response,array('Passwd'=>true));
}else{
	$Response = array_merge($Response,array('Passwd'=>false));
}

if($regDev->Auth()){
	$Response = array_merge($Response,array('DB_Result'=>array('Auth'=>true)));
	if($regDev->dbDevCheck()){
		$Response['DB_Result'] +=array('Duplication'=>false);
		$Response['DB_Result'] +=array('RegDev'=>$regDev->setDev2db());
		$Response['DB_Result'] +=array('DevToken'=>$regDev->getDevToken());
	}else{
		$Response['DB_Result'] +=array('Duplication'=>true);
		$Response['DB_Result'] +=array('AddDevDescriptions'=>$regDev->devDesc());
	}
}else{
	$Response = array_merge($Response,array('DB_Result'=>array('Auth'=>false)));
}

$ResJ = json_encode($Response);
echo $ResJ;
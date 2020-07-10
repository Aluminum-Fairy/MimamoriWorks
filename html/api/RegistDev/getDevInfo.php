<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/UserInfo.php");
header("Content-Type: application/json; charset=utf-8");

class RegDev extends UserInfo{
	private $Dupl = false;
	private $devExpl;
	private $devID;
	private $devName;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function inputDevExpl($input){
		$this->devExpl = $input;
	}

	public function inputDevID($input){
		$this->devID = $input;
	}

	public function inputDevName($input){
		$this->devName = $input;
	}

	public function setDev2db(){
		$regDev_SQL="INSERT INTO Device (`DevID`,`DevName`,`Expl`) values (:DevID,:DevName,:Expl)";
		$regDev_Pre=$this->dbh->prepare($regDev_SQL);
		$regDev_Pre->bindvalue(":DevID",$this->devID,PDO::PARAM_STR);
		$regDev_Pre->bindvalue(":DevName",$this->devName,PDO::PARAM_STR);
		$regDev_Pre->bindValue(":Expl",$this->devExpl,PDO::PARAM_STR);
		$regDev_Res = $regDev_Pre->execute();
		return $regDev_Res;
	}

	public function dbDevCheck(){
		$Check_SQL = "SELECT COUNT(DevID) FROM Device WHERE DevID = :DevID";
		$Check_Pre = $this->dbh->prepare($Check_SQL);
		$Check_Pre->bindvalue(":DevID",$this->,PDO::PARAM_STR);
		$ResCheck = $Check_Pre->execute();
		if($ResCheck){
			if($Check_Pre->fetchColumn() == 0){
				return true;
			}
		}
		return false;
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


$ResJ = json_encode($Response);
echo $ResJ;

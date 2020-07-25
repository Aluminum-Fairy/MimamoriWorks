<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/UserInfo.php");
header("Content-Type: application/json; charset=utf-8");

class RegUI extends UserInfo{

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function setUI2db(){
		$regUIsql="INSERT INTO user (`name`,`mailaddr`,`Passwd`) values (:name,:mailAddr,:PasswdHash)";
		$regUIpre=$this->dbh->prepare($regUIsql);
		$regUIpre->bindValue(":name",$this->userName,PDO::PARAM_STR);
		$regUIpre->bindValue(":mailAddr",$this->mailAddr,PDO::PARAM_STR);
		$regUIpre->bindValue(":PasswdHash",password_hash($this->Passwd, PASSWORD_DEFAULT),PDO::PARAM_STR);
		return $regUIpre->execute();
	}

}

$regUI = new RegUI($dsn, $db_user, $db_pass);
$Response;																		//結果出力用配列

if(filter_input(INPUT_POST,'UserName')){
	$regUI->inputName($_POST['UserName']);
	$Response = array('Name'=>true);
}else{
	$Response = array('Name'=>false);
}

if(filter_input(INPUT_POST,'mailAddr',FILTER_VALIDATE_EMAIL)){
	$regUI->inputMail($_POST['mailAddr']);
	$Response +=array('Mail'=>true);
}else{
	$Response +=array('Mail'=>false);
}

if(filter_input(INPUT_POST,'Passwd')){
	$regUI->inputPasswd($_POST['Passwd']);
	$Response +=array('Passwd'=>true);
}else{
	$Response +=array('Passwd'=>false);
}

if($regUI->dbUICheck()){
	$Response +=array('DB_Result'=>array('RegistUI'=>$regUI->setUI2db()));
	$Response['DB_Result'] +=array('Duplication'=>false);
}else{
	$Response +=array('DB_Result'=>array('RegistUI'=>false));
	$Response['DB_Result'] +=array('Duplication'=>true);
}

$ResJ = json_encode($Response);
echo $ResJ;
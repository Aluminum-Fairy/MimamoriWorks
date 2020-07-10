<?php
require_once(__DIR__."/../../../config/SQL_Login.php");
require_once(__DIR__."/../../../lib/MW/UserInfo.php");
header("Content-Type: application/json; charset=utf-8");

class RegUI extends UserInfo{
	private $Dupl = false;
	private $DevID;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function setUI2db(){
		$regUI_SQL="INSERT INTO user (`name`,`mailaddr`,`Passwd`) values (:name,:mailAddr,:PasswdHash)";
		$regUI_Pre=$this->dbh->prepare($regUI_SQL);
		$regUI_Pre->bindvalue(":name",$this->userName,PDO::PARAM_STR);
		$regUI_Pre->bindvalue(":mailAddr",$this->mailAddr,PDO::PARAM_STR);
		$regUI_Pre->bindValue(":PasswdHash",password_hash($this->Passwd, PASSWORD_DEFAULT),PDO::PARAM_STR);
		$regUI_Res = $regUI_Pre->execute();
		return $regUI_Res;
	}

	public function DBUICheck(){
		$Check_SQL = "SELECT COUNT(id) FROM user WHERE mailaddr = :mailAddr";
		$Check_Pre = $this->dbh->prepare($Check_SQL);
		$Check_Pre->bindvalue(":mailAddr",$this->mailAddr,PDO::PARAM_STR);
		$ResCheck = $Check_Pre->execute();
		if($ResCheck){
			if($Check_Pre->fetchColumn() == 0){
				return true;
			}
		}
		return false;
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
	$Response = array_merge($Response,array('Mail'=>true));
}else{
	$Response = array_merge($Response,array('Mail'=>false));
}

if(filter_input(INPUT_POST,'Passwd')){
	$regUI->inputPasswd($_POST['Passwd']);
	$Response = array_merge($Response,array('Passwd'=>true));
}else{
	$Response = array_merge($Response,array('Passwd'=>false));
}

if($regUI->DBUICheck()){
	if($regUI->setUI2db()){
		$Response = array_merge($Response,array('DB_Result'=>array('RegistUI'=>true)));
		$Response['DB_Result'] +=array('Duplication'=>false);
	}else{
		$Response = array_merge($Response,array('DB_Result'=>array('RegistUI'=>false)));
		$Response['DB_Result'] +=array('Duplication'=>false);
	}
}else{
	$Response = array_merge($Response,array('DB_Result'=>array('RegistUI'=>false)));
	$Response['DB_Result'] +=array('Duplication'=>true);
}

$ResJ = json_encode($Response);
echo $ResJ;
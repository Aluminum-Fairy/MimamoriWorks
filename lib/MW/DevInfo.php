<?php
class Devinfo{
	protected $devExpl;
	protected $devID;
	protected $devName;
	protected $devToken;
	protected $hdb;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
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

	protected function genToken(){
		$this->devToken = rand(1000000,9999999);
	}

	public function devAuth(){
		if(is_null($this->devID) || is_null($this->devToken)){
			return false;
		}
		$authSQL = "SELECT `DevToken` FROM Device WHERE DevID=:DevID";
		$authPre = $this->dbh->prepare($authSQL);
		$authPre->bindvalue(":DevID",$htis->devID,PDO::PARAM_INT);
		if($authPre->execute()){
			$dbToken = ($authPre->fetch())['DevToken'];
			return $dbToken == $this->devToken;
		}
		return false;
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
}
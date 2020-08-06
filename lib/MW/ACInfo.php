<?php
class ACInfo{
	protected $settingIDrow;
	protected $acNum;
	protected $acName;
	protected $acMode;
	protected $acVolume;
	protected $dbh;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function inputACname($input){
		$this->acName = $input;
	}

	public function inputACmode($input){
		$this->acMode = $input;
	}

	public function inputACvolume($input){
		$this->acVolue = $input;
	}

	public function getSettingIDrow(){
		if(is_null($this->settingIDrow)){
			return false;
		}
		return $this->settingIDrow;
	}

	public function srchSettingID($devID){
		$srchIDsql="SELECT settingID FROM `AC_Dev` WHERE `DevID` = :devID";
		$srchIDpre=$this->dbh->prepare($srchIDsql);
		$srchIDpre->bindValue(":devID",$devID,PDO::PARAM_STR);
		if($srchIDpre->execute()){
			$this->settingIDrow = $srchIDpre->fetchAll();
			return true;
		}
		return false;
	}

	public function acStatus($settingID){
		if(is_null($settingID)){
			return false;
		}
		$getStatusSql="SELECT `ACname`,`temp`,`mode`,`volume` ,`rcID`FROM `AC_config` WHERE `settingID`=:settingID";
		$getStatusPre=$this->dbh->prepare($getStatusSql);
		$getStatusPre->bindValue(":settingID",$settingID,PDO::PARAM_STR);
		if($getStatusPre->execute()){
			return $getStatusPre->fetch(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);
		}
		return false;
	}

}
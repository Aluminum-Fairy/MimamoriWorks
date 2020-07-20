<?php
class ACInfo{
	protected $settingID;
	protected $acName;
	protected $acMode;
	protected $acVolume;
	protected $dbh;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function inputSettingID($input){
		$this->settingID = $input;
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

	public function acStatus(){
		if(is_null($this->settingID)){
			return false;
		}
		$getStatusSql="SELECT `ACname`,`temp`,`mode`,`volume` FROM `airCon` WHERE `settingID`=:settingID";
		$getStatusPre=$this->dbh->prepare($getStatusSql);
		$getStatusPre->bindValue(":settingID",$this->settingID,PDO::PARAM_STR);
		if($getStatusPre->execute()){
			return $getStatusPre->fetch();
		}
		return false;
	}

}
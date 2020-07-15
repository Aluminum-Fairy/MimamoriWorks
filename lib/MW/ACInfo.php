<?php
class ACInfo{
	protected $settingID;
	protected $ACname;
	protected $mode;
	protected $volue;
	protected $dbh;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function inputSettingID($input){
		$this->settingID = $input;
	}

	public function acStatus(){
		if(is_null($this->settingID)){
			return false;
		}
		$getStatussql="SELECT `ACname`,`temp`,`mode`,`volume` FROM `airCon` WHERE `settingID`=:settingID";
		$getStatuspre=$this->dbh->prepare($getStatussql);
		$getStatuspre->bindValue(":settingID",$this->settingID,PDO::PARAM_STR);
		if($getStatuspre->execute()){
			return $getStatuspre->fetch();
		}
		return false;
	}

}
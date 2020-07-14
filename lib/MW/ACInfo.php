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

	public function acStaus(){
		if(is_null($this->settingID)){
			return false;
		}
		
	}

}
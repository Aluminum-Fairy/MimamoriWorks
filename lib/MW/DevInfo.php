<?php
class Devinfo{
	protected $devExpl;
	protected $devID;
	protected $devName;
	protected $devToken;
	protected $hdb;

	public function Auth(){
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
}
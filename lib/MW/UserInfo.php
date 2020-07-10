<?php
class UserInfo{
	protected $mailAddr;
	protected $userName;
	protected $Passwd;
	protected $dbh;

	public function inputMail($input){
		$this->mailAddr = $input;
	}

	public function inputName($input){
		$this->userName = $input;
	}

	public function inputPasswd($input){
		$this->Passwd = $input;
	}

	public function Auth(){
		if(is_null($this->mailAddr) || is_null($this->Passwd)){
			return false;
		}
		$authSQL="SELECT `Passwd` FROM user WHERE mailaddr = :mail";
		$authPre=$this->dbh->prepare($authSQL);
		$authPre->bindvalue(":mail",$this->mailAddr,PDO::PARAM_STR);
		if($authPre->execute()){
			$dbPasswd=($authPre->fetch())['Passwd'];
			return password_verify($this->Passwd,$dbPasswd);
		}
		return false;

	}
}
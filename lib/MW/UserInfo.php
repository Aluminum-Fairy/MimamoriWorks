<?php
class UserInfo{
	protected $mailAddr;
	protected $userName;
	protected $Passwd;
	protected $dbh;

	function __construct($dsn, $db_user, $db_pass){
		$this->dbh = new PDO($dsn, $db_user, $db_pass);
	}

	public function inputMail($input){
		$this->mailAddr = $input;
	}

	public function inputName($input){
		$this->userName = $input;
	}

	public function inputPasswd($input){
		$this->Passwd = $input;
	}

	public function userAuth(){
		if(is_null($this->mailAddr) || is_null($this->Passwd)){
			return false;
		}
		if(!$this->dbUICheck()){
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

	public function chPasswd($newPasswd){
		if(!$this->userAuth()){
			return false;
		}
		$chPasswdsql="UPDATE user SET Passwd = :newPasswd WHERE mailaddr = :mailAddr";
		$chPasswdpre=$this->dbh->prepare($chPasswdsql);
		$chPasswdpre->bindValue(":newPasswd",password_hash($newPasswd,PASSWORD_DEFAULT),PDO::PARAM_STR);
		$chPasswdpre->bindValue(":mailAddr",$this->mailAddr,PDO::PARAM_STR);
		return $chPasswdpre->execute();
	}

	public function dbUICheck(){
		if(is_null($this->mailAddr)){
			return false;
		}
		$checkSQL = "SELECT COUNT(id) FROM user WHERE mailaddr = :mailAddr";
		$checkPre = $this->dbh->prepare($checkSQL);
		$checkPre->bindvalue(":mailAddr",$this->mailAddr,PDO::PARAM_STR);
		if($checkPre->execute()){
			if($checkPre->fetchColumn() == 0){
				return true;
			}
		}
		return false;
	}
}
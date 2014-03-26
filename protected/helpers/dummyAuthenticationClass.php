<?php
class dummyAuthenticationClass
{
	private $dummy_username = 'test';
	private $dummy_password = '1234-zxcv';
	
	function __construct()
	{
		loggerClass::write('[i] dummyAthenticationClass object created',3);
	}

	public function auth($username,$password)
	{
		$auth_result = false;

		if($username==$this->dummy_username && $password==$this->dummy_password)
			$auth_result = true;

		loggerClass::write('<- auth(): '.serialize($auth_result),2);
		loggerClass::write('[i] Authentication credentials: username='.$username.' password='.$password,3);
		
		return $auth_result;
	}
}
?>
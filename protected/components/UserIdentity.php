<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
        private $nonLdapLogins = array(
            'otdel_vnedrenia_cbs'=>'1',
            'vnedrecerp'=>'1',
            'soprovoghderp'=>'1',
            'razrabcbs'=>'1',
            'vnedrecasu'=>'1',
            'soprovoghdasu'=>'1',
            'integrator_galact'=>'1',
            'integrator_sinerg'=>'1',
            'sr_developer'=>'sfK09f'
        );
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	
//	public function authenticate()
//	{
//		$users=array(
//			// username => password
//			'demo'=>'demo',
//			's.shupanov'=>'demo',
//			'admin'=>'admin',
//			'i.lysenko'=>'admin',
//		);
//
//		if(!isset($users[$this->username]))
//			$this->errorCode=self::ERROR_USERNAME_INVALID;
//		else if($users[$this->username]!==$this->password)
//			$this->errorCode=self::ERROR_PASSWORD_INVALID;
//		else
//		{
//			$this->_id = 4;
//			$this->errorCode=self::ERROR_NONE;
//		}
//		return !$this->errorCode;
//	}
	
	 
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */

	public function authenticate()
	{
            if (array_key_exists($this->username,$this->nonLdapLogins))
            {
                if ($this->password == $this->nonLdapLogins[$this->username])
                {
                    $this->UserAuthenticate();
                }  else {
                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                }
                return !$this->errorCode;
            }else
                {

                    $options = Yii::app()->params['ldap'];

                    $connection = ldap_connect($options['host']);

                    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

                        if($connection)
                        {
                            $bind = @ldap_bind($connection, $this->username.$options['domain'], $this->password);

                            if(!$bind) $this->errorCode = self::ERROR_PASSWORD_INVALID;  // ." ".self::ERROR_USERNAME_INVALID;
                            else {
                                        $this->UserAuthenticate();
                        }
                    }
                    return !$this->errorCode;
                }
	}

        private function UserAuthenticate()
        {
            $user = User::model()->find('LOWER(login)=?', array(strtolower($this->name)));
            if(!$user) $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;  // ." ".self::ERROR_USERNAME_INVALID;
            else {
                    $this->_id = $user->id;
                    $this->setState('title', $this->name);
                    $this->errorCode = self::ERROR_NONE;
            }
        }

        /**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}

}
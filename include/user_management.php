<?php
	include_once 'SecureSessionHandler.php';
	include_once 'db_connect.php';
	/********************
	 * function isLocked
	 * returns true if the user is locked because of too many failed login attempts
	 *******************/
	
	function isLocked($userId, $PDOHandle) {
		$currentTime = time();
		
		try {
		    $stm = $PDOHandle->prepare('SELECT id FROM login_attempts WHERE id = :id AND time > :time');
		    $stm->bindValue(':id', $userId);
		    $stm->bindValue(':time', ($currentTime - (10 * 60))); // account is locked if there were five failed attempts in the last ten minutes
		    $stm->execute();
		    $result = $stm->fetchAll();
		
    		if (count($result) >= 5) {
    			return true;
    		}
		} catch (PDOException $e) {
		    writePDOException($e);
		}
		return false;
	}
	
	/********************
	 * function login
	 * returns true if the username-password combination is correct and sets $_SESSION values
	 *******************/
	
	function login ($username, $password, $PDOHandle, $currentSession) {
		// query user information from database
		try
		{
		    $stm = $PDOHandle->prepare('SELECT id, username, password FROM users WHERE username = :username LIMIT 1;');
    		$stm->bindValue(':username', $username);
    		$stm->execute();
    		$result = $stm->fetchAll();
    		
    		// check if the user exists
    		if (count($result) == 0) {
    			return false;
    		}
    		
    		if (isLocked($result[0]['id'], $PDOHandle)) {
    			return false; //account is locked because of too many failed login attempts
    		}
    		
    		// create the hash of password and salt
    		// $password = hash('sha512', $password . $result[0]['salt']);
    		
    		if (password_verify($password, $result[0]['password'])) {
    			
    			// set user.sessionString (used as some kind of protection against hijacking)
    			$currentSession->put('user.sessionString', hash('sha512', $result[0]['password'] . $_SERVER['HTTP_USER_AGENT']));
    			
    			// set user.id and user.name
    			$currentSession->put('user.id', $result[0]['id']);
    			$currentSession->put('user.name', $result[0]['username']);
    			
    			return true;
    			
    		} else {
    		    // store failed login attempt to prevent brute force attacks
    		    try 
    		    {
        			 $stm = $PDOHandle->prepare("INSERT INTO login_attempts (`user_id`, `time`) VALUES (:userid, :time);");
        			 $stm->bindValue(':userid', $result[0]['id']);
        			 $stm->bindValue(':time', time());
        			 $stm->execute();
        			 return true;
        		 }
        		 catch (PDOException $e) {
        			 writePDoException($e);
        		 }
    		    
    			return false;
    		} 
		}
		catch (PDOException $e)
		{
		    writePDOException($e);
		}
		return false;
	}
	
	/********************
	 * function isLoggedIn
	 * returns true if a user is logged in
	 *******************/
	 
	 function isLoggedIn($PDOHandle, $currentSession) {
	     try
	     {
	        if (!empty($currentSession->get('user.id')) && !empty($currentSession->get('user.name')) && !empty($currentSession->get('user.sessionString'))) {
    			// query password hash from database
    			$stm = $PDOHandle->prepare('SELECT password FROM users WHERE id = :id LIMIT 1;');
    			$stm->bindValue(':id', $currentSession->get('user.id'));
    			$stm->execute();
    			$result = $stm->fetchAll();
    			
    			// check if the user exists
    			if (count($result) == 0) {
    				return false;
    			}
    			
    			if (hash('sha512', $result[0]['password'] . $_SERVER['HTTP_USER_AGENT']) == $currentSession->get('user.sessionString')) {
    				return true;
    			}
    			return false;
    		 }
    		 return false; 
	     }
	     catch (PDOException $e)
	     {
	         writePDOException($e);
	     }
		 
		 return false;
	 }
	 
	 
	/********************
	 * function createUser
	 * returns true if successful
	 *******************/
	 
	 function createUser($PDOHandle, $username, $password, $email = '') {
		 try {
			 $stm = $PDOHandle->prepare("INSERT INTO users (`id`, `username`, `email`, `password`) VALUES (NULL, :username, :email, :password);");
			 $stm->bindValue(':username', preg_replace('/[^a-zA-Z0-9-_]/', '', $username));
			 $stm->bindValue(':email', $email); //TODO: test if email address is valid
			 $stm->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
			 $stm->execute();
			 return true;
		 }
		 catch (PDOException $e) {
			 writePDoException($e);
		 }
		 
		 return false;
	 }
?>

<?php
	include_once 'SecureSessionHandler.php'
	
	/********************
	 * function login
	 * returns true if the username-password combination is correct and sets $_SESSION values
	 *******************/
	
	function login($username, $password, $PDOHandle, $currentSession) {
		// query user information from database
		$stm = $PDOHandle->prepare('SELECT id, username, password, salt FROM users WHERE username = :username LIMIT 1;');
		$stm->bindParam(':username', $username);
		$stm->execute();
		$result = $stm->fetchAll();
		
		// check if the user exists
		if (count($result) == 0) {
			return false;
		}
		
		// create the hash of password and salt
		$password = hash('sha512', $password . $result[0]['salt']);
		
		if ($password == $result[0]['password']) {
			
			// set user.sessionString (used as some kind of protection against hijacking)
			$currentSession->put('user.sessionString', hash('sha512', $password . $_SERVER['HTTP_USER_AGENT']));
			
			// set user.id and user.name
			$currentSession->put('user.id', $result[0]['id']);
			$currentSession->put('user.name', $result[0]['username']);
			
			return true;
		}
	}
	
	/********************
	 * function isLoggedIn
	 * returns true if a user is logged in
	 *******************/
	 
	 function isLoggedIn($PDOHandle, $currentSession) {
		 if (!empty($currentSession->get('user.id'), $currentSession->get('user.name'), $currentSession->get('user.sessionString'))) {
			// query password hash from database
			$stm = $PDOHandle->prepare('SELECT password FROM users WHERE id = :id LIMIT 1;');
			$stm->bindParam(':id', $currentSession->get('user.id'));
			$stm->execute();
			$result = $stm->fetchAll();
			
			// check if the user exists
			if (count($result) == 0) {
				return false;
			}
			
			if (hash('sha512', $result[0]['password'] . $_SERVER['HTTP_USER_AGENT']) == $currentSession->get('user.sessionString')) {
				return true;
			}
		 }
	 }
?>

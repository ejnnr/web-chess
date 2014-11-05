<?php
	include_once 'SecureSessionHandler.php'
	
	/********************
	 * function login
	 * returns true if the username-password combination is correct and sets $_SESSION values
	 *******************/
	
	function login($username, $password, $PDOHandle, $currentSession) {
		$stm = $PDOHANDLE->prepare('SELECT id, username, password, salt FROM users WHERE username = :username LIMIT 1;');
		$stm->bindParam(':username', $username);
		$stm->execute();
		$stm->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stm->fetch();
		
		$password = hash('sha512', $password . $result['salt']);
		if ($password == $result['password']) {
			$currentSession->put('user.id', $result['id']);
			$currentSession->put('user.name', $result['username']);
		}
	}
?>

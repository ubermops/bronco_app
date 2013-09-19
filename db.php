<?php

include_once 'constants.php';

class DB{
	var $connection;
	
	/*	Function:		createDB 
	 *	Parameters:		<none>
	 *	Returns:			phpDBO Object
	 *	author:			issiah
	 *
	 *	This function will instantiate the $connection
	 *	variable or die trying.
	 */
	function DB(){
		try{
			$this->connection = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,DB_USER,DB_PASS);
		
			$this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			die("Error Connecting to Database");
		}
	}
	
	/*  Mutators	
	*
	* $table = 'tablename'
	* $rownames = ['rowname1','rowname2']
	* $values = ['val1','val2']
	*/
	
	function prefix_strs($prefix,$strs){
		foreach ($strs as &$str)
			$str = $prefix.$str;
			
		return $strs;
	}
	
	/*
	function parse_conditon($condition_tree){
		foreach($condition_tree as &$condition)
			if(!is_string($condition))
				$condition = parse_condition($condition);
		
		if(sizeof($condition_tree) < 2){
			if(is_string($condition_tree)){
				$condition_tree
			}
		}
		
		while (list($key, $val) = each($condition_tree)) {
			echo "$key => $val\n";
		}
	
	}*/
	
	
	function insert($table, $rowname_values){
		$rownames = array_keys($rowname_values);
		$rows = implode(',',$rownames);
		$prefixed_rows = implode(',',prefix_strs(':',$rownames));
		
		$queryText = 'INSERT INTO '.$table.' ('.$rows.') VALUES ('.$prefixed_rows.')';
		$query = $this->connection->prepare($queryText);
		$query->execute(array_combine($prefixed_rows));
	}
	/*
	function select($table, $rownames ,$con){
		
		$queryText = 'SELECT '.implode(',',$rownames).' FROM '.$table.' ('..') VALUES ('.implode(',',prefix_strs(':',$values)).')';
	}
	
	function insert($table, $rownames ,$values){
		$queryText = 'INSERT INTO '.$table.' ('.implode(',',$rownames).') VALUES ('.implode(',',prefix_strs(':',$values)).')';
	}
	*/
	
	
	/*	Function:		fb_sign_in
	 *	Parameters:		
					uid: user's account uid
					fb_uid: user's facebook account uid
		Returns: <none>
	 *	author:			issiah
	 *
	 *	This function will be ran on fb sign in and will record new facebook accounts
	 *
	 */
	function facebook_sign_in($uid, $fb_uid){
		$queryText = 'INSERT INTO facebook_accounts (uid, fb_uid) VALUES (:uid, :fb_uid)';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':uid'=>$uid, ':fb_uid'=>$fb_uid));
	}
	
	/*	Function:		facebook_get_uid
	 *	Parameters:		
	 *					uid: User's account uid
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will insert the account data into the database
	 *
	 */
	function facebook_get_uid($uid){
		$queryText = 'SELECT facebook_uid FROM facebook_accounts WHERE uid = :uid';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':uid'=>$uid));
		
		return $query;
	}
	
	/*	Function:		user_get_contact
	 *	Parameters:		
	 *					uid: User's account uid
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will return the contact information associated with the account uid
	 *
	 */
	function user_get_contact($uid){
		$queryText = 'SELECT first_name, last_name FROM user_accounts WHERE Uid = :Uid';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':Uid'=>$uid));
		
		return $query;
	}
	
	/*	Function:		user_add_contact
	 *	Parameters:		
	 *					uid: User's account uid
	 *					contact_uid: The account uid of the contact to be added
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will add a contact to a user's contact list
	 *
	 */
	function user_add_contact($uid, $contact_uid){
		$queryText = 'INSERT INTO user_contacts (uid, contact_uid) VALUES (:uid, :contact_uid)';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':uid'=>$uid, ':contact_uid'=>$contact_uid));
	}
	
	/*	Function:		user_make_account
	 *	Parameters:
	 *				name: user's first name
	 *				last: user's last name
	 *				email: user's email address
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will insert the new account data into the database and return its associated uid
	 *
	 */
	function user_make_account($first_name, $last_name, $email){
		$queryText = 'INSERT INTO user_accounts (first_name, last_name, email) VALUES (:first_name, :last_name, :email)';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':first_name'=>$first_name, ':last_name'=>$last_name, ':email'=>$email));
		
		return array("uid"=>strval($this->connection->lastInsertId()));
	}
	
	/*	Function:		user_get_contacts
	 *	Parameters:		
	 *					uid: The user's account uid
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will get all of the user's contacts
	 *
	 */
	function user_get_contacts($uid){
		$queryText = 'SELECT user_accounts.first_name, user_accounts.last_name FROM user_accounts INNER JOIN user_contacts ON user_contacts.uid = :uid AND user_contacts.contact_uid = user_accounts.Uid';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':uid'=>$uid));
		
		return $query;
	}
	
	/*	Function:		user_get_notifications
	 *	Parameters:		
	 *					reciever_uid: The user's account uid, who is recieving the notification
	 *					tag: The tag that is associated with the notification(notification type)
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will return the user's notifications of a certain type
	 *
	 */	
	function user_get_notifications($reciever_uid, $tag){
		$queryText = 'Select user_notifications.sender_uid, user_notifications.reciever_uid, user_notifications.message, user_notifications.time,
			user_accounts.first_name as sender_first_name, user_accounts.last_name as sender_last_name
			FROM user_notifications INNER JOIN user_accounts WHERE reciever_uid = :reciever_uid AND message_tag = :message_tag ORDER BY time DESC';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':reciever_uid'=>$reciever_uid, ':message_tag'=>$tag));
		
		return $query;
	}
	
	/*	Function:		user_get_all_notifications
	 *	Parameters:		
	 *					reciever_uid: The user's account uid, who is recieving the notification
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will return all of the user's notifications
	 *
	 */	
	function user_get_all_notifications($reciever_uid){
		$queryText = 'Select user_notifications.sender_uid, user_notifications.reciever_uid, user_notifications.message_tag, user_notifications.message, user_notifications.time,
			user_accounts.first_name as sender_first_name, user_accounts.last_name as sender_last_name
			FROM user_notifications INNER JOIN user_accounts WHERE reciever_uid = :reciever_uid ORDER BY time DESC';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':reciever_uid'=>$reciever_uid));
		
		return $query;
	}
	
	/*	Function:		user_push_notifications
	 *	Parameters:		
	 *					reciever_uid: The user's account uid, who is recieving the notification
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will push a notifcation from the sender's account to the reciever's account
	 *
	 */	
	function user_push_notification($sender_uid, $reciever_uid, $message_tag, $message){
		$queryText = 'INSERT INTO user_notifications (sender_uid, reciever_uid, message_tag, message) VALUES (:sender_uid, :reciever_uid, :message_tag, :message)';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':sender_uid'=>$sender_uid, ':reciever_uid'=>$reciever_uid, ':message_tag'=>$message_tag, ':message'=>$message));
	}
	
	/*	Function:		user_store_request
	 *	Parameters:		
	 *					uid: The user's account uid, who is recieving the notification
	 *					request: The request as a JSON string
	 *	Returns:			phpDBOStatement Object
	 *	author:			issiah
	 *
	 *	This function will store data about any request sent to the server
	 *
	 */	
	function user_store_request($uid, $request){
		$queryText = 'INSERT INTO requests (sender_uid, request) VALUES (:sender_uid, :request)';
		
		$query = $this->connection->prepare($queryText);
		$query->execute(array(':sender_uid'=>$uid, ':request'=>$request));
	}
}

?>

<?php
	/*
	action:
		make_account
	params:
		first_name: User's first name that will be associated with the account.
		last_name: User's last name that will be associated with the account.
		email: User's email that will be associated with the account.
	return:
		SINGLE ROW
		uid: User's unqiue id for their account
	*/
	function user_make_account($params){
		$result = $params['db']->user_make_account( $params['first_name'],
			$params['last_name'], $params['email'] );
		
		return json_encode($result);
	}
	
	/*
	action:
		get_contact
	params:
		uid: The uid of the contact whose information is being queried.
	return:
		SINGLE ROW
		first_name: User's first name.
		last_name: User's last name.
		email: User's email.
	*/
	function user_get_contact($params){
		$result = $params['db']->user_get_contact($params['uid'])->fetchAll(PDO::FETCH_ASSOC);
		
		return json_encode($result);
	}
	
	/*
	action:
		add_contact
	params:
		uid: The uid of the contact who will be added to the requester's contact list.
		message: The message that the added user will see from the requester.
	return:
		<none>
	*/
	function user_add_contact($params){
		$params['db']->user_add_contact($params['user_data']['uid'], $params['uid']);
		
		$params['db']->user_push_notification($params['user_data']['uid'], $params['uid'], 'user_add_contact', $params['message']);
	}
	
	
	/*
	action:
		get_contacts
	params:
		<none>
	return:
		MULTIPLE ROWS
		first_name: User's first name.
		last_name: User's last name.
		email: User's email.
	*/
	function user_get_contacts($params){
		$result = $params['db']->user_get_contacts($params['user_data']['uid'])->fetchAll(PDO::FETCH_ASSOC);;
		
		return json_encode($result);
	}
	
	
	/*
	action:
		get_notifications
	params:
		message_tag: Only messages with this tag will be returned.
	return:
		MULTIPLE ROWS
		id: The notification's unique id.
		sender_uid: The user's unqiue id, who pushed the notification.
		sender_first_name: The user's first name, who pushed the notification.
		sender_last_name: The user's last name, who pushed the notification.
		reciever_uid: The user's unique id, who the notification was sent to.
		message: The message sent with the notification.
		time: The time the notification was pushed.
	*/
	function user_get_notifications($params){
		$result = $params['db']->user_get_notifications($params['user_data']['uid'],$params['message_tag'])->fetchAll(PDO::FETCH_ASSOC);	
		
		return json_encode($result);
	}
	
	/*
	action:
		get_all_notifications
	params:
		<none>
	return:
		MULTIPLE ROWS
		id: The notification's unique id.
		sender_uid: The user's unqiue id, who pushed the notification.
		reciever_uid: The user's unique id, who the notification was sent to.
		message_tag: The tag that is associated with the notification(the message type).
		message: The message sent with the notification.
		time: The time the notification was pushed.
	*/
	function user_get_all_notifications($params){
		$result = $params['db']->user_get_all_notifications($params['user_data']['uid'])->fetchAll(PDO::FETCH_ASSOC);

		return json_encode($result);
	}
?>
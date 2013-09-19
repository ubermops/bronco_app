<?php
	/*
	action:
		facebook_sign_in
	params:
		facebook_uid: User's facebook account uid.
	return:
		<none>
	*/
	function facebook_sign_in($params){
		$params['db']->facebook_sign_in($params['user_data']['uid'], $params['facebook_uid']);
	}
	
	/*
	action:
		facebook_get_uid
	params:
		<none>
	return:
		facebook_uid: User's facebook account uid
	*/
	function facebook_get_uid($params){
		$result = $params['db']->facebook_get_uid($params['user_data']['uid'])->fetchAll(PDO::FETCH_ASSOC);
		
		return json_encode($result);
	}
	
	/*
	action:
		facebook_get_uid
	params:
		uid: The account uid of the person recieving the request
		message: The message associated with the notification that is sent to user recieving the request
	return:
		facebook_uid: The user's, who is recieving the request, facebook account uid
	*/
	function facebook_friend_request($params){
		$params['db']->user_push_notification($params['user_data']['uid'], $params['uid'], 'facebook_friend_request', $params['message']);
	
		$result = $params['db']->facebook_get_uid($params['uid'])->fetchAll(PDO::FETCH_ASSOC);
		
		return json_encode($result);
	}
?>
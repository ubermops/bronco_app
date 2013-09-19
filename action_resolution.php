<?php
	header('Access-Control-Allow-Origin: *');
	
	include_once 'db.php';
	
	//handle for database
	 $mydb = new DB();
	 
	 
	 /*
	 need:
	 security
	 look into analytics on client/make sure server analytics is covered
	 gps system(for "security")*cough*analytics*cough*
	 
	 */
	
	//ADD PIPING MECHANIC for inter-action communication?
	//
	
	$request = json_decode(&$_POST['request'], true);
	
	if(!empty($request)){
		//validation & sanitation
		//record requests data
		$mydb->user_store_request(&$request['user_data']['uid'], &$_POST['request']);
		
		//break down request into actions and process
		$output ='[';

		foreach ($request['actions'] as $i=>&$action){
			if($i>=MAX_ACTIONS)
				break;
		
			$action['params']['db']=&$mydb;
			$action['params']['user_data']=&$request['user_data'];
			
			include_once($action['module'].'.php');
			//might want to use buffer instead of return values(less read/writes/duplicate data)
			$output.=call_user_func_array($action['module'].'_'.$action['action'], array(&$action['params'])).',';
		}

		$output = rtrim($output,',');
		
		echo $output.']';
	}
?>
<?php 

function check_login(){
	global $conn;
	$post_data  = $_POST;
	$username  = (isset($post_data['email'])) ? $post_data['email'] : "";
	$password  = (isset($post_data['password'])) ? md5($post_data['password']) : "";

	$get_plan_details_query = check_user_login($username, $password);
	$aps_result = $conn->query($get_plan_details_query);
	$output = array();	
	if($aps_result->rowCount()){
		$row_data = $aps_result->fetch(PDO::FETCH_ASSOC);

		if($row_data['user_status'] == '1'){
			$output = array(
				"success"=> 'custom_success',
				"message"=> 'user_found',
				"data"=> $row_data,
			);
		} else {
			$output = array(
				"success"=> 'error',
				"message"=> 'user_inactive'
			);
		}
	} else {
		$output = array(
			"success"=> 'error',
			"message"=> 'invalid_credits'
		);
	}
	echo json_encode($output); 
	die;
}

function get_all_depatments(){
	global $conn;
	$sql = get_all_depatments_query();
	$result = $conn->query($sql);
	$dept_dataset = $result->fetchAll();	
	if (count($dept_dataset) > 0) {
		$output = "";
		foreach($dept_dataset as $row) {
			$temp = array(
				"id"=>$row['id'],
				"name"=>utf8_encode(ucfirst($row['name'])),
				"description"=>utf8_encode(ucfirst($row['name'])),
			);
			$output[] = $temp;
		}
		// print_r($output); die;
		return json_encode(array("data"=>$output));

	} else {
		return json_encode(array("data"=>'none'));
	}

}


?>
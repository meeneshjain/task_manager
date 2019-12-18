<?php 


// database functions
// set SQL modes 
try{
	$conn->query("SET global sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
	
}  catch(PDOException $e) {
	// echo $e->getMessage(); 
}

/*function is_sfdc_installed(){
	global $conn;
	$query = check_sfdc_installed_query();
	$result = $conn->query($query);
	$installed_prod_data = $result->fetchAll();	
	if (count($installed_prod_data) > 0) {
		return 1;
	} else {
		return 0;
	}
}*/


//  Data functions 
/*function get_resources_task_priority($presentation_type = NULL, $default_work_center ){
	$get_all_resources  = get_all_resources($default_work_center);
	$get_task_type      = get_task_type();
	$priority_list      = priority_list();
	$work_centers       = get_work_centers($default_work_center);
	$work_order         = get_work_order();

	$get_all_resources  = json_decode($get_all_resources, true);
	$get_task_type      = json_decode($get_task_type, true);
	$priority_list      = json_decode($priority_list, true);
	$work_centers       = json_decode($work_centers, true);
	$work_order         = json_decode($work_order, true);

	$get_all_resources  = $get_all_resources['data'];
	$get_task_type      = $get_task_type['data'];
	$priority_list      = $priority_list['data'];
	$work_centers       = $work_centers['data'];
	$work_order         = $work_order['data'];

	return  json_encode(
		array(
			"resources"      => $get_all_resources, 
			"task_type"      => $get_task_type, 
			"priority_list"  => $priority_list,
			"work_centers"   => $work_centers,
			"work_order"     => $work_order,
		) 
	);
}*/

function get_resource_detail(){
	global $conn;
	$get_data = $_GET;
	if(isset($get_data) && $get_data!= ""){
		$resource_id = (isset($get_data['display_type']) && $get_data['display_type']!= "") ? $get_data['display_type'] : "";
		$sql = get_resource_info($resource_id);
		// echo $sql; die;
		$result = $conn->query($sql);
		if($result){
			$resource_row = $result->fetch(PDO::FETCH_ASSOC);
			if (count($resource_row) > 0) {
				$output = array("success"=>"success", "message"=>"no_data_found", "data"=> $resource_row);
			} else {
				$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
			}
		} else {
			$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
		}
	} else {
		$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
	}

	return json_encode($output);
}

function get_project_detail(){
	global $conn;
	$get_data = $_GET;
	if(isset($get_data) && $get_data!= ""){
		$project_id = (isset($get_data['display_type']) && $get_data['display_type']!= "") ? $get_data['display_type'] : "";
		$sql = get_project_info($project_id);
		// echo $sql; die;
		$result = $conn->query($sql);
		if($result){
			$project_row = $result->fetch(PDO::FETCH_ASSOC);
			if (count($project_row) > 0) {
				$output = array("success"=>"success", "message"=>"no_data_found", "data"=> $project_row);
			} else {
				$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
			}
		} else {
			$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
		}
	} else {
		$output = array("success"=>"error", "message"=>"no_data_found", "data"=>'none');
	}

	return json_encode($output);
}

function get_all_projects(){
	global $conn;
	$sql = all_project_query();
	$result = $conn->query($sql);
	if($result){

		$projects_dataset = $result->fetchAll();	
		if (count($projects_dataset) > 0) {
			$output = "";
			foreach($projects_dataset as $row) {
				$temp = array(
					"id"                   =>     $row['id'],
					"dept_id"              =>     $row['dept_id'],
					"code"                 =>     ucfirst($row['code']),
					"name"                 =>     ucfirst($row['name']),
					"parent_project_id"    =>     ($row['parent_project_id'] != "") ? $row['parent_project_id'] : "N/A",
					"status"               =>     ($row['status'] == "1") ? 'Active' : "Inactive",
					"parent_project_name"  =>     ($row['parent_project_name'] != "") ? ucfirst($row['parent_project_name']) : "N/A",
					"department_name"      =>     ($row['department_name'] != "") ? ucfirst($row['department_name']) : "N/A",
				);
				$output[] = $temp;
			}

			return json_encode(array("success"=>"success", "message"=>"data_found", "data"=>$output));

		} else {
			return json_encode(array("success"=>"error", "message"=>"major_error", "data"=>'none'));
		}
	} else {
		return json_encode(array("success"=>"error", "message"=>"no_data_found", "data"=>'none'));
	}
}

function get_all_resources($department_id = null){
	global $conn;
	$sql = all_resource_query($department_id);
	$result = $conn->query($sql);
	if($result){
		$resource_dataset = $result->fetchAll();	
		if (count($resource_dataset) > 0) {
			$output = "";
			foreach($resource_dataset as $row) {
				$temp = array(
					"id"                   =>     $row['id'],
					"dept_id"              =>     $row['dept_id'],
					"name"                 =>     ucfirst($row['res_name']),
					"key"                 =>     ucfirst($row['res_name']),
					"department_name"      =>     ($row['department_name'] != "") ? ucfirst($row['department_name']) : "N/A",
					"status"               =>     ($row['status'] == "1") ? 'Active' : "Inactive",
				);
				$output[] = $temp;
			}
			return json_encode(array("success"=>"success", "message"=>"data_found", "data"=>$output));
		} else {
			return json_encode(array("success"=>"error", "message"=>"major_error", "data"=>'none'));
		}
	}  else {
		return json_encode(array("success"=>"error", "message"=>"no_data_found", "data"=>'none'));
	}
}

function get_major_task_list(){

	global $conn;
	$ref_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	$sql = get_major_task_list_query($ref_id);
	// echo $sql; die;
	$result = $conn->query($sql);
	if($result){
		$resource_dataset = $result->fetchAll();	
		if (count($resource_dataset) > 0) {
			$output = "";
			foreach($resource_dataset as $row) {
				$temp = array(
					"id"=> $row['id'],
					"resource_id"=> $row['resource_id'],
					"resource_name"=> $row['resource_name'],
					"operation_name"=> $row['operation_name'],
					"description"=> $row['description'],
				);
				$output[] = $temp;
			}
			return json_encode(array("success"=>"success", "message"=>"data_found", "data"=>$output));
		} else {
			return json_encode(array("success"=>"error", "message"=>"major_error", "data"=>'none'));
		}
	}  else {
		return json_encode(array("success"=>"error", "message"=>"no_data_found", "data"=>'none'));
	}

}

function get_resources_task_priority($presentation_type = NULL, $current_department ){
	$ref_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	$get_all_resources  = get_all_resources($current_department);
	$get_task_type      = get_task_type();
	$get_category       = get_category(); 
	$priority_list      = priority_list();
	$departments        = get_all_depatments();
	$projects           = get_all_projects();
	$status             = get_task_status();
	$major_task         = get_major_task_list($ref_id);

	$get_all_resources  = json_decode($get_all_resources, true);
	$get_task_type      = json_decode($get_task_type, true);
	$get_category       = json_decode($get_category, true);
	$priority_list      = json_decode($priority_list, true);
	$departments        = json_decode($departments, true);
	$projects           = json_decode($projects, true);
	$status             = json_decode($status, true);
	$major_task         = json_decode($major_task, true);

	$get_all_resources  = $get_all_resources['data'];
	$get_task_type      = $get_task_type['data'];
	$get_category       = $get_category['data'];
	$priority_list      = $priority_list['data'];
	$departments        = $departments['data'];
	$projects           = $projects['data'];
	$status             = $status['data'];
	$major_task         = $major_task['data'];

	return  json_encode(
		array(
			"resources"      => $get_all_resources, 
			"task_type"      => $get_task_type, 
			"category"       => $get_category, 
			"priority_list"  => $priority_list,
			"departments"    => $departments,
			"projects"       => $projects,
			"status"         => $status,
			"major_task"     => $major_task,
		) 
	);
}


function get_operations($presentation_type = NULL, $work_center = NULL){
	global $conn;
	global $db_date_format;
	if(isset($_GET['action']) && $_GET['action']!=""){
		$start_date = date($db_date_format, strtotime($_GET['from_date']));
		$end_date   = date($db_date_format, strtotime($_GET['to_date']));
		
	} else {

		$range_for_data  =(6 - date('w'));

		$start_date = date($db_date_format, strtotime("-". $range_for_data ." days"));
		$end_date = date($db_date_format, strtotime("+". $range_for_data ." days"));
	}

	$ref_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	
	$task_data = "";
	
	$select_operation = get_aps_operations($start_date, $end_date, $ref_id, $work_center);
	// echo '<pre>'. $select_operation; die;
	$select_operation_result = $conn->query($select_operation);
	$operation_dataset = $select_operation_result->fetchAll(PDO::FETCH_ASSOC);
	if (count($operation_dataset) > 0) {
		$task_links = "";
		$open = "true";
		$get_limited_links = array();
		$available_wo = array();
		foreach($operation_dataset as $row) {
			if($work_center!=""){
				$get_limited_links[] = (int)$row['id'];
			}
			$readonly = "";
			if($row['readonly'] == "true"){
				$readonly = 1;
			} else {
				$readonly = 0;
			}
		
			$operation_type = $row['operation_type'];
			if($operation_type == 'operation'){
				if($row['parent_opr_id'] == 0 || $row['parent_opr_id'] == "") {
					$operation_type = 'project';
				} 
			}

			$start_date = change_date_format2($row["start_date"], 2, 1);
			$end_date = change_date_format2($row["end_date"], 2, 1);

			$planned_start_date = change_date_format2($row["planned_start_date"], 2, 1);
			$planned_end_date = change_date_format2($row["planned_end_date"], 2, 1);

			$start_date_time = (change_date_format2($row["start_date"], 2, 0) . ' '. $row['start_time']);
			$end_date_time = (change_date_format2($row["end_date"], 2, 0) . ' '. $row['end_time']);
			$planned_start_date_time = (change_date_format2($row["planned_start_date"], 2, 0) . ' '. $row['planned_start_time']);
			$planned_end_date_time = (change_date_format2($row["planned_end_date"], 2, 0) . ' '. $row['planned_end_time']);

			$data_temp = array(
				"id"                       => (int)$row['id'], 
				"ref_id"                   => (int)$row['ref_id'], 
				"parent_opr_id"            =>  ($operation_type =="operation" || $operation_type == 'task') ? $row["parent_opr_id"] : (int)0,
				"parent"                   => (int)$row['parent_opr_id'], 
				"operation_type"           =>  $operation_type,
				"project_id"                =>  $row["project_id"],
				"project"                  =>  $row["project_id"],
				"type"                     =>  $operation_type,
				"task_type"                =>  $operation_type,
				"category"                 =>  $row["category"],
				"resource_id"              =>  $row["resource_id"],
				"resource"                 =>  $row["resource_id"],
				"resource_name"            =>  $row["resource_name"],
				"operation_name"           =>  $row["operation_name"],
				"text"                     =>  $row["operation_name"],
				"description"              =>  $row["description"],
				"delay_reason"             =>  $row["delay_reason"],
				"priority"                 =>  (int) $row["priority"],
				"readonly"                 =>  (boolean)$row["readonly"],
				"open"                     =>  (boolean)1,
				"is_saved"                 => (int)1,
				"status"                   =>  ($row['status']) ? $row['status'] : (int)0,
				/*"start_date"               =>  change_date_format($row["start_date"]),
				"end_date"                 =>  change_date_format($row["end_date"]),
				"planned_start_date"       =>  ($row["planned_start_date"]),
				"planned_end_date"         =>  ($row["planned_end_date"]),*/
				"start_date"               =>  $start_date_time,
				"end_date"                 =>  $end_date_time,
				"planned_start_date"       =>  $planned_start_date_time,
				"planned_end_date"         =>  $planned_end_date_time,
				"extra_start_date"         =>  $start_date,
				"extra_start_time"         =>  $row['start_time'],
				"extra_end_date"           =>  $end_date,
				"extra_end_time"           =>  $row['end_time'],
				"extra_planned_start_date" =>  $planned_start_date,
				"extra_planned_start_time" =>  $row['planned_start_time'],
				"extra_planned_end_date"   =>  $planned_end_date,
				"extra_planned_end_time"   =>  $row['planned_end_time'],
				"duration"                 => ((int)$row['duration']!= 0 && $row['duration']!="") ? (int) $row['duration'] : (int) $row['min_duration'], 
				"min_duration"             =>  (int)$row['min_duration'], 
				"progress"                 =>  (float)$row["progress"],
				"is_deleted"               =>  $row["is_deleted"],
				"split_task_grp_id"        =>  (int)$row["split_task_grp_id"],
				"required_resource"        =>  trim($row["required_resource"]),
			);

			if(isset($data_temp) && $data_temp!=""){
				$task_data[] = $data_temp;
			}
		}

		// task links 
	//	if($presentation_type == "gantt_chart"){

		$operation_ids = "";
		if(!empty($get_limited_links)){
			$get_limited_links = array_unique($get_limited_links);
			$operation_ids = "'".implode("','", $get_limited_links)."'";

		}

		$select_operation_links = gant_chart_links_query($operation_ids, $ref_id); 
		//	  echo $select_operation_links; die;
		$select_operation_links_res = $conn->query($select_operation_links);
		$operation_link_dataset = $select_operation_links_res->fetchAll();
		if (count($operation_link_dataset) > 0) {
			foreach($operation_link_dataset as $link_row) {
				$link_temp = array(
					"id"             => "link-".$link_row['id'],
					"ref_id"        => $link_row['ref_id'],
					"is_saved"       => 1,
					"source"         => $link_row['source'],
					"target"         => $link_row['target'],
					"type"           => (int)$link_row['connection_type'],
					"link_id"        => $link_row['id'],

				);	

				$task_links[] = $link_temp;
			}
		}
	//	}

		return json_encode(
			array(
				"data"=>$task_data, 
				"links"=> $task_links, 
			)
		);
	} else {
		return json_encode(array("data"=>'none'));
	}

	exit;
	
}

function save_update_task(){
	global $conn;
	$post_data  = $_POST;
	// print_r($post_data); die;
	$task_data  = (isset($post_data['task_data'])) ? $post_data['task_data'] : "";
	$link_data  = (isset($post_data['links'])) ? $post_data['links'] : "";
	$from_date  = (isset($post_data['from_date'])) ? $post_data['from_date'] : "";
	$to_date  = (isset($post_data['to_date'])) ? $post_data['to_date'] : "";
	$reference_id  = (isset($post_data['reference_id'])) ? $post_data['reference_id'] : "";
	
	$count = 0;
	$new_tasks = array();
	$new_links = array();
	$links_new_soure_target = array();
	if($task_data!="") {
		$res_list = json_decode(get_all_resources(), true);

		foreach($task_data as $key => $tasks){
			// print_r($tasks);
			if(!isset($tasks['$virtual'])){
				/*$start_date = (isset($tasks['start_date']) && $tasks['start_date']!="") ? explode("(", $tasks['start_date'])[0]: "";
				$end_date   = (isset($tasks['start_date']) && $tasks['start_date']!="") ? explode("(",$tasks['end_date'])[0]: "";

				$planned_start_date = (isset($tasks['planned_start_date']) && $tasks['planned_start_date']!="") ? explode("(", $tasks['planned_start_date'])[0]: "";
				$planned_end_date   = (isset($tasks['planned_start_date']) && $tasks['planned_start_date']!="") ? explode("(",$tasks['planned_end_date'])[0]: "";*/

				$tasks['readonly'] = (isset($tasks['readonly']) && $tasks['readonly']!='') ? $tasks['readonly'] : (int)0;
				$tasks['resource'] = (isset($tasks['resource']) && $tasks['resource']!="") ? $tasks['resource'] : (int)0;

				$start_date = (isset($tasks['start_date']) && $tasks['start_date']!="") ? explode("(", $tasks['start_date'])[0]: "";
				$start_time = (isset($tasks['extra_start_time']) && $tasks['extra_start_time']!="") ? $tasks['extra_start_time']: "";

				$end_date = (isset($tasks['extra_end_date']) && $tasks['extra_end_date']!="") ? explode("(", $tasks['extra_end_date'])[0]: "";
				$end_time = (isset($tasks['extra_end_time']) && $tasks['extra_end_time']!="") ? $tasks['extra_end_time']: "";

				$planned_start_date = (isset($tasks['extra_planned_start_date']) && $tasks['extra_planned_start_date']!="") ? explode("(", $tasks['extra_planned_start_date'])[0]: "";
				$planned_start_time = (isset($tasks['extra_planned_start_time']) && $tasks['extra_planned_start_time']!="") ? $tasks['extra_planned_start_time'] : "";

				$planned_end_date = (isset($tasks['extra_planned_end_date']) && $tasks['extra_planned_end_date']!="") ? explode("(", $tasks['extra_planned_end_date'])[0]: "";
				$planned_end_time = (isset($tasks['extra_planned_end_time']) && $tasks['extra_planned_end_time']!="") ? $tasks['extra_planned_end_time']: "";
				
				if($tasks['readonly'] == 'false'){
					$tasks['readonly'] = 0;
				} else { 
					$tasks['readonly'] = 1;
				}

				$resource_name = '';
				if($res_list['data']!= 'none'){
					foreach ($res_list['data'] as $val) {
						if($val['id'] == $tasks['resource']){
							$resource_name = $val['name'] ;
						}
					}	
				}

				$tasks['start_date'] = get_only_date($start_date, 2);
				$tasks['start_time'] = $start_time;

				$tasks['end_date'] = get_only_date($end_date, 2);
				$tasks['end_time'] = $end_time;

				$tasks['planned_start_date'] = get_only_date($planned_start_date, 2);
				$tasks['planned_start_time'] = $planned_start_time;

				$tasks['planned_end_date'] = get_only_date($planned_end_date, 2);
				$tasks['planned_end_time'] = $planned_end_time;



				$tasks['text'] = (isset($tasks['text']) && $tasks['text']!="") ? utf8_decode(escape_character($tasks['text'])) : "";
				$tasks['description'] = (isset($tasks['description']) && $tasks['description']!="") ? utf8_decode(escape_character($tasks['description'])) : '';

				$tasks['parent_opr_id'] = (isset($tasks['parent_opr_id']) && $tasks['parent_opr_id']!="") ? $tasks['parent_opr_id'] : "0";
				$tasks['priority'] = (isset($tasks['priority']) && $tasks['priority']!="") ? $tasks['priority'] : (int)99;
				$tasks['status']   = (isset($tasks['status']) && $tasks['status']!="") ? $tasks['status'] : (int)0;
				$tasks['category'] = (isset($tasks['category']) && $tasks['category']!="") ? $tasks['category'] : '';
				$tasks['operation_type'] = (isset($tasks['task_type']) && $tasks['task_type']!="") ? $tasks['task_type'] : 'operation';
				$tasks['project'] = (isset($tasks['project']) && $tasks['project']!="") ? $tasks['project'] : '';
				$tasks['resource_name'] = $resource_name;
				
				$tasks['duration'] = (isset($tasks['duration']) && $tasks['duration']!="") ? $tasks['duration'] : "";
				$tasks['min_duration']   = (isset($tasks['min_duration']) && $tasks['min_duration']!="") ? $tasks['min_duration'] : $tasks['duration'];
				$tasks['split_task_group_id']   = (isset($tasks['split_task_group_id']) && $tasks['split_task_group_id']!="") ? $tasks['split_task_group_id'] : (int)0;
				$tasks['dept_id']   = (isset($tasks['dept_id']) && $tasks['dept_id']!="") ? $tasks['dept_id'] : (int)0;
				$tasks['required_resource']   = (isset($tasks['required_resource']) && $tasks['required_resource']!="") ? $tasks['required_resource'] : (int)1;
				$result = "";
				if(isset($tasks['is_saved']) && $tasks['is_saved']== 1){
					
					$query = update_aps_operations($tasks, $tasks['id']);
					$result = $conn->query($query);
				} else {
					$parent_task  =$tasks['parent_opr_id'];		 
					$query  = insert_new_aps_opr($reference_id, $tasks);	
					$result = $conn->query($query); 	
					//$insert_id = $conn->lastInsertId();
					$get_aps_id_query = get_last_aps_id();
					$aps_result = $conn->query($get_aps_id_query);
					$insert_id = $aps_result->fetch(PDO::FETCH_ASSOC)['id'];

					$new_tasks[$tasks['id']] = array("current_id"=> $tasks['id'], "new_id"=> $insert_id);

					// generate lnk 
					if($tasks['operation_type'] !== 'project'){

					 if(isset($_GET['is_split']) && $_GET['is_split']==0){
							$pred_task_id = 0;
							$connection_type = 0;
						 	$check_link_query = "SELECT TOP 1 OPER_ID as \"id\", PARENT_OPR_ID FROM \"OPTM_APS_PRODOPER\" WHERE PARENT_OPR_ID = '$parent_task' AND OPER_ID != '$insert_id' AND REF_ID = '$reference_id' ORDER BY OPER_ID DESC ";
						 	// echo $check_link_query; die;
							$check_link_res = $conn->query($check_link_query);
							if($check_link_res->rowCount()){
								$sch_data = $check_link_res->fetch(PDO::FETCH_ASSOC);
								$pred_task_id = $sch_data['id'];
								$connection_type = 0;
							} else {
								$pred_task_id = $parent_task;
								$connection_type  = 1;
							}
							$succ_id = $insert_id; 
							
							$link_query = "( '$reference_id', '$pred_task_id', '$succ_id', '$connection_type' )";
							$query = insert_aps_links($link_query);
							$result = $conn->query($query);
							$get_opr_link_query = get_last_opr_link_id();
							$link_result = $conn->query($get_opr_link_query);
							$insert_link_id = $link_result->fetch(PDO::FETCH_ASSOC)['id'];

							$links_new_soure_target["link-".$insert_link_id] = array("source" => $pred_task_id, "target" => $succ_id, "type"=> $connection_type, "is_new" => 1, "link_id" => "link-".$insert_link_id);	 
					 	}
					}


				}

				if($result){
					$count++;
				}
				$result= "";
			}
		}
	}

	 // update links
	
	if($link_data!=""){

		// echo '<pre>';	print_r($link_data); die;
		foreach($link_data as $key => $links){
			if(isset($new_tasks[$links['source']]) && $new_tasks[$links['source']]!= ""){
				$links['source'] = $new_tasks[$links['source']]['new_id'];
			}
			
			if(isset($new_tasks[$links['target']]) && $new_tasks[$links['target']]!= ""){
				$links['target'] = $new_tasks[$links['target']]['new_id'];
			}

			if(isset($links['is_saved']) && $links['is_saved']== 1){
				$link = explode('link-', $links['id']);
				$query = update_aps_links($links, $link[1], $reference_id);
				$result = $conn->query($query);
				$links_new_soure_target[$links['id']] = array("source"=>$links['source'], "target"=>$links['target'], "is_new" => 0);	
				
			} else {
				// $ref_id = (isset($links['ref_id']) && $links['ref_id']!="") ? $links['ref_id'] : $reference_id;
				$link_query = "('$reference_id', '$links[source]', '$links[target]', '$links[type]' )";
				$query = insert_aps_links($link_query);
				$result = $conn->query($query);
				$get_opr_link_query = get_last_opr_link_id();
				$link_result = $conn->query($get_opr_link_query);
				$insert_id = $link_result->fetch(PDO::FETCH_ASSOC)['id'];


				$new_links[] = array("current_id"=> $links['id'], "new_id"=> $insert_id);
				$links_new_soure_target["link-".$insert_id] = array("source"=>$links['source'], "target"=>$links['target'], "is_new" => 0);	
			}
			
		}
	}

	if($count > 0){
		$update_ref = update_reference_status_query("draft", $reference_id);
		$update_ref_res = $conn->query($update_ref);
		$output = array("success"=>true, "data"=> $new_tasks, "links"=>$new_links, "new_source_target"=>$links_new_soure_target);
		// print_r($output); die;
		return json_encode($output);

	} else {
		return json_encode(array("success"=>false, "data"=> ""));

	}

}

/*function push_to_production(){
	global $conn;
	
	$reference_id = (isset($_POST['reference_id']) && $_POST['reference_id']!="") ? $_POST['reference_id'] : "";
	$select_operation = get_aps_operations("", "", $reference_id, '');
	//   echo '<pre>'. $select_operation; die;
	$select_operation_result = $conn->query($select_operation);
	$operation_dataset = $select_operation_result->fetchAll();
	$success_count = 0;
	if (count($operation_dataset) > 0) {
		foreach($operation_dataset as $row) {
			if($row['is_local_task'] != 1) {
				$row['start_time'] = change_date_to_int_time($row['start_date']);
				
				$row['end_time']   = change_date_to_int_time($row['end_date']);

				$row['readonly'] = (isset($row['readonly']) && trim($row['readonly'])!=='') ? $row['readonly']: '';
				$row['description'] = (isset($row['description']) && trim($row['description'])!=='') ? ($row['description']): '';

				$row['status'] = (isset($row['status']) && trim($row['status'])!=='') ? $row['status']: ''; 

				$row['duration'] = (isset($row['duration']) && trim($row['duration'])!=='') ? $row['duration']: '000';

				$update_query = update_task_query($row['operation_type'], $row);
			//	echo '<pre>'; print_r($update_query); 
				if($row['operation_type'] == 'project') {
					$update_query_res = $conn->query($update_query['update_prod_head']);
				} else if($row['operation_type'] == 'task') {
					$update_query_res = $conn->query($update_query['update_prod_oper']);
				}

				if($update_query_res->rowCount()) {
					$success_count++;
				}
				
			}
		}

		if($success_count > 0) {
			$update_ref = update_reference_status_query("pushed", $reference_id);
			$update_ref_res = $conn->query($update_ref);
			$output = array("success" => true, "data"=> array(), "links"=> array(), "new_source_target"=> array());
		} else {
			$output = array("success" => false, "data"=> array(), "links"=> array(), "new_source_target"=> array());	
		}
	} else {
		$output = array("success" => false, "data"=> array(), "links"=> array(), "new_source_target"=> array());
	}

	return json_encode($output);
}	*/


function save_update_project(){
	global $conn;
	$post_data  = $_POST;
	
	$project_name  = (isset($post_data['project_name'])) ? $post_data['project_name'] : "";
	$project_code  = (isset($post_data['project_code'])) ? $post_data['project_code'] : "";
	$parent_project  = (isset($post_data['parent_project'])) ? $post_data['parent_project'] : "";
	$project_department  = (isset($post_data['project_department'])) ? $post_data['project_department'] : "";
	$project_status  = (isset($post_data['project_status'])) ? $post_data['project_status'] : "";
	$is_new  = (isset($post_data['is_new'])) ? $post_data['is_new'] : "";
	$project_id  = (isset($post_data['project_id'])) ? $post_data['project_id'] : "";
	$insert_id = "";
	if($is_new == '1'){ // insert project 
		$insert_project_query = save_project($project_department, $project_code, $project_name, $parent_project, $project_status);
		// echo $insert_project_query; die;
		$insert_project_res = $conn->query($insert_project_query);

		$get_project_id_query = get_last_inserted_project_id();
		$project_result = $conn->query($get_project_id_query);
		$insert_id = $project_result->fetch(PDO::FETCH_ASSOC)['id'];
		$output = array(
			"success"    => 'success',
			"message"    => "project_inserted_sucessfully",
			"project_id" => $insert_id,
		);
	} else { // update project
		$update_project_query = update_project($project_id, $project_department, $project_code, $project_name, $parent_project, $project_status);
		$update_project_res = $conn->query($update_project_query);
		$output = array(
			"success"    => 'success',
			"message"    => "project_updated_sucessfully",
			"project_id" => $project_id,
		);
	}

	return json_encode($output); 
}

function save_update_resource(){
	global $conn;
	$post_data  = $_POST;
	$resource_name  = (isset($post_data['resource_name'])) ? $post_data['resource_name'] : "";
	$resource_department  = (isset($post_data['resource_department'])) ? $post_data['resource_department'] : "";
	$resource_status  = (isset($post_data['resource_status'])) ? $post_data['resource_status'] : "";
	$is_new  = (isset($post_data['is_new'])) ? $post_data['is_new'] : "";
	$resource_id  = (isset($post_data['resource_id'])) ? $post_data['resource_id'] : "";
	$insert_id = "";
	if($is_new == '1'){ // insert project 
		$insert_resource_query = save_resource($resource_department, $resource_name, $resource_status);
		
		$insert_resource_res = $conn->query($insert_resource_query);

		$get_resource_id_query = get_last_inserted_resource_id();
		$resource_result = $conn->query($get_resource_id_query);
		$insert_id = $resource_result->fetch(PDO::FETCH_ASSOC)['id'];
		$output = array(
			"success"    => 'success',
			"message"    => "resource_inserted_sucessfully",
			"resource_id" => $insert_id,
		);
	} else { // update project
	//	print_r($post_data);
		$update_resource_query = update_resource($resource_id, $resource_department, $resource_name, $resource_status);
		$update_resource_res = $conn->query($update_resource_query);
		$output = array(
			"success"    => 'success',
			"message"    => "resource_updated_sucessfully",
			"resource_id" => $resource_id,
		);
	}

	return json_encode($output); 
}

function delete_saved_task(){
	global $conn;
	
	$task_id = (isset($_GET['task_id']) && $_GET['task_id']!="") ? $_GET['task_id'] : "";
	$reference_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	$delete_task_queries = delete_task_query($task_id);
	
	$select_task = $delete_task_queries['select_task'];
	$select_task_res = $conn->query($select_task);
	if (count($select_task_res->fetch(PDO::FETCH_ASSOC)) > 0) {

		$del_link = (isset($_GET['delete_link']) && $_GET['delete_link']!="") ? $_GET['delete_link'] : "";
		if($del_link == 1){
			$delete_task_link = $delete_task_queries['delete_task_link'];
			$delete_task_link_res = $conn->query($delete_task_link);
		}	

		$delete_task = $delete_task_queries['delete_task'];	
		$delete_task_res = $conn->query($delete_task);

		if($delete_task_res->rowCount() > 0){
			$update_ref = update_reference_status_query("draft", $reference_id);
			$update_ref_res = $conn->query($update_ref);

			$output = array(
				"success" => 'custom_success',
				"message" => "task_is_deleted",
				"task_id"=>$task_id,
			);
		} else {
			$output = array(
				"success" => 'error',
				"message" => 'global_error_msg',
				"task_id"=>'',
			);	
		}

	} else {
		$output = array(
			"success" => 'error',
			"message" => 'no_such_opr_exists',
			"task_id"=>'',
		);
	}

	return json_encode($output);
}

function complete_selected_task(){
	global $conn;
	$task_id = (isset($_GET['task_id']) && $_GET['task_id']!="") ? $_GET['task_id'] : "";
	$reference_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	$completed_task_query = complete_task_query($task_id);
	$select_task = $completed_task_query['select_task'];
	$select_task_res = $conn->query($select_task);
	if (count($select_task_res->fetch(PDO::FETCH_ASSOC)) > 0) {
		
		$complete_task_res = $conn->query($completed_task_query['complete_task']);

		if($complete_task_res->rowCount() > 0){
			$complete_child_task_res = $conn->query($completed_task_query['complete_child_task']);

			$update_ref = update_reference_status_query("draft", $reference_id);
			$update_ref_res = $conn->query($update_ref);
			$output = array(
				"success" => 'custom_success',
				"message" => "task_is_completed",
				"task_id"=>$task_id,
			);
		} else {
			$output = array(
				"success" => 'error',
				"message" => 'global_error_msg',
				"task_id"=>'',
			);	
		}

	} else {
		$output = array(
			"success" => 'error',
			"message" => 'no_such_opr_exists',
			"task_id"=>'',
		);
	}
	return json_encode($output);
}

function delete_task_link(){
	global $conn;
	
	$link_id = (isset($_GET['link_id']) && $_GET['link_id']!="") ? $_GET['link_id'] : "";
	$reference_id = (isset($_GET['reference_id']) && $_GET['reference_id']!="") ? $_GET['reference_id'] : "";
	if($link_id!=""){

		$delete_task_link_queries = delete_task_link_query($link_id);
		// print_r($delete_task_link_queries); 

		$select_link = $delete_task_link_queries['select_link'];
		$select_link_res = $conn->query($select_link);
		if ($select_link_res) {
			$delete_task = $delete_task_link_queries['delete_link'];	
			$delete_task_res = $conn->query($delete_task);

			if($delete_task_res->rowCount() > 0){
				$update_ref = update_reference_status_query("draft", $reference_id);
				$update_ref_res = $conn->query($update_ref);
				$output = array(
					"success" => 'custom_success',
					"message" => "link_is_deleted",
					"link_id" => $link_id,
				);
			} else {
				$output = array(
					"success" => 'error',
					"message" => 'global_error_msg',
					"link_id" =>'',
				);	
			}
		} else {
			$output = array(
				"success" => 'error',
				"message" => 'no_such_link_exists',
				"link_id" =>'',
			);
		}
	} else {
		$output = array(
			"success" => 'error',
			"message" => 'link_id_missing',
		);
	}

	return json_encode($output);
}


function get_system_settings(){
	global $conn;
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$select_settings_query = system_setting_query();
	// echo $select_settings_query;
	$select_setting_res = $conn->query($select_settings_query);
	//print_r($select_setting_res);
	$setting_dataset = $select_setting_res->fetchAll(PDO::FETCH_ASSOC);
	// echo '<pre>';  print_r($setting_dataset); print_r($select_setting_res->fetchColumn());

	//die;
	if (count($setting_dataset) > 0) {
		$setting_data = array();
		foreach($setting_dataset as $setting_row) {

			$setting_value = $setting_row['value1']. ''. @$setting_row['value2'];
			if(is_numeric($setting_value)){
				$setting_value = (int)$setting_value;
			} 
			if(is_array(json_decode($setting_value, true))){
				$setting_value = json_decode($setting_value, true);
			}
			$setting_data[$setting_row['name'] ] = $setting_value;
		}	
		$output = array(
			"success" => 'custom_success',
			"message" => 'no_config_exist',
			"data"    => $setting_data
		);
	} else {
		$output = array(
			"success" => 'error',
			"message" => 'config_avail',
		);
	}

	return json_encode($output);
}

function save_system_settings(){
	global $conn;
	$post_data  = $_POST;
	// print_r($post_data);
	
	$succes_count = 0;
	foreach($post_data as $field_name => $field_value){
		if(is_array($field_value)){
			$field_value = json_encode($field_value);
		}
		$update_settings = update_system_settings($field_value, $field_name);
		$update_settings_res = $conn->query($update_settings);
		if($update_settings_res->rowCount() > 0){
			$succes_count++;
		}
	}
	if($succes_count > 0){
		$output = array(
			"success" => 'custom_success',
			"message" => 'config_saved',
		);
	} else {
		$output = array(
			"success" => 'error',
			"message" => 'global_error_msg',
		);
	}
	return json_encode($output);
}


function shift_capacity_details(){
	// echo '<pre>';
	global $conn;

	$get_data  = $_GET;
	$from_date  = (isset($get_data['from_date'])) ? $get_data['from_date'] : "";
	$to_date  = (isset($get_data['to_date'])) ? $get_data['to_date'] : "";
	$reference_id  = (isset($get_data['reference_id'])) ? $get_data['reference_id'] : "";
	$work_center = (isset($get_data['default_work_center']) && $get_data['default_work_center']!="") ? $get_data['default_work_center'] : "";

	$from_date = (isset($from_date) && $from_date!="") ? explode("(", $from_date)[0]: "";
	$to_date   = (isset($to_date) && $to_date!="") ? explode("(",$to_date)[0]: "";

	// echo '<br>';
	$from_date = change_db_date_format($from_date);
	$to_date = change_db_date_format($to_date);

	$capacity_query = shift_capacity_details_query(get_only_date($from_date), get_only_date($to_date), $reference_id, $work_center);
	// print_r($capacity_query); die;
	// echo '<br>'. $capacity_query['total_avail_res_query'];  
	$total_resource_res = $conn->query($capacity_query['total_res_query']);
	$total_res_dataset = $total_resource_res->fetchAll();
	
	$total_res_data = array();
	if (count($total_res_dataset) > 0) {
		foreach($total_res_dataset as $total_row) {
			$total_row['shift_id'] = utf8_encode($total_row['shift_id']);
			$total_row[0] = utf8_encode($total_row[0]);
			$total_row['work_center_id'] = utf8_encode($total_row['work_center_id']);
			$total_row[2] = utf8_encode($total_row[2]);
			$total_row['resource'] = utf8_encode($total_row['resource']);
			$total_row[3] = utf8_encode($total_row[3]);
			if(!isset($total_res_data[$total_row['work_center_id']]) || (!isset($total_res_data[$total_row['work_center_id']][$total_row['resource']]))) {
				$total_res_data[$total_row['work_center_id']][$total_row['resource']] = 0;
			}
			($total_res_data[$total_row['work_center_id']][$total_row['resource']]=="")? $total_res_data[$total_row['work_center_id']][$total_row['resource']]=+1 : $total_res_data[$total_row['work_center_id']][$total_row['resource']]++;
			
		}
	}

	// echo '<pre>'; print_r($total_res_data);
	// echo '<br>'.  $capacity_query['total_avail_res_query'];//  die;
	$total_avail_res = $conn->query($capacity_query['total_avail_res_query']);
	$total_avail_dataset = $total_avail_res->fetchAll();
	
	$total_avail_data = array();
	$total_bal_data = array();
	if (count($total_avail_dataset) > 0) {
		
		foreach($total_avail_dataset as $avail_row) {
			$avail_row['shift_id'] = utf8_encode($avail_row['shift_id']);
			$avail_row[0] = utf8_encode($avail_row[0]);
			$avail_row['work_center_id'] = utf8_encode($avail_row['work_center_id']);
			$avail_row[5] = utf8_encode($avail_row[5]);
			$avail_row['resource'] = utf8_encode($avail_row['resource']);
			$avail_row[6] = utf8_encode($avail_row[6]);
			$temp_shift_array = array();
			if(!isset($total_res_data[$total_row['work_center_id']])) {
				$total_avail_data[$avail_row['work_center_id']][$avail_row['resource']] = 0;
			}

			if($avail_row['shift_start']!=""){
				$shift_start_val = $avail_row['shift_start'];
			} else {
				$shift_start_val = str_pad(0, 3, 0, STR_PAD_LEFT);
			}

			for($j = 0; $j <= round($avail_row['avail_capacity']); $j++){

				$new_start_time = ($shift_start_val + $j*100);
				if($new_start_time <= 900){
					$new_start_time = "0".$new_start_time; 
				} 
				$temp_shift_array[$new_start_time] = ($new_start_time != $avail_row['break_start_time']) ? $total_res_data[$avail_row['work_center_id']][$avail_row['resource']] : 0;
			}
			$total_avail_data[$avail_row['work_center_id']][$avail_row['resource']] = $temp_shift_array;
			$total_avail_data[$avail_row['work_center_id']][$avail_row['resource']]['break_time']= $avail_row['break_start_time'];

			$temp_bal_array = array();
			if(!isset($total_res_data[$total_row['work_center_id']])) {
				$total_bal_data[$avail_row['work_center_id']][$avail_row['resource']] = 0;
			}

			for($j = 0; $j <= round($avail_row['avail_capacity']); $j++){
				$new_start_time_2 =($shift_start_val + $j*100);

				$temp_bal_array[$new_start_time_2] = ($new_start_time_2 != $avail_row['break_start_time']) ? $total_res_data[$avail_row['work_center_id']][$avail_row['resource']] : 0;
			}
			$total_bal_data[$avail_row['work_center_id']][$avail_row['resource']] = $temp_bal_array;
		}

	}
	

	if(!empty($total_res_data) && !empty($total_avail_data)){
		$output = array(
			"success"=> 'custom_success',
			"message"=> 'caacity_found_msg',
			"total_resource" => $total_res_data, 
			'total_avail'=>$total_avail_data,
			'total_bal'=>$total_bal_data,
		);

	} else {
		$output = array(
			"success"=> 'none',
			"message"=> 'global_error_msg',
			"total_resource" => $total_res_data, 
			'total_avail'=>$total_avail_data
		);
	}
	// print_r($output); die;
	echo json_encode($output);
}

//  V0.1.2
function get_current_schedule_detail(){
	global $conn;
	$get_data  = $_GET;
	$reference_id  = (isset($get_data['reference_id'])) ? $get_data['reference_id'] : "";

	$get_plan_details_query = get_current_schedule_detail_query($reference_id);
	$aps_result = $conn->query($get_plan_details_query);
	if($aps_result->rowCount()){
		$sch_data = $aps_result->fetch(PDO::FETCH_ASSOC);
		$data = array(
			"sch_id"=> $sch_data['OPTM_REF_ID'], 
			"sch_name"=> $sch_data['OPTM_PLAN_NAME'], 
			"from_date"=> change_date_format_notime($sch_data['OPTM_FROM_DATE']), 
			"to_date"=> change_date_format_notime($sch_data['OPTM_TO_DATE']), 
			"current_status"=> ucfirst($sch_data['OPTM_SCHEDULING_STATUS']), 
		);

		$output = array(
			"success"=> 'custom_success',
			"message"=> 'current_ref_found',
			"data"=> $data,
		);
	} else{
		$output = array(
			"success"=> 'error',
			"message"=> 'global_error_msg'
		);
	}
	echo json_encode($output);


}

function get_login_service_URL()  {  
	global $conn;
	$aps_result = $conn->query(get_login_service_URL_query());

	if($aps_result->rowCount()){
		$sch_data = $aps_result->fetch(PDO::FETCH_ASSOC);
		$data = array(
			"url"=> $sch_data['OPTM_URL']
		);

		$output = array(
			"success"=> 'custom_success',
			"message"=> 'success',
			"data"=> $data,
		);
	} else{
		$output = array(
			"success"=> 'error',
			"message"=> 'global_error_msg'
		);
	}
	echo json_encode($output);
}
?>
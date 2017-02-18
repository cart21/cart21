<?php
class task_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
         
    }
    
   
   function get_all_task(){
   
	   $this->db->select("*")->from("task");
	   $this->db->join("task_order","task.task_id=task_order.task_id","left");
	   $this->db->where("finished",2);
	   $this->db->order_by("task_order.all_order","asc");
   
   
	return $this->db->get();
   }
   
   function tasks_finished(){
   
	return $this->db->where_not_in("finished",array(1,5))->order_by("all_order","asc")->get("task");
   }
   
   function get_task_group($task_id){
   
   		$this->db->where("task_group_id in(select task_group_id from task_to_group where task_id=".$task_id.")");
   		
		return $this->db->where("status",1)->get("task_group");
   }
   
   function projects(){
   return $this->db->get("project");
   }
   function project($project_id){
   
   return $this->db->where(array("project_id"=>$project_id))->get("project");
   }
   
   function get_project_tasks($project_id){
   
   return $this->db->where(array("project_id"=>$project_id))->get("task");
   }
   
   function get_project_group($project_id){
	
		$sql="SELECT * FROM `project_to_taskgroup` as ptg
		left join task_group as tg
		on tg.task_group_id=ptg.task_group_id 

		WHERE  `project_id`=".$project_id;   //and tg.status=1
	return $this->db->query($sql);

	}
	
   function task_group(){
   
   return $this->db->where("status",1)->get("task_group");
   }
   
   function task_status(){
   
   return $this->db->order_by("order_sort","asc")->get("task_status");
   }
   function task_statu($status_id){
   
   return $this->db->where("task_status_id",$status_id)->get("task_status");
   }
   
   function project_settings(){
   
   return $this->db->get("project_settings");
   }
   
	
	function task_files($task_id){
   
   return  $this->db->get_where("file",array("task_id"=>$task_id));
   }
   
   function ongoing_task_chart(){
   
   $dd=$this->db->query("SELECT count(*) as total , p.title as project_title,p.color as project_color  FROM `task`  as t
   
   
   left join project as p
   on p.project_id=t.project_id
   
   where t.`finished`=2 group by t.`project_id`");
   
   foreach($dd->result_array() as $row){
   
   $result["data"][]=array($row["project_title"],$row["total"]);
   $result["color"][]=$row["project_color"];
   }
    return $result;
   }
   
}
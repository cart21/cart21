<?php
class project_lib {

	function __construct(){
		
		 $this->CI= & get_instance() ;
		 
		 $this->project_id= 0;
	}
	
	function set_id($project_id){
	
	$this->project_id=$project_id;
	}
	
	function set_project(){
	
	$this->project= $this->CI->task_model->project($this->project_id);
	}
	
	function get_project_group(){
	
		
	return $this->CI->task_model->get_project_group($this->project_id);
	}
	
	
	function dbg($array){
		echo '<pre>'; var_dump($array); echo '</pre>';
	}
	function dbg2($array){
		echo '<pre>'; print_r($array); echo '</pre>';
	}
	
	
	
/*	
echo 'you are already logged in';
$sess_customer=$this->CI->sessiondd->userdata('customer');
$this->dbg2($sess_customer);
*/

}
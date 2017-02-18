<?php

class ajax extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    function index() {
    
   echo  "dd"; exit;
    }

    function citylist() {
    
	   
	   // $Cities=$this->Account_Model->get_Cities($this->input->post("code"));
	   
	    $Cities=$this->db->where(array("country_code"=>$this->input->post("code") ) )->order_by("city_code","asc")->get("city_list");
	    
	    $select="<select name='city_code' >  <option value=''>seç </option>";
	    
	    foreach($Cities->result_array() as $City ){
	    
	     $select.="<option value='".$City["city_code"]."'> ".$City["city_name"]." </option> ";
	    
	    }
	     $select.="</select>";
	    echo $select;
	    exit;
    }
    
    function get_room() {
  
	    $result=$this->db->where(array("otel_id"=>$this->input->post("id"),"status"=>1 ) )->get("room");
	    
	   // $this->quick->dbg2( $result);
	    $select="<select name='room_id' > <option value=''>seç </option>";
	    
	    foreach($result->result_array() as $room ){
	    
	     $select.="<option value='".$room["room_id"]."'> ".$room["title"]." </option> ";
	    
	    }
	     $select.="</select>";
	    echo $select;
	    exit;
    }
    
    function makeDafaultAdress() {
    
    
    	
    	if($this->input->post("address_id")){
    	
	    	$this->db->where('default_address',"1");
		$this->db->update('address', array("default_address"=>'0'));
		
		
		$this->db->where('address_id',$this->input->post("address_id") );
		$this->db->update('address', array("default_address"=>'1'));
	}
    	exit;
    
    }
    
    function set_language($language){
  
 		$this->sessiondd->set_userdata('language',$language);
       
		
    	redirect("account/login");
    
    }
    
	function deneme(){
    	
    	//$this->quick->dbg2( $this);
   
    }
    
//  function calender_edit_cell(){
//     
// 			
// 	echo $this->modules->calender_edit_cell($this->smarty);
// 	exit;
// 	}
// 	
	function get_customer(){
		
	$user=$this->db->where('email',$this->input->post("email") )->get("customer");
	
	if($user->num_rows>0){
		echo $this->modules->get_customer($this->smarty,$user->row_array());
	}else{
		echo "0";
	}
	exit;
	}
	
	function check_room_availability(){

		$otel_id	=$this->input->post("otel_id");
		$room_id	=$this->input->post("room_id");
		
		$begin_date		=strtotime($this->input->post("begin_date"));
		$expire_date	=strtotime($this->input->post("expire_date"));
	
		$sql="SELECT * FROM `rezervation` WHERE ( (begin_date  BETWEEN  $begin_date  AND $expire_date) OR (expire_date  BETWEEN $begin_date  AND $expire_date)  ) AND  otel_id=$otel_id AND  room_id=$room_id";
		

	echo $this->db->query($sql)->num_rows;
	
	exit;
	}
    
    
		
}
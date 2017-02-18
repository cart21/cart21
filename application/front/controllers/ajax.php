<?php

class ajax extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
       
    }
 
    function index($dd="") {
    
    if(!empty($dd)){
    	call_user_func_array(array($this, $dd));

	exit;
    }else{
    	echo "dd";
    }
    }

    function citylist() {
    
	    $this->load->model('Account_Model');
	    $Cities=$this->Account_Model->get_Cities($this->input->post("code"));
	    
	    $select="<select name='city_code' class='form-control' >";
	    
	    foreach($Cities->result_array() as $City ){
	    
	     $select.="<option value='".$City["city_code"]."'> ".$City["city_name"]." </option> ";
	    
	    }
	     $select.="</select>";
	    echo $select;
	    exit;
    }

}
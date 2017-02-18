<?php
class content_lib {

	function __construct(){
		
		 $this->CI= & get_instance() ;
		
	}
	
	
	function content_category_checkbox($content_type_id="",$checked=array()){

		$data=array();
		
		if(!empty($content_type_id)){
		$this->CI->db->where("content_type_id",$content_type_id);
		}
		$categories= $this->CI->content_model->content_categories();
		
		if($categories->num_rows>0){
		$data["content_categories"]=$categories->result_array();
		}else{
		$data["content_categories"]="";
		}
		
		$data["checked"]=$checked;
		
		return $this->CI->smarty->fetch("content/content_category_checkbox.tpl",$data);
	}
	
	


}
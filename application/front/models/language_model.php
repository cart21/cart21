<?php
class language_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
   
    
    function language_page($language_id=null){
    
     	if(! $language_id){
     		$language_id=f_language_id();
     	}
    
     	$this->db->where("language_id",$language_id);
    
    return $this->db->where("type",1)->get("meta");
    }

    
    function language($data){
    
    return $this->db->where($data)->get("language");
    }
    
    function languages(){
    	$this->db->where("status",1);
    	return $this->db->get("language");
    }
    
    function delete_language_c_page($language_c_id){
    	
    	$this->db->where("language_c_id",$language_c_id)->delete("language_c_to_page");
    	
    }
    
    function language_c($data){
    
    	return $this->db->where($data)->get("language_c");
    }
    
    function language_to_page($language_c_id){
    	
    	return $this->db->where("language_c_id",$language_c_id)->get("language_c_to_page");
    }
    
    function page_to_language($meta_id){
    	 
    	$this->db->where_in("meta_id",$meta_id);
    return $this->db->get("language_c_to_page");
    }
    
    function language_c_group($key_val){
    	
    	$sql="SELECT * FROM `language` as l

		left join language_c as lc 
		on lc.language_id=l.language_id
		
		where l.status=1 and  lc.key_val='".$key_val."'";
    	
    	return $this->db->query($sql);
    }
    
    function language_c_keys(){
    	
    	return $this->db->group_by("key_val")->get("language_c");
    }
    
    function language_c_key($key){
    	 
    	 $this->db->where("language_id",$_SESSION["cart21_language"]["language_id"]);
    	 $result=$this->db->where("key_val",$key)->get("language_c");
  
    	 if($result->num_rows){
    	 	return $result->row()->text_val;
    	 }else{
    	 	return $key;
    	 }
    	
    } 
    
    
    function languga_c_by_page($ids=0){
    	
    	$general_language_id=$this->quick_model->meta(array("class_routes"=>"general_language"))->row()->m_id;
    
    	if(is_array($ids)){
    		$ids=array_merge($ids,array($general_language_id));
    	}else{
    		$ids=array($general_language_id);
    	}
    	
    	$l_ids=array_column($this->page_to_language($ids)->result_array(),"language_c_id");
    	
    	$this->db->where_in("language_c_id",$l_ids);
    	
    	$this->db->where("language_id",f_language_id());
    	return $this->db->get("language_c");
    }
   

}
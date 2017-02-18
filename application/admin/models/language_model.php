<?php
class language_model extends CI_Model {


	function __construct(){

        parent::__construct();
    }
    
   
    
    function language_page($language_id=null){
    
     	if(! $language_id){
     		$language_id=language_id();
     	}
    
     	$this->db->where("language_id",$language_id);
    return $this->db->where("type",1)->get("meta");
    }
    
    function permission_sections(){
    
    	return $this->db->get("permission_sections");
    }

    
    function language($data){
    
    return $this->db->where($data)->get("language");
    }
    
    function languages($option=null){
    	
    	if(!$option){
    		$this->db->where("status",1);
    	}
    	return $this->db->get("language");
    }
    
    function delete_language_c_page($language_c_id){
    	
    	$this->db->where("language_c_id",$language_c_id)->delete("language_c_to_page");
    }
    
    function delete_language_c_section($language_c_id){
    	
    	$this->db->where("language_c_id",$language_c_id)->delete("language_c_to_section");
    }
    
    function language_c($data){

    	return $this->db->where($data)->get("language_c");
    }
    
    function language_to_page($language_c_id){
    	
    	return $this->db->where("language_c_id",$language_c_id)->get("language_c_to_page");
    }
    
    function language_to_section($language_c_id){
    	 
    	return $this->db->where("language_c_id",$language_c_id)->get("language_c_to_section");
    }
    function section_to_language($section_id){
    
    	$this->db->where_in("section_id",$section_id);
    	 
    	return $this->db->get("language_c_to_section");
    }
    
    function language_c_ids_by_class($class){
    	
    	$class=array_map(function($v){  return "'".$v."'"; },$class);
    	
    	$sql=" select lcs.language_c_id as language_c_id from language_c_to_section as lcs
    			
    			left join permission_sections as ps 
    			on ps.section_id=lcs.section_id
    			
    			where ps.class in(".implode(',',$class).") ";
    	
    	return $this->db->query($sql);
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
    
    function languga_c_by_page($ids=0){
    	
    		if(is_array($ids)){
    			$ids=array_merge($ids,array("general"));
    		}else{
    			$ids=array("general");
    		}
    		$l_ids=array_column($this->language_c_ids_by_class($ids)->result_array(),"language_c_id");
    	
    	 
    	$this->db->where_in("language_c_id",$l_ids);
    	 
    	$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	return $this->db->get("language_c"); 
    }
    
    function update($data,$language_id=null){
    	
    	if(! $language_id){
    		$language_id=$_SESSION["cart21_a_language"]["language_id"];
    	}
    	
    	if($this->language_c(array("key_val"=>$data["key_val"]))->num_rows ){
    		
    		$this->db->where(array("language_id"=>$language_id,"key_val"=>$data["key_val"]))->update("language_c",$data);
    	
    	}else{
    		$this->insert($data);
    	}
    }
    
    function insert($data,$language_id=null){
    	 $ids=array();
    	foreach($this->languages()->result_array() as $l){
    		
    		$data["language_id"]=$l["language_id"];
    		$this->db->insert("language_c",$data);
    		$ids[]=$this->db->insert_id();
    	}
    	
    	return $ids;
    }
    
    function language_c_key($key){
    
    	$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	$result=$this->db->where("key_val",$key)->get("language_c");
    
    	if($result->num_rows){
    		return $result->row()->text_val;
    	}else{
    		return $key;
    	}
    	 
    }
    
    
   

}
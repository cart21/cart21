<?php
class blog_model extends CI_Model {


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
   
   function posts($category_id="",$limit=""){
   
   	$this->db->select("*")->from("wp_posts");
        $this->db->join('wp_term_relationships', 'wp_term_relationships.object_id = wp_posts.id', 'left');
        
        if(!empty($category_id)){
        	$this->db->where("wp_term_relationships.term_taxonomy_id",$category_id);
        }
        
        if(!empty($limit)){
        	$this->db->limit($limit);
        }
        
        $posts=$this->db->where("post_status","publish")->order_by("wp_posts.id","desc")->get();
        return  $posts;
   	   
   
   }
   
   function categories(){
   
   	  return $this->db->get("wp_terms");
   	   
   }
   
   function categories_to_posts($category_id){
   
   	  return $this->db->where("term_taxonomy_id",$category_id)->get("wp_term_relationships");
   	   
   }
   
    	
   
 //$this->quick->dbg($data);   
 //echo $this->db->affected_rows();
 //echo  $this->db->insert_id();    

}
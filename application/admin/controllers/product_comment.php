<?php
class product_comment extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('category_model');
        $this->load->model('meta_model');
        
        $this->meta_type=5;
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_comment_id" => $this->data["L"]["id"],
    			"comment" => $this->data["L"]["comment"],
    			"customer_id" => $this->data["L"]["customer"],
    			"meta_type_id" => $this->data["L"]["metatype"],
				"product_id" => $this->data["L"]["product"],
				"status" => $this->data["L"]["status"],
    			"date_added" =>$this->data["L"]["dateadded"]
				
			);
    
    }
    
    function where_work(){
    
			
		
		if(is_array($this->form_post_product_comment)){
		
			$this->form_post_product_comment_where=array_diff_key($this->form_post_product_comment,$pattern=array("comment"=>"comment"));
			$this->db->where($this->form_post_product_comment_where);
			
			
			if(isset($this->form_post_product_comment["comment"])){
				$this->db->like("comment",$this->form_post_product_comment["comment"]);
			}
			
			
		}else{
		$this->form_post_product_comment=array();
		}	
		/// post1 *///	
    
    
    }
    
	function index($action=""){
		
		$this->permission->check_permission("view");
		
		$this->quick->Header("");
		
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_product_comment=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_product_comment',$this->form_post_product_comment);
		}else{
		
			$this->form_post_product_comment=$this->sessiondd->userdata('form_post_product_comment') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_comment;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_comment')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_comment/index';
		$config['total_rows'] = $this->data['Total'];
		$config['per_page'] = $per_page; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 4;
		$config["num_links"] =6;// round($choice);
		//$config['cur_tag_open'] = '<b>';
		$config['last_link'] = 'Last';
		$config['first_link'] = "First";
		$config['prev_link'] = ' previous ';
		$config['next_link'] = ' next ';
		
		
		$config['full_tag_open'] = ' <div class="pagination pagination-small" style="text-align:left;"> <ul>';
		$config['full_tag_close'] = ' </ul></div>';
		
		$config['cur_tag_open'] = ' <li><a style="color:grey;font-weight: 600;">';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = ' <li>';
		$config['num_tag_close'] = '</li>';
		
		$config['first_tag_open'] = ' <li>';
		$config['first_tag_close'] = '</li>';
		
		$config['last_tag_open'] = ' <li>';
		$config['last_tag_close'] = '</li>';
		
		
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		
		$this->pagination->initialize($config); 
		
		$this->data["pagelink"]=$this->pagination->create_links();
		/////////////  pagination  /////////////
		
		
		
		$this->where_work();
		
		///order by///
		
		$product_comment_session=$this->sessiondd->userdata('product_comment');
		
		//$this->dbg2($product_comment_session);
		if (	isset($product_comment_session["orderby"])		){
		
		$this->db->order_by($product_comment_session["orderby"], $product_comment_session["orderby_order"]);
		}else{
		$this->db->order_by("product_comment_id", "desc");
		}	
		///order by///
			
			
			
		 
		//// filter*  ////
		
		$page=$this->uri->segment(4);
		
		if(!empty($page) and $page!="list" ){
 		
 		$per_page_start=$per_page*($page-1);
 		}else{
 		$page=0;
 		$per_page_start=$per_page*$page;
 		}
 	
 		
 		$product_comments=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_comment");   ///segment 4 page
	
		$this->data["product_comments"]=$this->modules->product_comment_list($product_comments);
		
		if($action=="list"){
		echo $this->data["product_comments"];
		exit;
		}
		
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('comment/comment',$this->data);
	 
	}



    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");

    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("product_comment_id")>0){
    
    		$this->db->where("product_comment_id",$this->input->post("product_comment_id"))->update("product_comment",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    
    
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
		
			$this->db->where_in("product_comment_id",$this->input->post("product_comment_id"))->delete("product_comment");
		
			$this->quick_model->logs(implode(',',$this->input->post("product_comment_id"))." idli Comments  deleted ");
			$result="1";
		}else{
			$result="0";
		}
	   	echo $result;
    	exit;	
    }
    
    function get_filter_message(){
	
	
	$message="";
		foreach($this->data["POST"] as $key =>$value){
		
			if($key=="product_comment_group"){
				$group=$this->db->where_in("product_comment_group_id",$value)->get("product_comment_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_comment',"");
	redirect('admin/product_comment');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_comment_session=$this->sessiondd->userdata('product_comment') ;
			
				$product_comment_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_comment',$product_comment_session);
			
				
				if(	isset($product_comment_session["orderby_order"]) ){
				
					if($product_comment_session["orderby_order"]=="asc" ){
					
						$product_comment_session["orderby_order"]="desc";
					}else{
					
						$product_comment_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_comment_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_comment',$product_comment_session);
			}
	
	redirect('admin/product_comment');
	}
	
	
		
}
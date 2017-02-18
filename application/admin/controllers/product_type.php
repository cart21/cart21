<?php
class product_type extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('product_model');
   
    }
 
    function get_shape(){
   
    return array(
    			
    			"product_type_id" => $this->data["L"]["id"],
    			"title" => $this->data["L"]["title"],
    			"theme" => $this->data["L"]["theme"],
				"description" => $this->data["L"]["description"],
				"status" => $this->data["L"]["status"],
				"sort_order" => $this->data["L"]["sorting"],
				"pl_id" => $this->data["L"]["id"],
				"language_id" => $this->data["L"]["language"]
				
			);
    }
    
    function where_work(){
    
    	if(isset($this->form_post_product_type["language_id"])){
    		$this->db->where("language_id",$this->form_post_product_type["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_product_type)){
		
			$this->form_post_product_type_where=array_diff_key($this->form_post_product_type,$pattern=array("link"=>"link","class_routes"=>"class_routes"));
			$this->db->where($this->form_post_product_type_where);
			
			
			if(isset($this->form_post_product_type["link"])){
				$this->db->like("link",$this->form_post_product_type["link"]);
			}
			
			if(isset($this->form_post_product_type["class_routes"])){
				$this->db->like("class_routes",$this->form_post_product_type["class_routes"]);
			}
			
		}else{
		$this->form_post_product_type=array();
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
		
			$this->form_post_product_type=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_product_type',$this->form_post_product_type);
		}else{
		
			$this->form_post_product_type=$this->sessiondd->userdata('form_post_product_type') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_product_type;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('product_type')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/product_type/index';
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
		
		$product_type_session=$this->sessiondd->userdata('product_type');
		
		if (	isset($product_type_session["orderby"])		){
		
		$this->db->order_by($product_type_session["orderby"], $product_type_session["orderby_order"]);
		}else{
		$this->db->order_by("product_type_id", "desc");
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
 		
 		
 		$product_types=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("product_type");   ///segment 4 page
	
		$this->data["product_types"]=$this->modules->product_type_list($product_types);
		
		if($action=="list"){
		echo $this->data["product_types"];
		exit;
		}
		
		$this->data["categories"]=$this->product_model->product_categories();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('product/product_type',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', 'title', 'trim|required|xss_clean');	
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 	
	 		$data_product_type=$this->input->post();
	 		
	 		$data_product_type["language_id"]=language_id();
	 		
	 		$this->db->insert("product_type",$data_product_type);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." idli product_type added !");
	 	
	 		
	 		$this->db->where("product_type_id",$ids)->update("product_type",array("pl_id"=>$ids));
	 			
	 	redirect("admin/product_type/edit/{$ids}/{$ids}");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_type_form',$this->data);
	
    }

    function change_status(){
    
    	$this->permission->check_permission("view");
    	$this->permission->check_permission("edit");
    	 
    	//dbg($this->input->post());
    	 
    	if($this->input->post("status")=="true"){
    
    		$status=1;
    	}else {
    
    		$status=0;
    	}
    	 
    	if($this->input->post("product_type_id")>0){
    
    		$this->db->where("product_type_id",$this->input->post("product_type_id"))->update("product_type",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    
    
    function edit($ids,$pl_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
		$this->data["POST"]=$this->input->post();
		
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('title', $this->language_model->language_c_key("title"), 'trim|required|xss_clean');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		///
	 		if($this->input->post("status")=="on"){
	 		
	 			$this->data["POST"]["status"]=1;
	 		}else {
	 		
	 			$this->data["POST"]["status"]=0;
	 		}
	 		///
	 		
	 		$data_product_type=$this->data["POST"];
	 		
	 		$this->db->where("product_type_id",$ids)->update("product_type",$data_product_type);
	 		
	 		$intersect_key=array(
					"sort_order"=>1,
					"theme"=>1
			);
	 		
	 		$cl_new_data=array_intersect_key($data_product_type,$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 				
	 			$this->db->where(array("pl_id"=>$pl_id,"language_id"=>$l["language_id"]))->update("product_type",$cl_new_data);
	 		}
	 		
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->db->where("product_type_id",$ids)->get("product_type")->row_array();
		$this->type_language_create();
		
		
		$this->data["ptl_group"]=$this->product_model->ptl_group($this->data["POST"]["pl_id"])->result_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('product/product_type_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			foreach($this->input->post("product_type_id") as $pl_id){
			$this->product_model->delete_product_type($pl_id);
			}
			
			$this->quick_model->logs(implode(',',$this->input->post("product_type_id"))." id product_type deleted !");
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
		
			if($key=="product_type_group"){
				$group=$this->db->where_in("product_type_group_id",$value)->get("product_type_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_product_type',"");
	redirect('admin/product_type');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$product_type_session=$this->sessiondd->userdata('product_type') ;
			
				$product_type_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('product_type',$product_type_session);
			
				
				if(	isset($product_type_session["orderby_order"]) ){
				
					if($product_type_session["orderby_order"]=="asc" ){
					
						$product_type_session["orderby_order"]="desc";
					}else{
					
						$product_type_session["orderby_order"]="asc";
					}
					
				}else{
				
					$product_type_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('product_type',$product_type_session);
			}
	
	redirect('admin/product_type');
	}
	

	
	function type_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join product_type as pc
			on pc.language_id=l.language_id and pc.pl_id=".$this->data["POST"]["pl_id"]."
	
			where l.status=1 and ( pc.product_type_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"pl_id"=>1,
					"sort_order"=>1,
					"theme"=>1,
					"status"=>1,
					"title"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("product_type",$new_data);
			}
	
		}
	}
	
	
	
		
}
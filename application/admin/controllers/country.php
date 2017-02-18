<?php
class country extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
        $this->load->model('order_model');
   
    }
 
    function get_shape(){
   
    return array(
    			
    			"country_id" => $this->data["L"]["id"],
    			"cl_id" => $this->data["L"]["id"],
    			"name" => $this->data["L"]["name"],
    			"full_name" => $this->data["L"]["name"],
				"code" => $this->data["L"]["code"],
				"iso3" => "iso3",
				"number" => $this->data["L"]["number"],
				"continent_code" => $this->data["L"]["continent"],
				"sort_order" => $this->data["L"]["sorting"],
				"language_id" => $this->data["L"]["language"]
				
			);
    }
    
    function where_work(){
    
    	if(isset($this->form_post_country["language_id"])){
    		$this->db->where("language_id",$this->form_post_country["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
		
		if(is_array($this->form_post_country)){
		
			$this->form_post_country_where=array_diff_key($this->form_post_country,$pattern=array("name"=>"name","full_name"=>"full_name"));
			$this->db->where($this->form_post_country_where);
			
			
			
			if(isset($this->form_post_country["name"])){
				$this->db->like("name",$this->form_post_country["name"]);
			}
			if(isset($this->form_post_country["full_name"])){
				$this->db->like("full_name",$this->form_post_country["full_name"]);
			}
			
		}else{
		$this->form_post_country=array();
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
		
			$this->form_post_country=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_country',$this->form_post_country);
		}else{
		
			$this->form_post_country=$this->sessiondd->userdata('form_post_country') ;
		}
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_country;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('country')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=100;
		$choice = $this->data['Total']/ $per_page;
		
		
		$config['base_url'] = base_url().'/admin/country/index';
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
		
		
		$config['full_tag_open'] = '  <ul class="pagination pagination-sm no-padding no-margin pull-left">';
		$config['full_tag_close'] = ' </ul>';
		
		$config['cur_tag_open'] = ' <li><a href="#" class="bg-navy text-white"> ';
		$config['cur_tag_close'] = '</a></li>';
		
		$config['num_tag_open'] = ' <li class="text-navy">';
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
		
		$country_session=$this->sessiondd->userdata('country');
		
		if (	isset($country_session["orderby"])		){
		
		$this->db->order_by($country_session["orderby"], $country_session["orderby_order"]);
		}else{
		$this->db->order_by("sort_order", "asc");
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
 		
 		
 		$countrys=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("country");   ///segment 4 page
	
		$this->data["countrys"]=$this->modules->country_list($countrys);
		
		if($action=="list"){
		echo $this->data["countrys"];
		exit;
		}
		
		$this->data["continents"]=$this->order_model->continents();
		$this->data["languages"]=$this->language_model->languages()->result_array();
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('site_settings/country',$this->data);
	 
	}

	function add(){
	
	$this->permission->check_permission("view");
	$this->permission->check_permission("add");
	$this->data["page"]="add";
	
	
	$this->data["POST"]=$this->input->post();
	
	if( $this->input->post() and $this->permission->check_permission("add") ){
	
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules("name", $this->language_model->language_c_key("name"), 'trim|required|xss_clean');	
		
	 	
	 	if ($this->form_validation->run() 	) { 
	 		
	 	
	 		$data_country=$this->input->post();
	 		
	 		$data_country["language_id"]=language_id();
	 		
	 		$this->db->insert("country",$data_country);
	 		
	 		$ids=$this->db->insert_id() ;
	 		$this->quick_model->logs($ids." idli country added !");
	 	
	 		
	 		$this->db->where("country_id",$ids)->update("country",array("cl_id"=>$ids));
	 			
	 	redirect("admin/country/edit/{$ids}/{$ids}");
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
	$this->data["continents"]=$this->order_model->continents();
	
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('site_settings/country_form',$this->data);
	
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
    	 
    	if($this->input->post("country_id")>0){
    
    		$this->db->where("country_id",$this->input->post("country_id"))->update("country",array("status"=>$status ));
    
    	}
    	 
    	echo 1; exit;
    	 
    }    
    
    function edit($ids,$cl_id){
    
	    $this->permission->check_permission("view");
	    $this->permission->check_permission("edit");
	    $this->data["page"]="edit";
   		$this->ids=$ids;
   	
		$this->data["POST"]=$this->input->post();
		
	if( $this->input->post() and $this->permission->check_permission("add") ){
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	    
		$this->form_validation->set_rules('name', $this->language_model->language_c_key("name"), 'trim|required|xss_clean');	
		
	 	if ($this->form_validation->run() 	) { 
	 		
	 		
	 		$data_country=$this->data["POST"];
	 		
	 		$this->db->where("country_id",$ids)->update("country",$data_country);
	 		
	 		$intersect_key=array(
					"code"=>1,
	 				"iso3"=>1,
	 				"number"=>1,
	 				"continent_code"=>1
			);
	 		
	 		$cl_new_data=array_intersect_key($data_country,$intersect_key);
	 		
	 		foreach($this->language_model->languages()->result_array() as $l){
	 				
	 			$this->db->where(array("cl_id"=>$cl_id,"language_id"=>$l["language_id"]))->update("country",$cl_new_data);
	 		}
	 		
	 		
	 		$this->quick->success[]=$this->language_model->language_c_key("successfuledit");
	 		
	 	}else {
			$verrors=array_filter(explode('.',validation_errors()));
			foreach($verrors as $verror){
				$this->quick->errors[] = strip_tags($verror).".";
			}
		}
	}
	
		$this->data["POST"]=$this->db->where("country_id",$ids)->get("country")->row_array();
		$this->country_language_create();
		
		$this->data["continents"]=$this->order_model->continents();
		
		$this->data["country_group"]=$this->order_model->country_group($this->data["POST"]["cl_id"])->result_array();
		
		$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
	$this->smarty->view('site_settings/country_form',$this->data);
    
	
    }
    
    function delete(){
    
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("country_id",$this->input->post("country_id"))->delete("country");
			
			$this->quick_model->logs(implode(',',$this->input->post("country_id"))." id country deleted !");
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
		
			if($key=="country_group"){
				$group=$this->db->where_in("country_group_id",$value)->get("country_group")->result_array();
				$message.= "<p>".implode(",",$this->quick->array_column($group,"title") )."</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_country',"");
	redirect('admin/country');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$country_session=$this->sessiondd->userdata('country') ;
			
				$country_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('country',$country_session);
			
				
				if(	isset($country_session["orderby_order"]) ){
				
					if($country_session["orderby_order"]=="asc" ){
					
						$country_session["orderby_order"]="desc";
					}else{
					
						$country_session["orderby_order"]="asc";
					}
					
				}else{
				
					$country_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('country',$country_session);
			}
	
	redirect('admin/country');
	}
	

	
	function country_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join country as pc
			on pc.language_id=l.language_id and pc.cl_id=".$this->data["POST"]["cl_id"]."
	
			where l.status=1 and ( pc.country_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
	
			$intersect_key=array(
					"cl_id"=>1,
					"name"=>1,
					"full_name"=>1,
					"code"=>1,
	 				"iso3"=>1,
	 				"number"=>1,
	 				"continent_code"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("country",$new_data);
			}
	
		}
	}
	
	
	
		
}
<?php
class email_template extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
       
    }
 
    function get_shape(){
   
    return array(
    			
    			"subject" => $this->data["L"]["subject"],
    			"email_template_id" => $this->data["L"]["id"],
    			"language" => $this->data["L"]["language"],
    			"settings_options_id" => $this->data["L"]["website"],
    			"et_id" => $this->data["L"]["id"],
    			"language_id" => $this->data["L"]["language"]
				
			);
    
    }
    
    function where_work(){
	    /// post1 *///	
    	if(isset($this->form_post_email_template["language_id"])){
    		$this->db->where("language_id",$this->form_post_email_template["language_id"]);
    	}else{
    		 
    		$this->db->where("language_id",$_SESSION["cart21_a_language"]["language_id"]);
    	}
    	 

		if(is_array($this->form_post_email_template)){
		
			$this->form_post_where=array_diff_key($this->form_post_email_template,$pattern=array("post_title"=>""));
				$this->db->where($this->form_post_where);
			
			if(isset($this->form_post_email_template["subject"])){
				//$this->db->where("lower(post_title) like '%".strtolower($this->input->post("post_title"))."%'");
				$this->db->like("subject",($this->input->post("subject")));
				
				$this->db->or_like("subject",($this->input->post("subject")));
			}
		
		}else{
		$this->form_post_email_template=array();
		}	
		/// post1 *///	
    
    }
    
	function index($action=""){
		$this->permission->check_permission("view");

		$this->quick->Header("");
	 	$this->shape=$this->get_shape();
	
		$this->load->model('user');
		
		$this->data["settings_options"]=array_column($this->quick_model->get_sites()->result_array(),"site_title","settings_options_id");
		
		$this->data["languages"]=array_column($this->language_model->languages()->result_array(),"name","language_id");
		 
		/////////////  pagination  /////////////
		$this->load->library('pagination');
		

		///post1 ///
		if( $this->input->post()){
		
			$this->form_post_email_template=array_filter($this->input->post());
			$this->sessiondd->set_userdata('form_post_email_template',$this->form_post_email_template);
		}else{
		
			$this->form_post_email_template=$this->sessiondd->userdata('form_post_email_template') ;
		}
		
		//$this->dbg2($this->form_post_email_template);
		
		
		$this->where_work();
		
		$this->data["POST"]= $this->form_post_email_template;

		
		//// filter  ////
		
		/////////////  pagination  /////////////
		$this->data['Total']=$this->db->get('email_template')->num_rows();//$this->user->getTotalUsers(); 
		$per_page=15;
		$choice = $this->data['Total']/ $per_page;
		
		$config['base_url'] = base_url().'/admin/email_template/index';
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
		$email_template_session=$this->sessiondd->userdata('email_template');
		
		if (	isset($email_template_session["orderby"])		){
		
		$this->db->order_by($email_template_session["orderby"], $email_template_session["orderby_order"]);
		}else{
		$this->db->order_by("email_template_id", "asc");
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
 		
 		$email_templates=$this->db->limit($per_page, $per_page_start)->select(implode(",",array_keys($this->shape)))->get("email_template");   ///segment 4 page
		$this->data["email_templates"]=$this->modules->email_template_list($this->smarty,$email_templates);
		
		if($action=="list"){
		echo $this->data["email_templates"];
		exit;
		}
		
		$this->data["number_person"]=range(1,10);
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('email_template/email_template',$this->data);
	 
	}
	
	function edit($id,$et_id) {
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");
    	$this->data["page"]="edit";
	
		if( $this->input->post() ){
		
			$this->data["POST"]=array_diff_key($this->input->post(),array("categories"=>1));
			$this->db->where("email_template_id",$id)->update("email_template",$this->data["POST"]);
			
			$intersect_key=array(
					"et_id"=>1,
					"document"=>1,
					"settings_options_id"=>1
			);
			
			$et_new_data=array_intersect_key($this->data["POST"],$intersect_key);
			
			foreach($this->language_model->languages()->result_array() as $l){
					
				$this->db->where(array("et_id"=>$et_id,"language_id"=>$l["language_id"]))->update("email_template",$et_new_data);
			}
			
		}
		$this->data["settings_options"]=array_column($this->quick_model->get_sites()->result_array(),"site_title","settings_options_id");
		$this->data["languages"]=array_column($this->language_model->languages()->result_array(),"name","language_id");
		
	
      	$this->quick->Header("");
		$this->quick->Top_menu("");
		$this->quick->Footer("");
       
        $this->data["POST"]=$this->db->where("email_template_id",$id)->get("email_template")->row_array();
        $this->et_language_create();
        
        $this->data["et_group"]=$this->quick_model->et_group($this->data["POST"]["et_id"])->result_array();
        
     $this->smarty->view('email_template/email_template_edit',$this->data);	
     }
	function add(){
	
	echo 'adding manual';
	}
	function view($id){
		$this->permission->check_permission("view");
		
		$this->data["POST"]=$this->db->where("email_template_id",$id)->get("email_template")->row_array();
		
		
		echo "<div> <p  class='inline'>  Subject:   </p> <p  class='inline text-navy bold'>".$this->data["POST"]["subject"]."</p></div> <hr>";
		echo $this->data["POST"]["body"];
		exit;
	}
    
    function delete(){
    
    
    exit;
		if($this->permission->check_permission("delete") ){
			
			$this->db->where_in("email_template_id",$this->input->post("email_template_id"))->delete("email_template");
			
			$this->quick_model->logs(implode(',',$this->input->post("email_template_id"))." idli email_templatelar silindi ");
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
		
			if($key=="customer_id"){
				$customer=$this->db->where("customer_id",$value)->get("customer")->row_array();
				$message.= "<p>".$this->shape[$key].":".$customer["firstname"]." ".$customer["lastname"]." rezervasyonları</p>";
			}else{
				$message.= "<p>".$this->shape[$key].":".$value."</p>";
			}
		}
		
	return $message;
	}

	function clear_filter(){
	
		$this->sessiondd->set_userdata('form_post_email_template',"");
	redirect('admin/email_template');
	}

	function set_orderby(){
	
		if($this->input->get("orderby")){
				
				$email_template_session=$this->sessiondd->userdata('email_template') ;
			
				$email_template_session["orderby"]=$this->input->get("orderby");
				$this->sessiondd->set_userdata('email_template',$email_template_session);
			
				
				if(	isset($email_template_session["orderby_order"]) ){
				
					if($email_template_session["orderby_order"]=="asc" ){
					
						$email_template_session["orderby_order"]="desc";
					}else{
					
						$email_template_session["orderby_order"]="asc";
					}
					
				}else{
				
					$email_template_session["orderby_order"]="asc";
				}
				
				$this->sessiondd->set_userdata('email_template',$email_template_session);
			}
	
	redirect('admin/email_template');
	}
	
	function et_language_create(){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join email_template as et
			on et.language_id=l.language_id and et.et_id=".$this->data["POST"]["et_id"]."
		
			where l.status=1 and ( et.email_template_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
				
			$intersect_key=array(
					"et_id"=>1,
					"document"=>1,
					"settings_options_id"=>1,
					"subject"=>1,
					"body"=>1
			);
			$new_data=array_intersect_key($this->data["POST"],$intersect_key);
				
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("email_template",$new_data);
			}
				
		}
	}
	
	
		
}
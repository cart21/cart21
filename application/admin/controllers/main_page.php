<?php
class main_page extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('task_model');
        $this->load->model('main_page_model');
     
    }
    
    function slider(){
	
		$this->permission->check_permission("view");
		$this->permission->check_permission("edit");

		$this->quick->Header("");
		
		if( $this->input->post() and  $this->permission->check_permission("edit")){
		
			/// link slide ///
			$description= $this->input->post("slide_description");
			$title= $this->input->post("slide_title");
			
			foreach( $this->input->post("link") as $k =>$v){
			
			$this->db->where("page_slides_id",$k)->update("page_slides", array("link"=>$v,"description"=>$description[$k],"title"=>$title[$k]));
			}
			/// link slide///
			$this->upload();
		}
		
		$this->data["page_slides"]=$this->main_page_model->page_slides()->result_array();
	 	
		foreach( $this->data["page_slides"] as $v){
			$this->page_slide_language_create($v);
		}
		
		$this->quick->Top_menu("");
		$this->quick->Footer("");
			
	$this->smarty->view('content/main_page_slide',$this->data);
	 
	}
    
	function index($action=""){}
    

    
	function upload(){
	
		if($_FILES["ImageFile"]["error"][0]!=4){
		
		$directoryPath = $_SERVER["DOCUMENT_ROOT"]."/uploads/slides/";
		
        if (!file_exists($directoryPath)) {
        mkdir($directoryPath, 0755);
        }
		
        $config['upload_path'] ='./uploads/slides/';
       
		$config['file_name'] = mktime()."_".rand(1,5);
		$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|ico|rar|zip|php';
		$config['max_size']	= '22222100';
		$config['max_width']  = '22222221024';
		$config['max_height']  = '222222768';
		
		$this->load->library('upload', $config);
		$result=$this->upload_("ImageFile");
	
		
		}
    }
    
    function upload_($name){
	
		$result=array();
		$files = $_FILES;
		$link= $this->input->post("link_new");
		$title= $this->input->post("slide_title_new");
		$description= $this->input->post("description_new");
		
		$cpt = count($_FILES[$name]['name']);
		for($i=0; $i<$cpt; $i++){
	
				$_FILES[$name]['name']= $files[$name]['name'][$i];
				$_FILES[$name]['type']= $files[$name]['type'][$i];
				$_FILES[$name]['tmp_name']= $files[$name]['tmp_name'][$i];
				$_FILES[$name]['error']= $files[$name]['error'][$i];
				$_FILES[$name]['size']= $files[$name]['size'][$i]; 
			
			if ( ! $this->upload->do_upload($name)){
				$error = $this->upload->display_errors();
				$result["error"][]=$error;
			}else{
				$image =  $this->upload->data();
				
				$first_id=null;
				
				foreach($this->language_model->languages()->result_array() as $l){
					
					$image_data=array(
					"image"=>'uploads/slides/'.$image["file_name"],
					"link"=>$link[$i],
					"description"=>$description[$i],
					"title"=>$title[$i],
					"date_added"=>mktime(),
					"language_id"=>$l["language_id"]
					);
					
					$image_data["pl_id"]=$first_id;
					
					$this->db->insert("page_slides",$image_data	);
					
					if($first_id==null){
						$first_id=$this->db->insert_id();
						$this->db->where("page_slides_id",$first_id)->update("page_slides",array("pl_id"=>$first_id));
						
					}
				}
				
				$result["success"][]=$image;
			}
		
		}
		
		 return  ($result);
	
	}
 
    function delete_image(){
   
    $image_url=$this->db->where(array("pl_id"=>$this->input->post("file_id")) )->get("page_slides");
    $image_url=$image_url->row()->image;
    
    $this->db->delete("page_slides",array("pl_id"=>$this->input->post("file_id")));
    
    unlink($_SERVER["DOCUMENT_ROOT"].'/'.$image_url);
    exit;
    }
    
    function sort_order(){
	
			if($this->input->is_ajax_request()){
			
					$page_slides=explode(',',$this->input->post("page_slides"));
					
					$i=1;
					foreach($page_slides as $page_slide){
					
						$this->db->where("pl_id",$page_slide)->update("page_slides",array("sort_order"=>$i));
					$i++;
					}
			$this->quick_model->logs("main page slide sorted");		
			}
	echo "1";	exit;	
	}
    
	
	function main_page_language_create($POST){
	
		$sql="SELECT l.language_id FROM `language` as l
	
			left join main_page_option as mp
			on mp.language_id=l.language_id and mp.ml_id=".$POST["ml_id"]."
		
			where l.status=1 and ( mp.main_page_option_id is null)";
	
		$result=$this->db->query($sql);
	
		if($result->num_rows){
				
			$intersect_key=array(
					"ml_id"=>1,
					"content"=>1
			);
			$new_data=array_intersect_key($POST,$intersect_key);
				
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("main_page_option",$new_data);
			}
				
		}
	}
	
	function page_slide_language_create($POST){

		$sql="SELECT l.language_id FROM `language` as l
	
			left join page_slides as ps
			on ps.language_id=l.language_id and ps.pl_id=".$POST["pl_id"]."
	
			where l.status=1 and ( ps.page_slides_id is null)";
	
		$result=$this->db->query($sql);

		if($result->num_rows){
	
			$intersect_key=array(
					"pl_id"=>1,
					"image"=>1,
					"sort_order"=>1,
					"date_added"=>1,
					"description"=>1
			);
			$new_data=array_intersect_key($POST,$intersect_key);
	
			foreach($result->result_array() as $l){
	
				$new_data["language_id"]=$l["language_id"];
				$this->db->insert("page_slides",$new_data);
			}
	
		}
		return 1;
	}
		
}
<?php
class product_property extends CI_Controller {
 
    function __construct() {
    
        parent::__construct();
        
       
        $this->load->model('quick_model');
        $this->load->model('product_model');
        
        $this->meta_type=10;
       
    }
 
  
    
	function index($property_id=""){
		
		$this->data["pp"]=$this->product_model->property($property_id);
		
		if($this->data["pp"]->num_rows){
			$this->data["pp"]=$this->data["pp"]->row_array();
		}else{
		exit;	
		}
		
		$this->data["products"]=$this->product_model->property_products($property_id);
		
        /// meta ///
        $uri=substr($_SERVER["REQUEST_URI"],1);
		$db_routes=$this->db->limit(1)->get_where("meta",array("link"=>mysql_real_escape_string($uri)));

		if($db_routes->num_rows()>0){
		
			$this->data["meta"]=$db_routes->row_array(); 
		}else{
		
       		$meta = $this->quick_model->meta(array("type"=>10,"type_id"=>$property_id));
       		$this->data["meta"]=$meta->num_rows >0 ? $meta->row_array() : ""; 
        }
        
        /// meta ///
        $this->data["L"]=array_column($this->language_model->languga_c_by_page(array(0))->result_array(),"text_val","key_val");
        
   		$this->quick->Header("");
        $this->quick->Top_menu("");
        $this->quick->Footer("");
     
       
       $this->smarty->view('product_property',$this->data);}

		
}
<?php
class page_position_lib {

	function __construct(){
		
		 $this->CI= & get_instance() ;
		 
	}
	
	function set_page_position_form($data){
		
		// l_id, plugin_id, 
		$this->data["data"]=$data;
		$this->data["plugin"]=$this->CI->plugin_model->plugin($data["plugin_id"])->row();
		$this->data["L"]=$this->CI->data["L"];
		
		///position ///
		$this->data["static_pages"]=$this->CI->meta_model->static_pages()->result_array();
		$this->data["page_positions"]=$this->CI->plugin_model->page_positions()->result_array();
		///position ///
		
		///lucky position ///
		$this->data["lucky_positionsF"]=$this->CI->plugin_model->assigned_positions($data["plugin_id"],$data["type_id"])->result_array();
		$this->data["lucky_positions"]=array();
		foreach($this->data["lucky_positionsF"] as $l ){
		
			$this->data["lucky_positions"][$l["meta_id"]][$l["plugin_id"]][$l["page_position_id"]][$l["type_id"]]=1;
		}
		///lucky position ///
		
		$this->CI->data["page_positions"]=$this->CI->smarty->fetch('plugins/page_position_form.tpl',$this->data);
	}
	
	function after_post($data){
		
		$this->data["plugin"]=$this->CI->plugin_model->plugin($data["plugin_id"])->row();
		
		
		///plugin dynamic to page ///
		if($this->CI->input->post("plugin_to_page")){
			foreach($this->CI->input->post("plugin_to_page") as $k=>$p){
					
				foreach ($p as $position_id){
		
					$check=$this->CI->db->where(array(
							"meta_id"=>$k,
							"plugin_id"=>$data["plugin_id"],
							"page_position_id"=>$position_id,
							"type_id"=>$data["type_id"]))->get("plugin_to_page");
		
					if($check->num_rows==0){
						$this->CI->db->insert("plugin_to_page",array(
								"meta_id"=>$k,
								"plugin_id"=>$data["plugin_id"],
								"page_position_id"=>$position_id,
								"type_id"=>$data["type_id"]));
					}
					$this->data["new_lucky_positions"][]=$k."-".$data["plugin_id"]."-".$position_id."-".$data["type_id"];
				}
			}
		
			///lucky position ///
			$this->data["lucky_positionsF"]=$this->CI->plugin_model->assigned_positions($data["plugin_id"],$data["type_id"]);
			if($this->data["lucky_positionsF"]->num_rows){
				foreach($this->data["lucky_positionsF"]->result_array() as $l ){
					$this->data["lucky_positions"][]=$l["meta_id"]."-".$l["plugin_id"]."-".$l["page_position_id"]."-".$l["type_id"];
				}
			}else{
				$this->data["lucky_positions"]=array();
			}
			///lucky position ///
		
			/// deletetion
			$diff=array_diff($this->data["lucky_positions"],$this->data["new_lucky_positions"]);
		
			if(count($diff)){
				foreach ($diff as $d ){
					$d=explode("-",$d);
					$this->CI->db->where(array("meta_id"=>$d[0],"plugin_id"=>$d[1],"page_position_id"=>$d[2],"type_id"=>$d[3]))->delete("plugin_to_page");
				}
			}
			/// deletetion
		}else{
			$this->CI->plugin_model->delete_plugin_to_page(array("plugin_id"=>$data["plugin_id"],"type_id"=>$data["type_id"]));
		}
		///plugin dynamic to page ///
	
	}


}
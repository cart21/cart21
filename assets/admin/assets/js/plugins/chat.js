

$(document).ready(function(){
	
	var my_id=21;

	
		$("[id='open_chat']").click(function(event){
			
			event.preventDefault();
			event.stopPropagation();
			console.log("dd");
			
			var chat_menu=$(this).parent().find(".chat_menu");
	
			var o_state= chat_menu.css("display");
	
			chat_menu.slideToggle("fast");
	
			if(o_state=="none"){
	
					$("#open_chat").find(".fa").removeClass("fa-chevron-circle-down").addClass("fa-chevron-circle-up");
	
				}else{
					$("#open_chat").find(".fa").removeClass("fa-chevron-circle-up").addClass("fa-chevron-circle-down");
	
				}
		
			
		});
				
				
				$(".chat_menu>.list-group>a").dblclick(function(){
					
					var name=$(this).text();
					var user_id=Math.random();
					
					var message_con='<div class="list-group">'
						 
					 	+'<div class="list-group-item"> '
					 		+'<p class="list-group-item-heading bold"> <i class="fa fa-user"></i> Jasmine</p>'
					 		+'<p class="list-group-item-text"> Hey! How are you ? </p>'
					 		+'<p class="plotm1 small"> 21:21 PM</p>'
					        
					 	+'</div>'
					 	  
					 	+'<div class="list-group-item">'
					 		+'<p class="list-group-item-heading bold"> <i class="fa fa-user"></i> muslum21</p>'
					 		+' <p class="list-group-item-text"> I am Good and You</p>'
					 		+'<p class="plotm1 small"> 21:21 PM</p>'
					        
					 	+'</div>'
					      
					 	+'<div class="list-group-item">'
						 	+'<p class="list-group-item-heading bold"> <i class="fa fa-user"></i> muslum21</p>'
						 	+'<p class="list-group-item-text"> I am Good too :)</p>'
						 	+'<p class="plotm1 small"> 21:21 PM</p>'
						        
						 +'</div>'
						  
						 	+'<div class="list-group-item">'
						 		+'<p class="list-group-item-heading bold"> <i class="fa fa-user"></i> muslum21</p>'
						 		+' <p class="list-group-item-text"> I am Good and You</p>'
						 		+'<p class="plotm1 small"> 21:21 PM</p>'
						        
						 	+'</div>'
						      
						 	+'<div class="list-group-item">'
							 	+'<p class="list-group-item-heading bold"> <i class="fa fa-user"></i> muslum21</p>'
							 	+'<p class="list-group-item-text"> I am Good too :)</p>'
							 	+'<p class="plotm1 small"> 21:21 PM</p>'
							        
							 +'</div>'
					      
					      
						 +'</div>'
					 
					 +'<div class="input-group">'
						+'<input type="text" class="form-control"> <span class="input-group-addon"><i class="fa fa-send"></i></span> '
					+'</div>'
					 
					+'<div class="clearfix"></div>'
					+' <br>';
					
					var html='<div class="col-md-12 " style="bottom:0;"  id="chat'+user_id+'">'
					
						+'<div class="col-md-12 bg-plotm8 round" id="open_chat-'+user_id+'" > '
							+'<h6 class="text-white">'
								+'<i class="fa fa-user"></i> '+name+'	<i class=" pull-right fa fa-close"></i> <i class=" pull-right fa fa-chevron-circle-down"></i> '
						+'</h6> </div>'
					
						+'<div class="col-md-12 bg-plotm3 round chat_menu " style="display:none;"> '
						+message_con
						+'</div> ';
					
					$("#chat_user_block").append(html);
					
					$("#chat_user_block").append('<div class="clearfix"></div>');
					
					
					/// add click event ///
					$("[id^='open_chat-"+user_id+"']").click(function(event){
						
						event.preventDefault();
						event.stopPropagation();
						console.log("dd");
						
						var chat_menu=$(this).parent().find(".chat_menu");

						var o_state= chat_menu.css("display");

						chat_menu.slideToggle("fast");

						if(o_state=="none"){

								$("#open_chat").find(".fa").removeClass("fa-chevron-circle-down").addClass("fa-chevron-circle-up");

							}else{
								$("#open_chat").find(".fa").removeClass("fa-chevron-circle-up").addClass("fa-chevron-circle-down");

							}
					
						
					});
					
					$("[id^='open_chat-"+user_id+"']").find(".fa-close").click(function(event){
						event.preventDefault();
						event.stopPropagation();
						
						$("[id^='open_chat-"+user_id+"']").parent().remove();
						
					});
					
					
				});


	
		});


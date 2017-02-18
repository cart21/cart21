/**
 * 
 */




$(document).ready(function(){
	
/*
	  $('.menu-template21').animate({
	        scrollTop: $(".menu-template21>li>a[class='menu_active']").offset().top
	    }, 100);
	*/
	 
	 
////panel toggle ////
	 
	 $(document).ready(function(){
	
		 
		 $(".panel[data='toolbar']").each(function(){
			 
			 $(this).find(".panel-heading>.panel-title:first").after('<div class="pull-right inline"> '
					 +'<button type="button" data="panel-toggle" class="btn btn-default btn-xs"> <i class="fa fa-minus"></i> </button>   ' 
					 +'<button type="button" data="panel-close" class="btn btn-danger btn-xs"> <i class="fa fa-close"></i> </button>'
					 +'</div>');
		 });
		 
		 	$(".box[data='toolbar']").each(function(){
			 
			 $(this).find(".box-header>.box-title:first").after('<div class="pull-right inline"> '
					 +'<button type="button" data="box-toggle" class="btn btn-default btn-xs"> <i class="fa fa-minus"></i> </button>   ' 
					 +'<button type="button" data="box-close" class="btn btn-danger btn-xs"> <i class="fa fa-close"></i> </button>'
					 +'</div>');
		 });
		 

		 $("[data='panel-toggle']").click(function(){ 
			 
			
			 var parent_div=$(this).parent().parent().parent(); //console.log(parent_div.attr("id"));
			 var display=parent_div.find(".panel-body").css("display"); //console.log(parent_div.css("display"));
			 
			 
			 if(display=="none"){
				 parent_div.find(".panel-body").css("display","block");
				 cookie.set(parent_div.attr("id"),"block");
				 $(this).find(".fa").removeClass("fa-minus").addClass("fa-chevron-down");
				 $(this).find(".fa").removeClass("fa-chevron-left");
			}else{
				 parent_div.find(".panel-body").css("display","none");
				 cookie.set(parent_div.attr("id"),"none");

				 $(this).find(".fa").removeClass("fa-minus").addClass("fa-chevron-left");
				 $(this).find(".fa").removeClass("fa-chevron-down");
			}
		 
		 });
		 
		 
		 $("[data='panel-close']").click(function(){ $(this).parent().parent().parent().remove(); });
		 
		 
		 $("[data='box-toggle']").click(function(){ 

			// $(this).parent().parent().parent().find(".box-body").toggle("fast");
			 
			 var parent_div=$(this).parent().parent().parent(); //console.log(parent_div.attr("id"));
			 var display=parent_div.find(".box-body").css("display"); //console.log(parent_div.css("display"));
			 
			 
			 if(display=="none"){
				 parent_div.find(".box-body").css("display","block");
				 cookie.set(parent_div.attr("id"),"block");
				 $(this).find(".fa").removeClass("fa-minus").addClass("fa-chevron-down");
				 $(this).find(".fa").removeClass("fa-chevron-left");
			}else{
				 parent_div.find(".box-body").css("display","none");
				 cookie.set(parent_div.attr("id"),"none");

				 $(this).find(".fa").removeClass("fa-minus").addClass("fa-chevron-left");
				 $(this).find(".fa").removeClass("fa-chevron-down");
			}
			
			 
		 });
		 
		 
		 
		 $("[data='box-close']").click(function(){ 
			 
			 $(this).parent().parent().parent().remove(); 
			
		 });



		 });
	 
////panel toggle ////
	
	 
	 
	/// left menu ///
	 



   		$(".dropdown-t21>li>a").click(function(){  

	      		var menu= $(this).next(".dropdown-t21:first")

	      	///
	      	
	      		var display=menu.css("display");
				if( display=="none" ){
					
	      		var i=$(this).find(".fa-angle-down");
	      		
	      		i.addClass("fa-angle-up").removeClass("fa-angle-down");

				}else{
					var i=$(this).find(".fa-angle-up");
					
		      		i.addClass("fa-angle-down").removeClass("fa-angle-up");
				}
		///
		
		
	      		menu.slideToggle("fast");
	      		
		
				
	      		var li =$(this).parent()

	      		$(".dropdown-t21>li").removeClass("menu_active");
	      		li.addClass("menu_active");
	      		 });

	      
	 
	/// left menu ///
 
	
});


function box_toggle_init(id,type){

  	$("#"+id).find("."+type+"-body").css("display",cookie.get(id) );
		if(cookie.get(id)=="block"){  $("#"+id).find(".fa-minus").removeClass("fa-minus").addClass("fa-chevron-down");  }else{  $("#"+id).find(".fa-minus").removeClass("fa-minus").addClass("fa-chevron-left"); }
	

      }

function cityList(code){
	//alert(code);

	 $.ajax({
		url: "/ajax/citylist", 
		type: "post",
		data: {"code":code},  
		dataType: "text", 
		cache: false,
		beforeSend: function () {
    			$("#loadingAjax").css('display','block');
  		},
		success: function (response) {  
		
			$("#citylist").html(response);
		}
	}).done(function ( data ) {  
			$("#loadingAjax").css('display','none');
				
	});	
	
}


function deleteAddress(address_id){
	if(!confirm("are sure to delete this address ?")) {return false;}
	 $.ajax({
		url: "/ajax/deleteAddress/"+address_id, 
		type: "post",
		data: {"address_id":address_id},  
		dataType: "text", 
		cache: false,
		beforeSend: function () {
    			$("#loadingAjax").css('display','block');
  		},
		success: function (response) {  
		
			$("#citylist").html(response);
		}
	}).done(function ( data ) {  
			$("#loadingAjax").css('display','none');
				
	});	
	
}


$("input[name='default_address']").on('click' , function(){

//alert( $(this).val());
var address_id= $(this).val();
 $.ajax({
		url: "/ajax/makeDafaultAdress", 
		type: "post",
		data: {"address_id":address_id},  
		dataType: "text", 
		cache: false,
		beforeSend: function () {
    			$("#loadingAjax").css('display','block');
  		},
		success: function (response) {  
		
		console.log(response);	
		}
	}).done(function ( data ) {  
			$("#loadingAjax").css('display','none');
				
	});	


});
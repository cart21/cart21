<?php /* Smarty version Smarty-3.1.12, created on 2015-05-13 02:29:54
         compiled from "/Applications/XAMPP/xamppfiles/htdocs/cart21/install_folder/templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:183324603055528616a48802-52108799%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8cc420cfc1c2c4924d254daedeb695ffaaf76df6' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/cart21/install_folder/templates/index.tpl',
      1 => 1431476992,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '183324603055528616a48802-52108799',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_55528616ab8e98_89193059',
  'variables' => 
  array (
    'errors' => 0,
    'error' => 0,
    'success' => 0,
    'succes' => 0,
    'POST' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55528616ab8e98_89193059')) {function content_55528616ab8e98_89193059($_smarty_tpl) {?>
	 <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <title>cart21 Installation</title>
    <meta name="description" content="install cart21">
    <meta name="keywords" content="install cart21">
    <meta name="author" content="Müslüm ÇEN">
    
    
    <link rel="icon" href="favicon.ico">


    <!-- Bootstrap core CSS -->
    <link href="/assets/bootstrap-3.2.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/front/assets/plugins/font-awesome-4.2.0/css/font-awesome.min.css"
    

    <!-- Custom styles for this template -->
    <link href="/assets/front/assets/css/general.css" rel="stylesheet">
    
    <script src="/assets/front/assets/js/jquery.min-1.11.1.js"></script>
    <script src="/assets/bootstrap-3.2.0/js/bootstrap.min.js"></script>
    
    </head>
    
  <body>
  
  
  
      <!-- validation -->
    
     <script src="/assets/front/assets/plugins/validator/jquery.validate.js"></script>
     <script src="/assets/front/assets/plugins/validator/localization/messages_en.js"></script>
	<script>


	$().ready(function() {

		$("#login_form").validate();
		$("#register_form").validate();
	
	});
	</script>
   <div class="container">	
	<!-- validation -->
<div class="row main padding">
    
		    <h1 class="page-header no-margin">Cart21 Installation</h1>
	  		
			<div class="row">
			
				<div class="col-md-7">
				<div class="col-md-12 ">
					
					<br><br>
					<?php if (is_array($_smarty_tpl->tpl_vars['errors']->value)){?>
<div class="clearfix"></div>
<br>
                <div class="alert alert-danger " role="alert">
            	<?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value){
$_smarty_tpl->tpl_vars['error']->_loop = true;
?>
                  <?php echo $_smarty_tpl->tpl_vars['error']->value;?>

                  <div class="clearfix"></div>
                <?php } ?>	
                </div>
            
<?php }?>

<?php if (is_array($_smarty_tpl->tpl_vars['success']->value)){?>
	 		<div class="alert alert-success">
            		<?php  $_smarty_tpl->tpl_vars['succes'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['succes']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['success']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['succes']->key => $_smarty_tpl->tpl_vars['succes']->value){
$_smarty_tpl->tpl_vars['succes']->_loop = true;
?>
            		
                   <?php echo ucwords($_smarty_tpl->tpl_vars['succes']->value);?>

                   <div class="clearfix"></div>
                    <?php } ?>
				</div>

<?php }?>
					
					
						<form class="form-horizontal" action="" method="post" enctype="multipart/form-data" role="form" id="register_form" ">
						  				
						  					  
										<div class="form-group">
											<label class="col-sm-3 control-label" >Website Title</label>
											<div class="col-md-6">
	                                        <input type="text"  name="title" class="form-control" placeholder="shop name"  value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['title'])){?><?php echo $_smarty_tpl->tpl_vars['POST']->value['title'];?>
<?php }?>" >
	                                        </div>
	                                    </div>
	                                    
	                                   
	                                <hr>
						  				
						  				<div class="form-group">
						  					<label class="col-sm-3 control-label" >Host:</label>
						  					<div class="col-md-4">
	                                        <input type="text" name="host" class="form-control"    value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['host'])){?><?php echo $_smarty_tpl->tpl_vars['POST']->value['host'];?>
<?php }else{ ?>localhost<?php }?>" >
	                                        </div>
	                                    </div>
										  <div class="clearfix"></div>
										  
										  <div class="form-group">
										  	<label class="col-sm-3 control-label" >Database User:</label>
										  	<div class="col-md-4">
	                                        <input type="text" name="user" class="form-control"   value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['user'])){?><?php echo $_smarty_tpl->tpl_vars['POST']->value['user'];?>
<?php }?>" >
	                                        </div>
	                                    </div>
										  <div class="clearfix"></div>
										 
										 <div class="form-group">
										 	<label class="col-sm-3 control-label" >Database Pass:</label>
										 	<div class="col-md-4">
	                                        <input type="text" name="pass" class="form-control"   value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['pass'])){?><?php echo $_smarty_tpl->tpl_vars['POST']->value['pass'];?>
<?php }?>" >
	                                        </div>
	                                    </div>
										  <div class="clearfix"></div>
										 
										 <div class="form-group">
										 	<label class="col-sm-3 control-label" >Database Name:</label>
										 	<div class="col-md-4">
	                                        <input type="text" name="database" class="form-control"   value="<?php if (isset($_smarty_tpl->tpl_vars['POST']->value['database'])){?><?php echo $_smarty_tpl->tpl_vars['POST']->value['database'];?>
<?php }?>" >
	                                        </div>
	                                    </div>
										  <div class="clearfix"></div>
									    
	                                  
	                  
						  <div class="form-group">
						    <div class="col-md-12">
						      <button type="submit" class="btn btn-primary bg-navy pull-right ">Install</button>
						    </div>
						  </div>
						</form>
						
						
				</div>
				</div>
				
			</div>
			
</div>
	
  </div>
  
  </body>
  </html><?php }} ?>
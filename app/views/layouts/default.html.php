<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License

 */
 use lithium\storage\Session;
 use app\models\Pages;
 use app\models\Details; 
	 use app\models\Parameters; 
 
 if(!isset($title)){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>'home')
		));
 		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
 }
$user = Session::read('default');
$detail = Details::find("first",array(
			"conditions"=>array("user_id"=>$user["_id"])
));
$parameters = Parameters::find('first');
if(count($detail)==0){
	$theme = "default";
}else{
	if($detail["theme"]==""){$theme = "default";}else{$theme = $detail["theme"];}
}

	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pagestarttime = $mtime; 


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="<?php if(isset($keywords)){echo $keywords;} ?>">	
	<meta name="description" content="<?php if(isset($description)){echo $description;} ?>">		
	<meta name="author" content="">
	<link rel="shortcut icon" href="/img/logo-MultiSigX.gif" />
	<title><?php echo MAIN_TITLE;?><?php if(isset($title)){echo $title;} ?></title>
	<!-- Bootstrap Core CSS -->
	<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
	
	<!-- Custom CSS -->
	<?php if($this->_request->controller=="Pages"){?>	
	<link href="/bootstrap/css/grayscale.css" rel="stylesheet">
	<?php }else{?>
	<link href="/bootstrap/css/dashboard.css" rel="stylesheet">	
	<?php }?>
	<!-- Custom styles for this template -->
	
				
	<!-- Custom Fonts -->
	<link href="/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/bootstrap/js/bootstrap-datepicker.js"></script>	
	<?php
	$this->scripts('<script src="/js/main.js?v='.rand(1,100000000).'"></script>'); 	
	?>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top" <?php if(
				$this->_request->controller!='Company' 
	&& $this->_request->controller!='Sessions' 
	&& $this->_request->controller!='Admin'
	&& $this->_request->controller!='API'
	){?> onLoad="UpdateDetails();" <?php }?>>


				<?php if($this->_request->controller=="Pages"){?>
					<?php echo $this->_render('element', 'carousel');?>	
					<?php echo $this->_render('element', 'feature');?>						
				<?php }else{?>	
					<?php echo $this->_render('element', 'header');?>				
				<?php }?>
				<div class="<?=$this->_request->action?> 				<?php if($this->_request->controller!="Pages"){?> mainpage<?php }?>">
				<?php if(strtolower($this->_request->controller)=='admin'){ ?>
					<?php echo $this->_render('element', 'admin');?>
				<?php }?>
					<?php if($parameters['server']==false){?>
					<h1 class="alert alert-danger">Server is down for maintenance!</h1>
					<?php }?>
					<?php echo $this->content(); ?>
				</div>
<?php 
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pageendtime = $mtime;
	$pagetotaltime = ($pageendtime - $pagestarttime);
?>
				<?php if($this->_request->controller=="Pages"){?>
					<?php echo $this->_render('element', 'footer');?>	
				<?php }else{?>	
					<?php echo $this->_render('element', 'footerall');?>	
				<?php }?>
		<?php echo $this->scripts(); ?>	
		<!-- jQuery -->
		
		<!-- Bootstrap Core JavaScript -->
		<script src="/bootstrap/js/bootstrap.min.js"></script>
		<!-- Plugin JavaScript -->
		<script src="/js/jquery.easing.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="/js/grayscale.js"></script>
	</body>
</html>
<script type="text/javascript">
$(function() {
 $('.tooltip-x').tooltip();
 $("input:text:visible:first").focus();
});
</script>
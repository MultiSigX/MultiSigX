<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2012, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License

 */
 use lithium\storage\Session;
 use app\models\Pages;
 if(!isset($title)){
		$page = Pages::find('first',array(
			'conditions'=>array('pagename'=>'home')
		));
 		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
 }

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
		<link rel="shortcut icon" href="/img/MultiSigX.gif" />
		<title><?php echo MAIN_TITLE;?><?php if(isset($title)){echo $title;} ?></title>

    <!-- Bootstrap core CSS -->
    <link href="/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/bootstrap/css/dashboard.css?v=<?=rand(1,100000000)?>" rel="stylesheet"> 
<style type="text/css">
body {
	font-family: 'Open Sans', sans-serif;
}
</style>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800italic' rel='stylesheet' type='text/css'>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="/bootstrap/js/bootstrap-datepicker.js"></script>	
	<?php
	$this->scripts('<script src="/js/main.js?v='.rand(1,100000000).'"></script>'); 	
	?>
</head>
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<body>
    <div class="container">
				<?php echo $this->_render('element', 'header');?>				
				<?php if($this->_request->controller=="Pages"){?>
					<?php echo $this->_render('element', 'carousel');?>	
					<?php echo $this->_render('element', 'feature');?>						
				<?php }?>	
				<div class="<?=$this->_request->action?> 				<?php if($this->_request->controller!="Pages"){?> mainpage<?php }?>">
					<?php echo $this->content(); ?>
				</div>
				<?php echo $this->_render('element', 'footer');?>	
		</div> <!-- container-fluid -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
		<?php echo $this->scripts(); ?>	
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/bootstrap/js/docs.min.js"></script>
  </body>
</html>
<script type="text/javascript">
$(function() {
 $('.tooltip-x').tooltip();
 $("input:text:visible:first").focus();
});
</script>
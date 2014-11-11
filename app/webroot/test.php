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
	<link href="/bootstrap/css/grayscale.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="/bootstrap/css/dashboard.css?v=<?=rand(1,100000000)?>" rel="stylesheet"> 
				
	<!-- Custom Fonts -->
	<link href="/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
<script src="/bootstrap/js/bootstrap-datepicker.js"></script>	
	<?php
//	$this->scripts('<script src="/js/main.js?v='.rand(1,100000000).'"></script>'); 	
	?>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <!-- Navigation -->
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">
                    <i class="fa fa-play-circle"></i>  <span class="light">Bitcoin</span> MultiSigX Security
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#features">Features</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#signup">Signup</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading">Digital currency</h1>
                        <p class="intro-text">
																								In the dark world of bitcoin, security is a concern for users.<br>No wallets are safe from hackers.<br>
													<div class="row">
														<div class="col-lg-6 col-lg-offset-3">
															<img  src="/img/logo-MultiSigX.png" style="max-width:100%"/>
														</div>
													</div>

																								</p>
																								
                        <a href="#about" class="btn btn-circle page-scroll">
                            <i class="fa fa-angle-double-down animated"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section id="about" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
													<div class="row">
														<div class="col-lg-6 col-lg-offset-3">
															<img  src="/img/logo-MultiSigX.png" style="max-width:100%"/>
														</div>
													</div>

                <p>Bitcoin / GreenCoin Multi Signatures for everyone
																<br><code>PrivateKeys</code> or <code>Coins</code> are not on stored the server</p>
                <p>Security has been the main concern for everyone since the start of Cryptocurrencies. MultiSigX makes your coins extremely secure!</p>
																<a href="#features" class="btn btn-circle page-scroll">
                 <i class="fa fa-angle-double-down animated"></i>
                </a>
																<p></p>
            </div>
        </div>
    </section>


    <!-- Feature Section -->
    <section id="features" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
													<div class="row">
														<div class="col-lg-6 col-lg-offset-3">
															<img  src="/img/logo-MultiSigX.png" style="max-width:100%"/>
														</div>
													</div>
                <h2>Bitcoin / GreenCoin Multi Signatures <br>for everyone</h2>
                <p><code>PrivateKeys</code> or <code>Coins</code> are not on stored the server<br>
																2FA security, mobile alerts on withdrawal<br>
																Individuals<sup>*</sup> - NOMINAL 0.001% commission<br>
																Groups<sup>*</sup> - 1% commission on withdrawal<br>
																Business<sup>*</sup> -  2% commission on withdrawal</p>
                <ul class="list-inline banner-social-buttons">
																	<li>
																	<a href="https://github.com/MultiSigX/MultiSigX" class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i> <span class="network-name">Github</span></a>
																	</li>
                </ul>
																<a href="#signup" class="btn btn-circle page-scroll">
                 <i class="fa fa-angle-double-down animated"></i>
                </a>
																
            </div>
        </div>
    </section>

    <!-- Signup Section -->
    <section id="signup" class="content-section text-center">
        <div class="download-section">
            <div class="container">
                <div class="col-lg-8 col-lg-offset-2">
																<h3>One of ours users said:</h3>
																<p>With MultiSigX, I have used <code>2 of 3</code> and <code>3 of 3</code> security, I am able to sleep soundly with my computer, USB, phone connected to internet.</p>

                <p>You can start using MultiSigX just in a few clicks</p>
																<div class="col-lg-6 col-lg-offset-3">
																<img  src="/img/logo-MultiSigX.png" style="max-width:100%"/><br>
																<a href="/signup" class="btn btn-default btn-lg">Sign Up</a>
																<a href="/signin" class="btn btn-default btn-lg">Sign In</a>
																</div>
                 
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <div id="map"></div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
								<p>MultiSigX is a registered name of <strong>MultiSigX Inc. Apia, Samoa</strong> Company No: 65518</p>
        </div>
    </footer>

    <!-- jQuery -->
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/js/grayscale.js"></script>

</body>

</html>

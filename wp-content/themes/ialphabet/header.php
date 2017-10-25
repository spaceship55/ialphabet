<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ialphabet
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<!-- Mailchimp subscription popup code -->
<!-- <script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us14.list-manage.com","uuid":"4afc2e0e56505b1f8a970f141","lid":"bc3433a1da"}) })</script>

 -->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>









	<div class="header" style="background:#e9b943; color:#fff; margin-bottom:50px;">
		<div class="container" style="background:#DD1E27; padding:0;">
			<a href="http://www.albustanseeds.org/" target="_blank"><img src="/ialphabet/wp-content/themes/ialphabet/img/logo_1.png" style="float:left; display:block; width:140px; padding:10px 20px 10px 0; background:#e9b943;"/></a>	
			<a href="http://www.albustanseeds.org/ialphabet/"><img src="/ialphabet/wp-content/themes/ialphabet/img/logo.png" style="float:left; width:130px; padding:5px; margin:0 50px 0 10px; "/></a>	
		
			
		<div class="navbar-header" >
             <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
             </button>
			 
             <a class="navbar-brand" href="#"></a>
       </div>
       <div class="navbar-collapse collapse">
		   <?php /* Primary navigation */
		   wp_nav_menu( array(
		     'menu' => 'main',
		     'depth' => 2,
		     'container' => false,
		     'menu_class' => 'nav navbar-nav',
		     //Process nav menu using our custom nav walker
		     'walker' => new wp_bootstrap_navwalker())
		   );
		   ?>
             <ul class="nav navbar-nav navbar-right" style="padding-top:2px; color:#fff; margin-right:0px;">
			
			 <div class="social-icons" style="float:right;">
			   	<a href="https://www.facebook.com/pages/Al-Bustan-Seeds-of-Culture/5740039617" target="_blank"><i class="fa fa-facebook"></i></a>
			   	<a href="https://twitter.com/albustanseeds" target="_blank"><i class="fa fa-twitter"></i></a>
				<a href="https://www.instagram.com/albustanseeds/" target="_blank"><i class="fa fa-instagram"></i></a>     
			   	<a href="https://albustanblog.wordpress.com/" target="_blank"><i class="fa fa-wordpress"></i></a>
			
				<!--<i class="fa fa-calendar"></i>-->
				<a href="http://www.albustanseeds.org/about/contact-us/"><i class="fa fa-phone-square"></i></a>
			</div>

		</div>
			 
		

		</div>
	</div>


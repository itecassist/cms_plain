<?php
/**
 * Example: index.htm with editable zones
 * This demonstrates how to add data-editable attributes to make content editable
 */
require_once '../config.php';
require_once '../functions.php';

// Get content
$phone = get_content('index.htm', 'header-phone', '1-800-488-6040');
$hours = get_content('index.htm', 'header-hours', 'Mon - Fri: 8:00AM - 7:00PM | Sat - Sun: Closed');
$hero_title = get_content('index.htm', 'hero-title', 'push <span>yourself</span>');
$hero_description = get_content('index.htm', 'hero-description', 'Maecenas consequat ex id lobortis venenatis. Mauris id erat enim. Morbi dolor dolor, auctor tincidunt lorem ut, venenatis dapibus mi. Nunc venenatis sollicitudin nisl vel auctor.');
$welcome_title = get_content('index.htm', 'welcome-title', 'Welcome To <span>Crossfit</span>');
$welcome_slogan = get_content('index.htm', 'welcome-slogan', 'Maecenas consequat ex id lobortis venenatis. Mauris id erat enim. Morbi dolor dolor, auctor tincidunt lorem ut, venenatis dapibus miq.');
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
	<meta charset="UTF-8">
	<title>Fitmax</title>
	<!-- =================== META =================== -->
	<meta name="keywords" content="">
	<meta name="description" content="">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="assets/img/favicon.png">
	<!-- =================== STYLE =================== -->
	<link rel="stylesheet" href="assets/css/slick.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-grid.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
</head>

<body id="home">
	<!--================ PRELOADER ================-->
	<div class="preloader-cover">
		<div id="cube-loader">
			<div class="caption">
				<div class="cube-loader">
					<div class="cube loader-1"></div>
					<div class="cube loader-2"></div>
					<div class="cube loader-4"></div>
					<div class="cube loader-3"></div>
				</div>
			</div>
		</div>
	</div>
	<!--============== PRELOADER END ==============-->
	
	<!-- ================= HEADER ================= -->
	<header class="header">
		<a href="#" class="nav-btn">
			<span></span>
			<span></span>
			<span></span>
		</a>
		<div class="top-panel">
			<div class="container">
				<div class="header-left">
					<ul class="header-cont">
						<li data-editable="header-phone"><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:18004886040"><?php echo $phone; ?></a></li>
						<li data-editable="header-hours"><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo $hours; ?></li>
					</ul>
				</div>
				<div class="header-right">
					<form class="search-form">
						<input type="search" class="search-form__field" placeholder="Search" value="" name="s">
						<button type="submit" class="search-form__submit"><i class="fa fa-search" aria-hidden="true"></i></button>
					</form>
					<ul class="social-list">
						<li><a target="_blank" href="https://www.facebook.com/rovadex"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="https://twitter.com/RovadexStudio"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="https://www.youtube.com"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="https://www.instagram.com/rovadex"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="header-menu">
			<div class="container">
				<div class="header-logo">
					<a href="index.html.htm" class="logo"><img src="assets/img/logo.svg" alt="logo"></a>
				</div>
				<nav class="nav-menu">
					<ul class="nav-list">
						<li class="dropdown">
							<a href="#">Home <i class="fa fa-caret-down"></i></a>
							<ul>
								<li class="menu-active"><a href="index.html.htm">Crossfit</a></li>
								<li><a href="home-fitness.html.htm">Fitness</a></li>
								<li><a href="home-crossfit-lite.html.htm">Crossfit Lite</a></li>
							</ul>
						</li>
						<li><a href="about.html.htm">About</a></li>
						<li><a href="services.html.htm">Services</a></li>
						<li class="dropdown">
							<a href="#">Pages <i class="fa fa-caret-down"></i></a>
							<ul>
								<li><a href="program.html.htm">Program</a></li>
								<li><a href="trainer.html.htm">Trainer</a></li>
							</ul>
						</li>
						<li><a href="blog.html.htm">News</a></li>
						<li><a href="contacts.html.htm">Contacts</a></li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
	<!-- =============== HEADER END =============== -->

	<!-- ============ S-CROSSFIT-SLIDER ============ -->
	<section class="s-crossfit-slider">
		<div class="crossfit-slider">
			<div class="crossfit-slide">
				<div class="crossfit-slider-effect effect-1">
					<div data-hover-only="true" class="scene">
						<span class="scene-item" data-depth="0.2" style="background-image: url(assets/img/effect-1.svg);"></span>
					</div>
				</div>
				<div class="crossfit-slider-effect effect-2">
					<div data-hover-only="true" class="scene">
						<span class="scene-item" data-depth="0.4" style="background-image: url(assets/img/effect-2.svg);"></span>
					</div>
				</div>
				<div class="crossfit-slide-bg" style="background-image: url(assets/img/slide-1.jpg);"></div>
				<div class="container">
					<div class="crossfit-slide-cover">
						<h2 class="title" data-editable="hero-title"><?php echo $hero_title; ?></h2>
						<p data-editable="hero-description"><?php echo $hero_description; ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="slider-navigation">
			<div class="container">
				<div class="slider-navigation-cover"></div>
			</div>
		</div>
	</section>
	<!-- ========== S-CROSSFIT-SLIDER END ========== -->

	<!-- ================ S-CROSSFIT ================ -->
	<section class="s-crossfit">
		<div class="container">
			<img src="assets/img/placeholder-all.png" data-src="assets/img/group-circle-2.svg" alt="img" class="crossfit-icon-1 rx-lazy">
			<img src="assets/img/placeholder-all.png" data-src="assets/img/line-red-1.svg" alt="img" class="crossfit-icon-2 rx-lazy">
			<img src="assets/img/placeholder-all.png" data-src="assets/img/tringle-about-top.svg" alt="img" class="crossfit-icon-3 rx-lazy">
			<h2 class="title-decor" data-editable="welcome-title"><?php echo $welcome_title; ?></h2>
			<p class="slogan" data-editable="welcome-slogan"><?php echo $welcome_slogan; ?></p>
			<div class="row">
				<div class="col-md-4 crossfit-col">
					<div class="crossfit-item">
						<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-1.svg" alt="img">
						<h3 data-editable="service1-title">body bulding</h3>
						<p data-editable="service1-description">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
						<a class="btn" href="program.html.htm">view Schedule</a>
					</div>
				</div>
				<div class="col-md-4 crossfit-col">
					<div class="crossfit-item">
						<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-2.svg" alt="img">
						<h3 data-editable="service2-title">group workouts</h3>
						<p data-editable="service2-description">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
						<a class="btn" href="program.html.htm">view Schedule</a>
					</div>
				</div>
				<div class="col-md-4 crossfit-col">
					<div class="crossfit-item">
						<img class="rx-lazy" src="assets/img/placeholder-all.png" data-src="assets/img/serv-3.svg" alt="img">
						<h3 data-editable="service3-title">boxing</h3>
						<p data-editable="service3-description">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
						<a class="btn" href="program.html.htm">view Schedule</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- ============== S-CROSSFIT END ============== -->

	<!-- Rest of your HTML here -->

	<!-- =================== SCRIPT =================== -->
	<script src="assets/js/jquery-2.2.4.min.js"></script>
	<script src="assets/js/slick.min.js"></script>
	<script src="assets/js/rx-lazy.js"></script>
	<script src="assets/js/parallax.min.js"></script>
	<script src="assets/js/scripts.js"></script>
</body>
</html>

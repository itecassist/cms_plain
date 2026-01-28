<!DOCTYPE html>
<html lang="zxx">
<head>
	<meta charset="UTF-8">
	<title><?php echo htmlspecialchars($seo_data['title'] ?? SITE_NAME); ?></title>
	<!-- =================== META =================== -->
	<meta name="keywords" content="<?php echo htmlspecialchars($seo_data['keywords'] ?? ''); ?>">
	<meta name="description" content="<?php echo htmlspecialchars($seo_data['description'] ?? ''); ?>">
	<meta name="format-detection" content="telephone=no">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<!-- Open Graph / Social Media Meta Tags -->
	<?php if (!empty($seo_data['og_title'])): ?>
	<meta property="og:title" content="<?php echo htmlspecialchars($seo_data['og_title']); ?>">
	<?php endif; ?>
	<?php if (!empty($seo_data['og_description'])): ?>
	<meta property="og:description" content="<?php echo htmlspecialchars($seo_data['og_description']); ?>">
	<?php endif; ?>
	<?php if (!empty($seo_data['og_image'])): ?>
	<meta property="og:image" content="<?php echo htmlspecialchars($seo_data['og_image']); ?>">
	<?php endif; ?>
	<meta property="og:type" content="website">
	
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
						<li data-editable="header-phone"><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:18004886040"><?php echo get_content('_global', 'header-phone', '1-800-488-6040'); ?></a></li>
						<li data-editable="header-hours"><i class="fa fa-clock-o" aria-hidden="true"></i><?php echo get_content('_global', 'header-hours', 'Mon - Fri: 8:00AM - 7:00PM | Sat - Sun: Closed'); ?></li>
					</ul>
				</div>
				<div class="header-right">
					<form class="search-form">
						<input type="search" class="search-form__field" placeholder="Search" value="" name="s">
						<button type="submit" class="search-form__submit"><i class="fa fa-search" aria-hidden="true"></i></button>
					</form>
					<ul class="social-list">
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-facebook', 'https://www.facebook.com'); ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-twitter', 'https://twitter.com'); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-youtube', 'https://www.youtube.com'); ?>"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-instagram', 'https://www.instagram.com'); ?>"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
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
						<?php echo render_menu(); ?>
					</ul>
				</nav>
			</div>
		</div>
	</header>
	<!-- =============== HEADER END =============== -->

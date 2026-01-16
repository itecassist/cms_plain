	<!-- ================ FOOTER ================ -->
	<footer class="footer">
		<div class="container">
			<div class="row footer-row">
				<div class="col-sm-6 col-lg-3 footer-item">
					<a href="index.html.htm" class="logo"><img src="assets/img/logo-footer.svg" alt="logo"></a>
					<p data-editable="footer-about"><?php echo get_content('_global', 'footer-about', 'Maecenas consequat ex id lobortis venenatis. Mauris id erat enim. Morbi dolor dolor, auctor tincidunt lorem ut, venenatis dapibus mi. Nunc venenatis sollicitudin nisl vel auctor.'); ?></p>
					<ul class="social-list">
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-facebook', 'https://www.facebook.com'); ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-twitter', 'https://twitter.com'); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-youtube', 'https://www.youtube.com'); ?>"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
						<li><a target="_blank" href="<?php echo get_content('_global', 'social-instagram', 'https://www.instagram.com'); ?>"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
					</ul>
				</div>
				<div class="col-sm-6 col-lg-3 footer-item">
					<h3>Twitter</h3>
					<div class="twitter">
						<div class="twitter-item">
							<i class="fa fa-twitter" aria-hidden="true"></i>
							<p data-editable="footer-tweet1"><?php echo get_content('_global', 'footer-tweet1', 'Maecenas consequat ex id lobortis venenatis. Mauris id erat enim.'); ?></p>
						</div>
						<div class="twitter-item">
							<i class="fa fa-twitter" aria-hidden="true"></i>
							<p data-editable="footer-tweet2"><?php echo get_content('_global', 'footer-tweet2', 'Morbi dolor dolor, auctor tincidunt lorem ut, venenatis dapibus mi.'); ?></p>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-lg-3 footer-item">
					<h3>Contact Us</h3>
					<ul class="footer-cont">
						<li data-editable="footer-phone"><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:18004886040"><?php echo get_content('_global', 'footer-phone', '1-800-488-6040'); ?></a></li>
						<li data-editable="footer-email"><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:crossFit@gmail.com"><?php echo get_content('_global', 'footer-email', 'CrossFit@gmail.com'); ?></a></li>
						<li data-editable="footer-address"><i class="fa fa-map-marker" aria-hidden="true"></i><a href="contacts.html.htm"><?php echo get_content('_global', 'footer-address', 'London, Street 225r.21'); ?></a></li>
					</ul>
				</div>
				<div class="col-sm-6 col-lg-3 footer-item">
					<h3>Blog</h3>
					<ul class="footer-blog">
						<li>
							<a href="blog.html.htm" class="img-cover"><img src="assets/img/footer-icon-1.jpg" alt="img"></a>
							<div class="footer-blog-info">
								<div class="name"><a href="blog.html.htm">Sed ut perspiciatis</a></div>
								<p>Omnis iste natus error sit voluptatem…</p>
							</div>
						</li>
						<li>
							<a href="blog.html.htm" class="img-cover"><img src="assets/img/footer-icon-2.jpg" alt="img"></a>
							<div class="footer-blog-info">
								<div class="name"><a href="blog.html.htm">Sed ut perspiciatis</a></div>
								<p>Omnis iste natus error sit voluptatem…</p>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="footer-bottom">
				<div class="copyright" data-editable="footer-copyright"><a href="#" target="_blank">Rovadex</a> © <?php echo date('Y'); ?>. <?php echo get_content('_global', 'footer-copyright', 'Fitmax. All Rights Reserved.'); ?></div>
				<ul class="footer-menu">
					<li class="active"><a href="index.html.htm">Home</a></li>
					<li><a href="about.html.htm">About</a></li>
					<li><a href="services.html.htm">Services</a></li>
					<li><a href="blog.html.htm">News</a></li>
					<li><a href="contacts.html.htm">Contacts</a></li>
				</ul>
			</div>
		</div>
	</footer>
	<!-- ================ FOOTER END ================ -->

	<!--=================== TO TOP ===================-->
	<a class="to-top" href="#home">
		<i class="fa fa-chevron-up" aria-hidden="true"></i>
	</a>
	<!--================= TO TOP END =================-->

	<!--=================== SCRIPT	===================-->
	<script src="assets/js/jquery-2.2.4.min.js"></script>
	<script src="assets/js/slick.min.js"></script>
	<script src="assets/js/rx-lazy.js"></script>
	<script src="assets/js/parallax.min.js"></script>
	<script src="assets/js/scripts.js"></script>
</body>
</html>

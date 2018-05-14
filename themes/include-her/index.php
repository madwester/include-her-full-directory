<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Include_Her
 */

get_header();
?>
<main>
	<div class="feature featureHome">
		<h1>We love to speak about code.<br>Welcome in!</h1>
		<button type="button" class="btn featureBtn">Join us!</button>
	</div>
	<div class="container containerHome">
		<div class="flexRow">
			<div class="homepageItem large blue">
				<p>In our social section we want you to arrange meet up with friends and new friends. 
				We think coding together is the best way to code. A meetup can be small or large, whatever you prefer!</p>
				<a href="#">Meet<i class="fa fa-angle-double-right"></i></a>
			</div>
			<div class="homepageItem small pink">
				<p>Include her is an online community where anyone who is interested in coding is welcome. Read more about our story.</p>
				<a href="about.html">About us<i class="fa fa-angle-double-right"></i></a>
			</div>
		</div>
		<div class="flexRow">
			<div class="homepageItem small gray">
				<p>Have you got any questions? Please don't hestitate to get in touch.</p>
				<a href="#">Connect<i class="fa fa-angle-double-right"></i></a>
			</div>
			<div class="homepageItem large yellow">
				<p>Everyone knows being stuck is a part of coding, and it can be exhausting. In our forum our members are extremly 
				helpful to solve your issues. </p>
				<a href="#">Forum<i class="fa fa-angle-double-right"></i></a>
			</div>
		</div>
	</div>
</main>

<?php get_footer();?>

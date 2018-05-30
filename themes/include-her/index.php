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
		<?php if(!is_user_logged_in()) { ?>
			<h1>We love to speak about code.<br>Welcome in!</h1>
			<button type="button" class="btn featureBtn pink">Join us!</button>
		<?php } else { ?>
			<h1>Are you being stuck with anything in your code? Ask for help in our forum.</h1>
			<button type="button" class="btn featureBtn yellow">Forum</button>
		<?php }?>
	</div>
	<div class="container-fluid containerHome content">
		<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
				<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner" role="listbox">
				<div class="carousel-item blue active">
					<div class="carouselCaption">
						<h2>Did you know that,</h2>
						<h4>Fewer women run top Australian companies than <br>men called John - or Peter, or David.</h4>
					</div>
				</div>
				<div class="carousel-item pink">
					<div class="carouselCaption">
						<h2>Here's a fun fact (or depressing)!</h2>
						<h4>Out of every 100 software developers/engineers in <br>Los Angeles, approximately 10-12 are female.</h4>
					</div>
				</div>
				<div class="carousel-item yellow">
					<div class="carouselCaption">
						<h2>Did you know that,</h2>
						<h4>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem <br>Ipsum has been the industry's.</h4>
					</div>
				</div>
			</div>
			<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
		<div class="flexRow">
			<div class="homepageItem forumItem">
				<div class="homepageItemCaption">
					<h2>Are you being stuck in your code?</h2>
					<h5>Ask any of your coding sisters for help.</h5>
					<button class="homepageBtn homepageBtnYellow">Forum</button>
				</div>
			</div>
			<div class="homepageItem pink">
				<div class="homepageItemCaption">
					<h4>When was the last time you met up with someone to code? Our members regularly organize events, meet up and hackathons to join. 
						Have a look to see when the next one happens around you.</h4>
					<button class="homepageBtn homepageBtnBlack">Events</button>
				</div>
			</div>
		</div>
		<div class="flexRow">
			<div class="homepageItem blue">
				<div class="homepageItemCaption">
					<h4>Are you curious to hear our story?</h4>
					<button class="homepageBtn homepageBtnWhite">About</button>
				</div>
			</div>
			<div class="homepageItem contactItem">
			<div class="homepageItemCaption">
					<h2>Have you got any questions or tips on how to improve us?</h2>
					<h5>Your opinios will make a difference.</h5>
					<button class="homepageBtn homepageBtnGray">Contact</button>
				</div>
			</div>
		</div>
		<div id="instafeed"></div>
	</div>
</main>

<?php get_footer();?>

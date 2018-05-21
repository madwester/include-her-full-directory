<?php
/*
Template Name: Contact Us
*/
?>
<?php get_header(); ?>
<main>  
<div class="feature featureContact">
    <h1>Dont' be shy. Get in touch.</h1>
</div>
    <div class="container">
        <div class="row">
            <div class="col-md-3 blue leftItemContact">
                <h2>Follow us online</h2>
                <ul>
                    <li><i class="fas fa-plus"></i><a href="">Instagram</a></li>
                    <li><i class="fas fa-plus"></i><a href="">Facebook</a></li>
                    <li><i class="fas fa-plus"></i><a href="">Google</a></li>
                </ul>
            </div>
            <div class="col-md-9 rightItemContact">
                <?php echo do_shortcode( '[contact-form-7 id="129" title="Main Contact Form"]' ); ?>
            </div>
        </div>
    </div>  
</main>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Include_Her
 */

?>

	<!-- </div>#content -->

<footer>
      <div class="flexRow">
        <div class="itemFoot">
          <img src="<?php bloginfo('template_directory')?>/build/images/include-logo.png">
        </div>
        <div class="itemFoot align-right">
          <ul class="socialMediaList">
            <li><i class="fab fa-instagram"></i></li>
            <li><i class="fab fa-facebook-f"></i></li>
            <li><i class="fab fa-google"></i></li>
          </ul>
        </div>
      </div>
      <hr> 
      <div class="flexRow">
        <div class="itemFoot">
          <p>Copyright &copy;	2017. All rights reserved.</p>
        </div>
      </div>
    </footer>
	<?php wp_footer(); ?>
  </body>
</html>
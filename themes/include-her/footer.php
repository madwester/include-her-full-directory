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
            <li><a href="https://www.facebook.com/Include-Her-422232668245105/?modal=admin_todo_tour"><i class="fab fa-instagram"></i></a></li>
            <li><a href="https://www.instagram.com/includeherau"><i class="fab fa-facebook-f"></i></a></li>
          </ul>
        </div>
      </div>
      <hr> 
      <div class="flexRow">
        <div class="itemFoot">
          <p>Copyright &copy;	2018. All rights reserved.</p>
        </div>
      </div>
    </footer>
	<?php wp_footer(); ?>
  </body>
</html>
<?php
/**
 * Template for site footer
 * @package themify
 * @since 1.0.0
 */

/** Themify Default Variables
 *  @var object */
	global $themify; ?>

                    <?php themify_layout_after(); // hook ?>
			</div>
			<!-- /body -->
			<?php if ( 'yes' != $themify->hide_footer ) : ?>
			<div id="footerwrap">

				<?php themify_footer_before(); // hook ?>

				<footer id="footer" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPFooter">

					<?php themify_footer_start(); // hook ?>

					<?php get_template_part( 'includes/footer-widgets' ); ?>

					

					<?php themify_footer_end(); // hook ?>

				</footer>
				<!-- /#footer -->

				<?php themify_footer_after(); // hook ?>

			</div>
			<!-- /#footerwrap -->
			
		<?php endif; // exclude footer ?>
		
		</div>
		<!-- /#pagewrap -->

		<?php
		/**
		 *  Stylesheets and Javascript files are enqueued in theme-functions.php
		 */
		?>

		<!-- wp_footer -->
		<?php wp_footer(); ?>
		<?php themify_body_end(); // hook ?>
	</body>
</html>
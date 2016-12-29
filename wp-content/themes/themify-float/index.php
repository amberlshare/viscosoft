<?php
/**
 * Template for common archive pages, author and search results
 * @package themify
 * @since 1.0.0
 */
?>
<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix">

	<!-- content -->
    <?php themify_content_before(); //hook ?>
	<div id="content" class="list-post">
    	<?php themify_content_start(); //hook ?>

		<?php
		/////////////////////////////////////////////
		// Author Page
		/////////////////////////////////////////////
		if(is_author()) : ?>
			<?php
			global $author, $author_name;
			$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
			$author_url = $curauth->user_url;
			?>
			<div class="author-bio clearfix">
				<p class="author-avatar"><?php echo get_avatar( $curauth->user_email, $size = '48' ); ?></p>
				<h2 class="author-name"><?php _e('About','themify'); ?> <?php echo esc_html( $curauth->first_name ); ?> <?php echo esc_html( $curauth->last_name ); ?></h2>
				<?php if($author_url != ''): ?><p class="author-url"><a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_url( $author_url ); ?></a></p><?php endif; //author url ?>
				<div class="author-description">
					<?php echo wp_kses_post( $curauth->user_description ); ?>
				</div>
				<!-- /.author-description -->
			</div>
			<!-- /.author bio -->

			<h2 class="author-posts-by"><?php _e('Posts by','themify'); ?> <?php echo esc_html( $curauth->first_name ); ?> <?php echo esc_html( $curauth->last_name ); ?>:</h2>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// Search Title
		/////////////////////////////////////////////
		?>
		<?php if( is_search() ): ?>
			<h1 class="page-title"><?php _e('Search Results for:','themify'); ?> <em><?php echo get_search_query(); ?></em></h1>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// Date Archive Title
		/////////////////////////////////////////////
		?>
		<?php if ( is_day() ) : ?>
			<h1 class="page-title"><?php printf( __( 'Daily Archives: <span>%s</span>', 'themify' ), get_the_date() ); ?></h1>
		<?php elseif ( is_month() ) : ?>
			<h1 class="page-title"><?php printf( __( 'Monthly Archives: <span>%s</span>', 'themify' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'themify' ) ) ); ?></h1>
		<?php elseif ( is_year() ) : ?>
			<h1 class="page-title"><?php printf( __( 'Yearly Archives: <span>%s</span>', 'themify' ), get_the_date( _x( 'Y', 'yearly archives date format', 'themify' ) ) ); ?></h1>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// Category Title
		/////////////////////////////////////////////
		?>
		<?php if( is_category() || is_tag() || is_tax() ): ?>
			<h1 class="page-title"><?php single_cat_title(); ?></h1>
			<?php echo themify_get_term_description(); ?>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// Default query categories
		/////////////////////////////////////////////
		?>
		<?php if( !is_search() ): ?>
			<?php
				global $query_string;
				query_posts( apply_filters( 'themify_query_posts_args', $query_string.'&order='.$themify->order.'&orderby='.$themify->orderby ) );
			?>
		<?php endif; ?>

		<?php
		/////////////////////////////////////////////
		// Loop
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : ?>

			<!-- loops-wrapper -->
			<div id="loops-wrapper" class="loops-wrapper clearfix <?php echo esc_attr( themify_theme_query_classes() ); ?>">
				<?php if(is_tax('portfolio-category')):?>
					<?php 
						$themify->query_taxonomy ='portfolio-category';
						$tax = get_queried_object();
						while (have_posts()){ 
							the_post();
							$categories = wp_get_object_terms(get_the_ID(), $themify->query_taxonomy  );
							foreach($categories as $cat){
								$themify->query_category[] = $cat->term_id;
							}
						}
						$themify->query_category = array_unique($themify->query_category);
						rewind_posts();
						$themify->is_isotop = true;
					?>
					<?php get_template_part( 'includes/filter', get_post_type() ); ?>
				<?php endif;?>
				<?php while (have_posts()) : the_post(); ?>

					<?php get_template_part( 'includes/loop', get_post_type() ); ?>
		
				<?php endwhile; ?>
							
			</div>
                        <!-- /loops-wrapper -->
			<?php get_template_part( 'includes/pagination'); ?>

		<?php
		/////////////////////////////////////////////
		// Error - No Page Found
		/////////////////////////////////////////////
		?>

		<?php else : ?>

			<p><?php _e( 'Sorry, nothing found.', 'themify' ); ?></p>

		<?php endif; ?>
	<?php themify_content_end(); //hook ?>
	</div>
    <?php themify_content_after(); //hook ?>
	<!-- /#content -->

	<?php
	/////////////////////////////////////////////
	// Sidebar
	/////////////////////////////////////////////
	if ($themify->layout != 'sidebar-none'): get_sidebar(); endif; ?>

</div>
<!-- /#layout -->

<?php get_footer(); ?>

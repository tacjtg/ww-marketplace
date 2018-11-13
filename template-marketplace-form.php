<?php
/**
 * Template Name: Marketplace Form
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !is_user_logged_in() ) {
	wp_redirect( wp_login_url() ); exit;
}

acf_form_head();
get_header(); ?>

<div class="ww-container">
	<div class="ww-back-link"><a href="<?php echo get_post_type_archive_link( 'ww_listing' ); ?>" class="ww-btn">Back to Marketplace</a></div>
	<div style="clear:both"></div>
	
	<header class="entry-header">
		<h1 class="entry-title"></h1>
	</header>	
	
	<div class="bootstrap-wrapper">
		<div class="container">	
			
			<div class="row">
				
				<div class="col-lg-3 col-md-12 col-sm-12">
					<div class="ww-navbox">						
						<h3>Listing Categories</h3>											
						<?php if ( is_active_sidebar( 'ww-sidebar' ) ) : ?>
							<ul id="ww-sidebar">
								<?php dynamic_sidebar( 'ww-sidebar' ); ?>
								<a href="<?php echo get_post_type_archive_link( 'ww_listing' ); ?>" class="ww-btn-all">All Posts</a>
							</ul>
						<?php endif; ?>
						<hr>								
					</div><!-- .ww-navbox -->
				</div><!-- .col -->

				<div class="col-lg-9 col-md-12 col-sm-12">
		
				<?php
					 
					$new_post = array(
						'post_id' => 'new', // Create a new post
						'field_groups' => array('group_5b71919481c1b'), // Create post field group ID(s)
						'form' => true,
						'post_title' => false,
						'return' => '%post_url%', // Redirect to new post url
						'html_before_fields' => '',
						'html_after_fields' => '',
						'submit_value' => 'Submit Listing',
						'updated_message' => 'Saved!'
					);
					acf_form( $new_post );
				?>
		
				</div><!-- .col -->
				
			</div><!-- .row -->		
		</div><!-- .container -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .ww-container -->

<?php get_footer();
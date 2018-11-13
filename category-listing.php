<?php

/**
 * Archive template for 'ww_listing' CPT
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>
<div class="ww-container">
	<div class="bootstrap-wrapper">
		<div class="container-fluid">
			<div class="row">
				
				<div class="col-lg-3 col-md-12 col-sm-12">
					<header class="entry-header">
						<h1 class="entry-title">Marketplace</h1>
					</header>
				</div><!-- .col -->
				
				<div class="col-lg-3 col-md-12 col-sm-12 offset-lg-6">
					<div class="ww-back-link">
						<a href="<?php echo get_post_type_archive_link( 'ww_listing' ); ?>" class="ww-btn">Back to Marketplace</a>
					</div><!-- .ww-back-link -->
				</div><!-- .col -->
				
			</div><!-- .row -->			
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
						<div class="ww-post-link">
							<a href="<?php echo site_url('/marketplace-form/'); ?>" class="ww-btn">Post A Listing</a>
						</div><!-- .ww-back-link -->								
					</div><!-- .ww-navbox -->
				</div><!-- .col -->
				
				<div class="col-lg-9 col-md-12 col-sm-12">				
					<div class="ww-postlisting">
						<!--<div class="ww-filter-result"></div>-->
						
						<?php  if ( have_posts() ) { while ( have_posts() ) { the_post(); 
							$listing_category = get_field_object('ww_listing_category');
							$listing_category_value = $listing_category['value'];
							$listing_category_label = $listing_category['choices'][ $listing_category_value ];
							
							$buy_or_sell =  get_field_object('ww_buy_or_sell');
							$buy_or_sell_value = $buy_or_sell['value'];
							$buy_or_sell_label = $buy_or_sell['choices'][ $buy_or_sell_value ];
						?>     
						<div class="ww-postblock">
							<?php if ( $buy_or_sell_value == 'buy' ) { ?>
								<div class="ww-postblock-buy">
									<p>Seeking</p>
								</div>
							<?php } else { ?>
								<div class="ww-postblock-sell">							
									<p>For Sale</p>
								</div>
							<?php } ?>
							
							<div class="ww-postblock-title">
								<a href="<?php the_permalink( $post->ID ); ?>"><h3><?php the_field('ww_listing_title'); ?></h3></a>
							</div>
							<div class="ww-postblock-details">
								<?php the_time( get_option( 'date_format' ) ); get_the_time('', $post->ID); ?> | 
								<?php echo $listing_category_label; ?> | 
								<?php if( get_field('ww_location') ) {  the_field('ww_location'); } ?>
							</div>
							<div class="ww-postblock-content">
								<p><?php $content = the_field('ww_listing_description'); echo substr($content, 0, 100); ?></p>
							</div>
							<div class="ww-postblock-button">
								<a href="<?php the_permalink( $post->ID ); ?>" class="ww-btn">View Marketplace Entry</a>
							</div>
						</div><!-- .ww-postblock -->
						<?php } wp_reset_postdata(); } ?>
					</div><!-- .ww-postlisting -->					
				</div><!-- .col -->
				
			</div><!-- .row -->
		</div><!-- .container-fluid -->
	</div><!-- .bootstrap-wrapper -->
</div>
<?php
get_footer();
<?php
	
/**
 * Single template for 'ww_listing' CPT
 */
 
// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) exit;
acf_form_head();
get_header();

$listing_category = get_field_object('ww_listing_category');
$listing_category_value = $listing_category['value'];
$listing_category_label = $listing_category['choices'][ $listing_category_value ];

$buy_or_sell =  get_field_object('ww_buy_or_sell');
$buy_or_sell_value = $buy_or_sell['value'];
$buy_or_sell_label = $buy_or_sell['choices'][ $buy_or_sell_value ];

$grape_sample = get_field_object('ww_grapes_sample');
$grape_sample_value = $grape_sample['value'];
$grape_sample_label = $grape_sample['choices'][ $grape_sample_value ];

$grape_sell = get_field_object('ww_grapes_sell');
$grape_sell_value = $grape_sell['value'];
$grape_sell_label = $grape_sell['choices'][ $grape_sell_value ];

$wine_sample = get_field_object('ww_wine_sample');
$wine_sample_value = $wine_sample['value'];
$wine_sample_label = $wine_sample['choices'][ $wine_sample_value ];

$wine_sell = get_field_object('ww_wine_sell');
$wine_sell_value = $wine_sell['value'];
$wine_sell_label = $wine_sell['choices'][ $wine_sell_value ];

?>


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
						<?php if ( ( is_user_logged_in() && $current_user->ID == $post->post_author )  or ( current_user_can('administrator') ) ) { ?>
							<div class="ww-post-link">
								<a id="edit-post" href="#edit" class="ww-btn">Edit This Post</a>
							</div>
						<?php } ?>
						<hr>
						<div class="ww-post-link">
							<a href="<?php echo site_url('/marketplace-form/'); ?>" class="ww-btn">Post A Listing</a>
						</div><!-- .ww-back-link -->								
					</div><!-- .ww-navbox -->
				</div><!-- .col -->

				<div class="col-lg-9 col-md-12 col-sm-12">
					
					<div class="row">
						<div class="col-sm-9">
							<h1 class="entry-title"><?php the_field('ww_listing_title'); ?></h1>
						</div>
						<?php if( get_field_object('ww_buy_or_sell') ) { ?>
									<?php if ( $buy_or_sell_value == 'buy' ) { ?>
									<div class="col-sm-3">
										<div class="ww-postblock-buy">
											<p>Seeking</p>
										</div>
									</div>
									<?php } else { ?>
									<div class="col-sm-3">
										<div class="ww-postblock-sell">							
											<p>For Sale</p>
										</div>
									</div>
									<?php } ?>
								<?php } ?>
					</div>
					<div class="row">												
						<div class="col">														
							<div class="row">
								
								<?php if( get_field_object('ww_listing_category') ) { ?>
									<div class="col">
										<h4>Category</h4> 
										<p><?php echo $listing_category_label; ?></p>
									</div>
								<?php } ?>
			
								<?php if( ($listing_category_value == 'grapes') or ($listing_category_value == 'wine') or ($listing_category_value == 'equipment') or ($listing_category_value == 'realestate') ) { ?>
										
									<?php if( get_field('ww_listing_price') ) { ?>
										<div class="col">
											<h4>Price</h4> 
											<p><?php the_field('ww_listing_price'); ?></p>
										</div>
									<?php } ?>
									
								<?php } else { ?>
										
									<?php if( get_field('ww_job_compensation') ) { ?>
										<div class="col">
											<h4>Compensation</h4> 
											<p><?php the_field('ww_job_compensation'); ?></p>
										</div>
									<?php } ?>
									
								<?php } ?>
								
								
							</div><!-- .row -->							
							<div class="row">				
								<?php if( get_field('ww_listing_description') ) { ?>
								<div class="col">
									<h4>Description</h4> 
									<p><?php the_field('ww_listing_description'); ?></p>
								</div>
								<?php } ?>
							</div><!-- .row -->																		
						</div><!-- .col -->											
						<div class="col ww-contact-details">						
							<h4>Contact Details</h4> 
							<?php if( get_field('ww_grapes_contact_name') ) { ?>
								<p>Name: <?php the_field('ww_grapes_contact_name'); ?></p>
							<?php } ?>
							
							<?php if( get_field('ww_grapes_contact_phone') ) { ?>
								<p>Phone: <a href="tel:<?php the_field('ww_grapes_contact_phone'); ?>"><?php the_field('ww_grapes_contact_phone'); ?></a></p>
							<?php } ?>
							
							<?php if( get_field('ww_grapes_contact_email') ) { ?>
								<p>Email: <a href="mailto:<?php the_field('ww_grapes_contact_email'); ?>"><?php the_field('ww_grapes_contact_email'); ?></a></p>
							<?php } ?>											
						</div><!-- .col -->															
					</div><!-- .row -->
					
					<div class="row">
						<div class="col">
					
							<?php if ( $listing_category_value == 'grapes' ) { ?>
					
								<div class="ww-grape-details">			
									<h3>Grape Details</h3>						
									<div class="row">
										
									<?php if( get_field('ww_grapes_ava') ) { ?>
									<div class="col">
										<h4>AVA</h4> 
										<p><?php the_field('ww_grapes_ava'); ?></p>
									</div>
									<?php } ?>
									
									<?php if( get_field('ww_grapes_varietal') ) { ?>
									<div class="col">
										<h4>Varietal</h4> 
										<p><?php the_field('ww_grapes_varietal'); ?></p>
									</div>
									<?php } ?>
										
									<?php if( get_field('ww_grapes_tons') ) { ?>
									<div class="col">
										<h4>Tonnage</h4> 
										<p><?php the_field('ww_grapes_tons'); ?></p>
									</div>
									<?php } ?>
									
									<?php if( get_field('ww_grapes_year_planted') ) { ?>
									<div class="col">
										<h4>Year Planted</h4> 
										<p><?php the_field('ww_grapes_year_planted'); ?></p>
									</div>
									<?php } ?>
										
								</div><!-- .row -->			
									<div class="row">
									
									<?php if( get_field('ww_grapes_clone') ) { ?>
									<div class="col">
										<h4>Clone</h4> 
										<p><?php the_field('ww_grapes_clone'); ?></p>
									</div>
									<?php } ?>
										
									<?php if( get_field('ww_grapes_soil_type') ) { ?>
									<div class="col">
										<h4>Soil Type</h4> 
										<p><?php the_field('ww_grapes_soil_type'); ?></p>
									</div>
									<?php } ?>
									
									<?php if( get_field('ww_grapes_spacing') ) { ?>
									<div class="col">
										<h4>Spacing</h4> 
										<p><?php the_field('ww_grapes_spacing'); ?></p>
									</div>
									<?php } ?>	
									
									<?php if( get_field('ww_grapes_topography') ) { ?>
									<div class="col">
										<h4>Topography</h4> 
										<p><?php the_field('ww_grapes_topography'); ?></p>
									</div>
									<?php } ?>
									
								</div><!-- .row -->			
									<div class="row">
									
									<?php if( get_field('ww_grapes_trellis_type') ) { ?>
									<div class="col">
										<h4>Trellis Type</h4> 
										<p><?php the_field('ww_grapes_trellis_type'); ?></p>
									</div>
									<?php } ?>
										
									<?php if( get_field('ww_grapes_vineyard_manager') ) { ?>
									<div class="col">
										<h4>Vineyard Manager</h4> 
										<p><?php the_field('ww_grapes_vineyard_manager'); ?></p>
									</div>
									<?php } ?>
									
									<?php if( get_field('ww_grapes_vineyard_certifications') ) { ?>
									<div class="col">
										<h4>Vineyard Certifications</h4> 
										<p><?php the_field('ww_grapes_vineyard_certifications'); ?></p>
									</div>
									<?php } ?>
									
								</div><!-- .row -->				
									<div class="row">					
									
									<div class="col">
										<h4>Willing to sell to home winemaker?</h4> 
										<p><?php echo $grape_sell_label; ?></p>
									</div>
									
						
									<div class="col">
										<h4>Sample available?</h4> 
										<p><?php echo $grape_sample_label; ?></p>
									</div>	
										
								</div><!-- .row -->			
								</div><!-- .ww-grape-details -->
					
							<?php } elseif ( $listing_category_value == 'wine' ) { ?>
					
								<div class="ww-wine-details">			
									<h3>Wine Details</h3>							
									<div class="row">
											
											<?php if( get_field('ww_wine_facility_name') ) { ?>
											<div class="col">
												<h4>Facility Name</h4> 
												<p><?php the_field('ww_wine_facility_name'); ?></p>
											</div>
											<?php } ?>
						
											<?php if( get_field('ww_wine_location') ) { ?>
											<div class="col">
												<h4>Location</h4> 
												<p><?php the_field('ww_wine_location'); ?></p>
											</div>
											<?php } ?>
											
											<?php if( get_field('ww_wine_varietal') ) { ?>
											<div class="col">
												<h4>Varietal</h4> 
												<p><?php the_field('ww_wine_varietal'); ?></p>
											</div>
											<?php } ?>
										
											<?php if( get_field('ww_wine_vineyard') ) { ?>
											<div class="col">
												<h4>Vineyard</h4> 
												<p><?php the_field('ww_wine_vineyard'); ?></p>
											</div>
											<?php } ?>
											
											<?php if( get_field('ww_wine_gallons') ) { ?>
											<div class="col">
												<h4>Total Gallons Available</h4> 
												<p><?php the_field('ww_wine_gallons'); ?></p>
											</div>
											<?php } ?>
											
									</div><!-- .row -->				
									<div class="row">					
											
										<?php if( get_field('ww_wine_brix') ) { ?>
										<div class="col">
											<h4>Brix</h4> 
											<p><?php the_field('ww_wine_brix'); ?></p>
										</div>
										<?php } ?>
										
										<?php if( get_field('ww_wine_abv') ) { ?>
										<div class="col">
											<h4>ABV</h4> 
											<p><?php the_field('ww_wine_abv'); ?></p>
										</div>
										<?php } ?>
										
										<?php if( get_field('ww_wine_ph') ) { ?>
										<div class="col">
											<h4>Ph</h4> 
											<p><?php the_field('ww_wine_ph'); ?></p>
										</div>
										<?php } ?>
										
										<?php if( get_field('ww_wine_fermentation_vessel') ) { ?>
										<div class="col">
											<h4>Fermentation Vessal</h4> 
											<p><?php the_field('ww_wine_fermentation_vessel'); ?></p>
										</div>
										<?php } ?>					
										
									</div><!-- .row -->							
									<div class="row">
										
										<?php if( get_field('ww_wine_tasting_notes') ) { ?>
										<div class="col">
											<h4>Tasting Notes</h4> 
											<p><?php the_field('ww_wine_tasting_notes'); ?></p>
										</div>
										<?php } ?>					
											
										<?php if( get_field('ww_wine_cellar_manager') ) { ?>
										<div class="col">
											<h4>Cellar Manager</h4> 
											<p><?php the_field('ww_wine_cellar_manager'); ?></p>
										</div>
										<?php } ?>
										
									</div><!-- .row -->					
									<div class="row">											
										<div class="col">
											<h4>Willing to sell to home winemaker?</h4> 
											<p><?php echo $wine_sell_label; ?></p>
										</div>											
										<div class="col">
											<h4>Sample available?</h4> 
											<p><?php echo $wine_sample_label; ?></p>
										</div>										
									</div><!-- .row -->								
								</div><!-- .ww-wine-details -->
						
							<?php } elseif ( $listing_category_value == 'equipment' ) { ?>
					
								<div class="ww-equipment-details">				
									<div class="row">
										<div class="col">
												<div class="row">
													
													<?php if( get_field('ww_equipment_photo_1') ) { ?>									
														<div class="col">
															<h4>Photos</h4>
															<img src="<?php the_field('ww_equipment_photo_1'); ?>" class="ww-single-img" />
														</div>
													<?php } ?>
													
													<?php if( get_field('ww_equipment_photo_2') ) { ?>
														<div class="col">
															<img src="<?php the_field('ww_equipment_photo_2'); ?>" class="ww-single-img" />
														</div>
													<?php } ?>
													
													</div><!-- .row -->
											</div><!-- .col -->								
										<div class="col">
												<div class="row">
													<?php if( get_field('ww_equipment_year') ) { ?>
													<div class="col">
														<h4>Year</h4> 
														<p><?php the_field('ww_equipment_year'); ?></p>
													</div>
													<?php } ?>
								
													<?php if( get_field('ww_equipment_make') ) { ?>
													<div class="col">
														<h4>Make / Model</h4> 
														<p><?php the_field('ww_equipment_make'); ?></p>
													</div>
													<?php } ?>
											
												</div><!-- .row -->
											</div><!-- .col -->									
									</div><!-- .row -->			
								</div><!-- .ww-equipment-details -->
						
							<?php } elseif ( $listing_category_value == 'realestate' ) { ?>
					
								<div class="ww-realestate-details">			
									<div class="row">												
										<?php if( get_field('ww_realestate_photo_1') ) { ?>
											<h4>Photos</h4>
											<div class="col">
												<img src="<?php the_field('ww_realestate_photo_1'); ?>" class="ww-single-img" />
											</div>
										<?php } ?>					
										<?php if( get_field('ww_realestate_photo_2') ) { ?>
											<div class="col">
												<img src="<?php the_field('ww_realestate_photo_2'); ?>" class="ww-single-img" />
											</div>
										<?php } ?>												
									</div><!-- .row -->
								</div><!-- .ww-realestate-details -->
						
							<?php } elseif ( $listing_category_value == 'job' ) { ?>
					
								<div class="ww-job-details">	
									<div class="row">				
								<?php if( get_field('ww_job_requirements') ) { ?>
									<div class="col">
										<h4>Job Requirements</h4> 
										<p><?php the_field('ww_job_requirements'); ?></p>
									</div>
								<?php } ?>				
								<div class="col">
									<div class="row">					
										<?php if( get_field('ww_job_start_date') ) { ?>
										<div class="col">		
											<h4>Start Date</h4> 
											<p><?php the_field('ww_job_start_date'); ?></p>
										</div>
										<?php } ?>							
										<?php if( get_field('ww_job_end_date') ) { ?>
										<div class="col">
											<h4>End Date</h4> 
											<p><?php the_field('ww_job_end_date'); ?></p>
										</div>
										<?php } ?>	
									</div><!-- .row -->
								</div><!-- .col -->				
							</div><!-- .row -->											
								</div><!-- .ww-job-details -->
						
							<?php } else {} ?>
						</div><!-- .col -->
					</div><!-- .row -->
				</div><!-- .col -->
			</div><!-- .row -->		
		</div><!-- .container -->
	</div><!-- .bootstrap-wrapper -->
</div><!-- .ww-container -->

<!-- BEGIN Left Sidr -->
<?php if ( ( is_user_logged_in() && $current_user->ID == $post->post_author ) or ( current_user_can('administrator') ) ) { ?>
	<div id='sidr' class='acf-edit-post'>
    	<a href='#' class='edit-close ww-btn'>Close Editor</a>
            <?php acf_form (array(
                'field_groups' => array('group_5b71919481c1b'), // Same ID(s) used before
                'form' => true,
                'return' => '%post_url%',
                'submit_value' => 'Save Changes',
                'post_title' => false,
                'post_content' => false,
            ));?>
	</div>
<?php } ?>
<!-- END Left Sidr -->

<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery('#edit-post').sidr();
	    jQuery( '.edit-close' ).on( 'click', function() {
	        jQuery.sidr( 'close' , 'sidr' );
	    });
	});
</script>

<?php get_footer();
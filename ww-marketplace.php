<?php
/*
Plugin Name: Well Wined Marketplace
Plugin URI: 
Description: A digital marketplace for excess grapes, bulk juice, winery equipment, and vineyards. Made exclusively for TFWA.
Version: 1.0
Author: JTG
Author URI: https://jonathangatlin.com
License: GPLv2
*/

class WW_Marketplace {
	
	public function __construct() {
			
        add_filter( 'acf/settings/path', array( $this, 'update_acf_settings_path' ) ); // Include ACF
        add_filter( 'acf/settings/dir', array( $this, 'update_acf_settings_dir' ) );
        add_filter('acf/settings/show_admin', '__return_false');

		if( ! class_exists('acf') ) {     
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/acf.php' );
        }
        if( ! function_exists('include_field_types_image_crop') ) {  
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/acf-image-crop/acf-image-crop.php' );
        }
        if( ! class_exists('SearchAndFilter') ) {  
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/search-filter/search-filter.php' );
        }
        if( ! class_exists('Agp_Autoloader') ) {  
        	include_once( plugin_dir_path( __FILE__ ) . 'vendor/agp-ajax-taxonomy-filter/agp-ajax-taxonomy-filter.php' );
        }      
        				
		register_activation_hook( __FILE__, array( $this, 'ww_flush_permalinks' ) ); // Flush Permalinks	
		register_activation_hook(__FILE__, array( $this, 'ww_listings_expire' ) ); // Listing Expiration		
		        
		add_action( 'init', array( $this, 'register_ww_listing' ) ); // Register CPTs	
		add_action( 'init', array( $this, 'create_ww_listing_taxonomies' ) ); // Register Taxonomies	
		add_action( 'init', array( $this, 'ww_listing_options' ) ); // Include ACF Options		
		add_action( 'init', array( $this, 'ww_archive_post_status' ) ); // Archive Status
		add_action( 'init', array( $this, 'ww_sidebar' ) ); // Register Sidebar		
		add_action( 'admin_footer-post.php', array( $this, 'ww_append_post_status_list' ) ); // Post Status Selector
		add_action( 'wp_enqueue_scripts', array( $this, 'ww_marketplace_frontend_styles' ), 99 ); // Include Styles		
					
		add_filter( 'archive_template', array( $this, 'ww_listing_archive_template' ) ); // Assign Archive Page Templates
		add_filter( 'single_template', array( $this, 'ww_listing_single_template' ) );	// Assign Single Page Templates
		add_filter( 'archive_template', array( $this, 'ww_listing_category_template' ) ); // Assign Category Templates
		
		add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'register_project_templates' ) );
		add_filter( 'template_include',  array( $this, 'view_project_template') );
	
		$this->templates = array( 'template-marketplace-form.php' => 'Marketplace Form', );
		
		add_action('acf/pre_save_post', array( $this, 'ww_listing_save_post' ) );	
				
		//register_activation_hook( __FILE__, array( $this, 'ww_add_marketplace_poster_role') ); // Add Marketplace Poster Role	
		//register_deactivation_hook( __FILE__ , array( $this, 'ww_remove_marketplace_poster_role' ) );      
        //add_action( 'admin_init', array( $this, 'ww_marketplace_poster_caps' ) );	//Marketplace Poster Capabilities
								
		// Display Errors
		error_reporting(E_ALL); ini_set('display_errors', 0);
		//define('WP_DEBUG', true);
				
    }    
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    // Register Sidebar
    public function ww_sidebar(){
	    $text_domain = "ww-marketplace";
	    register_sidebar( array(
		    'id'          => 'ww-sidebar',
		    'name'        => __( 'Marketplace Sidebar', $text_domain ),
		    'description' => __( 'This sidebar is located on the Marketplace pages.', $text_domain ),
		) );
    }   
	
	// Include ACF
	public function update_acf_settings_path( $path ) {
	   $path = plugin_dir_path( __FILE__ ) . 'vendor/acf-pro/';
	   return $path;
	}
    
    public function update_acf_settings_dir( $dir ) {
        $dir = plugin_dir_url( __FILE__ ) . 'vendor/acf-pro/';
        return $dir;
    }
    
    // Register Custom Post Type - Listing   
	public function register_ww_listing() {
	
		$labels = array(
			'name'                  => _x( 'Listings', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Listing', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Marketplace', 'text_domain' ),
			'name_admin_bar'        => __( 'Marketplace', 'text_domain' ),
			'archives'              => __( 'Listing Archives', 'text_domain' ),
			'attributes'            => __( 'Listing Attributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Listing:', 'text_domain' ),
			'all_items'             => __( 'All Listings', 'text_domain' ),
			'add_new_item'          => __( 'Add New Listing', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Listing', 'text_domain' ),
			'edit_item'             => __( 'Edit Listing', 'text_domain' ),
			'update_item'           => __( 'Update Listing', 'text_domain' ),
			'view_item'             => __( 'View Listing', 'text_domain' ),
			'view_items'            => __( 'View Listings', 'text_domain' ),
			'search_items'          => __( 'Search Listings', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Listing', 'text_domain' ),
			'items_list'            => __( 'Listings list', 'text_domain' ),
			'items_list_navigation' => __( 'Listings list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter Listings list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'Listing', 'text_domain' ),
			'description'           => __( 'Marketplace Listing', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => false,
			'taxonomies'            => array( 'ww_listing_category' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'map_meta_cap'			=> true,
			'capability_type'       => 'post',
			'menu_icon' 			=> 'dashicons-cart',
			"rewrite" => array( "slug" => "marketplace", "with_front" => true ),
		);
		register_post_type( 'ww_listing', $args );
	
	}
	
	// Register Taxonomies	
	public function create_ww_listing_taxonomies() {
		
		register_taxonomy(  
	        'ww_listing_category',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
	        'ww_listing',        //post type name
	        array(  
	            'hierarchical' => true,  
	            'label' => 'Listing Categories',  //Display name
	            'query_var' => true,
	            'rewrite' => array(
	                'slug' => 'marketplace-category', // This controls the base slug that will display before each term
	                'with_front' => false // Don't display the category base before 
	            )
	        )  
	    );
	    
	    wp_insert_term(
			'Grapes & Fruit', // the term 
			'ww_listing_category', // the taxonomy
			array(
				'description'=> 'Grapes & Fruit',
				'slug' => 'grapes'
			)
		);
		
		wp_insert_term(
			'Bulk Juice & Wine', // the term 
			'ww_listing_category', // the taxonomy
			array(
				'description'=> 'Bulk Juice & Wine',
				'slug' => 'wine'
			)
		);
		
		wp_insert_term(
			'Winery Equipment', // the term 
			'ww_listing_category', // the taxonomy
			array(
				'description'=> 'Winery Equipment',
				'slug' => 'equipment'
			)
		);
		
		wp_insert_term(
			'Real Estate', // the term 
			'ww_listing_category', // the taxonomy
			array(
				'description'=> 'Real Estate',
				'slug' => 'realestate'
			)
		);
		
		wp_insert_term(
			'Job Opportunities', // the term 
			'ww_listing_category', // the taxonomy
			array(
				'description'=> 'Job Opportunities',
				'slug' => 'job'
			)
		);
			    
	}	
		
	// Include ACF Options	
	public function ww_listing_options() {

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5b71919481c1b',
	'title' => 'Listing',
	'fields' => array(
		array(
			'key' => 'field_5bcf5e3566b89',
			'label' => 'Listing Title',
			'name' => 'ww_listing_title',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e2a9339f5',
			'label' => 'Listing Description',
			'name' => 'ww_listing_description',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
		),
		array(
			'key' => 'field_5b7193fe25ee2',
			'label' => 'Contact Name',
			'name' => 'ww_grapes_contact_name',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71940c25ee3',
			'label' => 'Contact Phone',
			'name' => 'ww_grapes_contact_phone',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71941525ee4',
			'label' => 'Contact Email',
			'name' => 'ww_grapes_contact_email',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b92a31c89fec',
			'label' => 'Location',
			'name' => 'ww_location',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e089c6818',
			'label' => 'Listing Category',
			'name' => 'ww_listing_category',
			'type' => 'select',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'grapes' => 'Grapes & Fruit',
				'wine' => 'Bulk Juice & Wine',
				'equipment' => 'Winery Equipment',
				'realestate' => 'Real Estate',
				'job' => 'Job Opportunities',
			),
			'default_value' => array(
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b92a16b4ca05',
			'label' => 'Are you looking to Buy or Sell?',
			'name' => 'ww_buy_or_sell',
			'type' => 'select',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'grapes',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'wine',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'equipment',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'realestate',
					),
				),
			),
			'wrapper' => array(
				'width' => '16',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'buy' => 'Buy',
				'sell' => 'Sell',
			),
			'default_value' => array(
				0 => 'buy',
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b71e6338a587',
			'label' => 'Price',
			'name' => 'ww_listing_price',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'grapes',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'wine',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'equipment',
					),
				),
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'realestate',
					),
				),
			),
			'wrapper' => array(
				'width' => '16',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b75cd97ef17f',
			'label' => 'Compensation',
			'name' => 'ww_job_compensation',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'job',
					),
				),
			),
			'wrapper' => array(
				'width' => '32',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b7193cf25ee0',
			'label' => 'Grape Information',
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'grapes',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array(
			'key' => 'field_5b71928e25ed3',
			'label' => 'AVA',
			'name' => 'ww_grapes_ava',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b7192c225ed4',
			'label' => 'Varietal',
			'name' => 'ww_grapes_varietal',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b7192e825ed5',
			'label' => 'Tons',
			'name' => 'ww_grapes_tons',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b7192ff25ed6',
			'label' => 'Year Planted',
			'name' => 'ww_grapes_year_planted',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71931a25ed7',
			'label' => 'Clone',
			'name' => 'ww_grapes_clone',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71931f25ed8',
			'label' => 'Soil Type',
			'name' => 'ww_grapes_soil_type',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71932725ed9',
			'label' => 'Spacing',
			'name' => 'ww_grapes_spacing',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71933025eda',
			'label' => 'Topography',
			'name' => 'ww_grapes_topography',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71933925edb',
			'label' => 'Trellis Type',
			'name' => 'ww_grapes_trellis_type',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71934625edc',
			'label' => 'Vineyard Manager',
			'name' => 'ww_grapes_vineyard_manager',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b7193b125edf',
			'label' => 'Vineyard Certification(s)',
			'name' => 'ww_grapes_vineyard_certifications',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71936925edd',
			'label' => 'Sample Available',
			'name' => 'ww_grapes_sample',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => array(
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b71937425ede',
			'label' => 'Willing to sell to home winemakers',
			'name' => 'ww_grapes_sell',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => array(
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b71e58b8a586',
			'label' => 'Bulk Wine & Juice',
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'wine',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array(
			'key' => 'field_5b71e6718a588',
			'label' => 'Facility Name',
			'name' => 'ww_wine_facility_name',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e6958a589',
			'label' => 'Location',
			'name' => 'ww_wine_location',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e6c18a58b',
			'label' => 'Varietal',
			'name' => 'ww_wine_varietal',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e6f08a58c',
			'label' => 'Vineyard of Origin',
			'name' => 'ww_wine_vineyard',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e70d8a58d',
			'label' => 'Total Available Gallons',
			'name' => 'ww_wine_gallons',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e71b8a58e',
			'label' => 'Brix',
			'name' => 'ww_wine_brix',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e73e8a58f',
			'label' => 'ABV',
			'name' => 'ww_wine_abv',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e74a8a590',
			'label' => 'Ph',
			'name' => 'ww_wine_ph',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e75c8a591',
			'label' => 'Fermentation Vessel',
			'name' => 'ww_wine_fermentation_vessel',
			'type' => 'text',
			'instructions' => 'Steel, Oak (species), Concrete, Plastic',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e79a8a592',
			'label' => 'Tasting Notes',
			'name' => 'ww_wine_tasting_notes',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '66',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
		),
		array(
			'key' => 'field_5b71e7ad8a593',
			'label' => 'Cellar Manager',
			'name' => 'ww_wine_cellar_manager',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b71e7b78a594',
			'label' => 'Willing to sell to home winemakers?',
			'name' => 'ww_wine_sell',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => array(
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b7226063c85f',
			'label' => 'Sample Available?',
			'name' => 'ww_wine_sample',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => array(
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array(
			'key' => 'field_5b75800e71cad',
			'label' => 'Winery Equipment & Services',
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'equipment',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array(
			'key' => 'field_5b75cc41ef172',
			'label' => 'Year',
			'name' => 'ww_equipment_year',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b75cc56ef173',
			'label' => 'Make/Model',
			'name' => 'ww_equipment_make',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5b75802b71cae',
			'label' => 'Photo 1',
			'name' => 'ww_equipment_photo_1',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5b75808571caf',
			'label' => 'Photo 2',
			'name' => 'ww_equipment_photo_2',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5b75cc8eef175',
			'label' => 'Real Estate',
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'realestate',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array(
			'key' => 'field_5b75cc74ef174',
			'label' => 'Photo 1',
			'name' => 'ww_realestate_photo_1',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5b75ccd1ef176',
			'label' => 'Photo 2',
			'name' => 'ww_realestate_photo_2',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'url',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5b75ccebef177',
			'label' => 'Job Opportunities',
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5b71e089c6818',
						'operator' => '==',
						'value' => 'job',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array(
			'key' => 'field_5b75cd21ef179',
			'label' => 'Start Date',
			'name' => 'ww_job_start_date',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'F j, Y',
			'return_format' => 'F j, Y',
			'first_day' => 1,
		),
		array(
			'key' => 'field_5b75cd42ef17a',
			'label' => 'End Date',
			'name' => 'ww_job_end_date',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'F j, Y',
			'return_format' => 'F j, Y',
			'first_day' => 1,
		),
		array(
			'key' => 'field_5b75cd08ef178',
			'label' => 'Requirements',
			'name' => 'ww_job_requirements',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'maxlength' => '',
			'rows' => '',
			'new_lines' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'ww_listing',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'permalink',
		1 => 'the_content',
		2 => 'excerpt',
		3 => 'custom_fields',
		4 => 'discussion',
		5 => 'comments',
		6 => 'revisions',
		7 => 'slug',
		8 => 'author',
		9 => 'format',
		10 => 'page_attributes',
		11 => 'featured_image',
		12 => 'categories',
		13 => 'tags',
		14 => 'send-trackbacks',
	),
	'active' => 1,
	'description' => '',
));

endif;
	}

	// Assign Archive Page Templates
	public function ww_listing_archive_template( $archive_template ) {
		global $post;
		if ( is_post_type_archive ( 'ww_listing' ) ) {
			$archive_template = dirname( __FILE__ ) . '/archive-listing.php';
		}
		return $archive_template;
	}
		
	// Assign Single Page Templates
	public function ww_listing_single_template( $single_template ) {
		global $post;
		if ( $post->post_type == 'ww_listing' ) {
			$single_template = dirname( __FILE__ ) . '/single-listing.php';
		}
		return $single_template;
	}
	
	// Assign Category Page Templates
	public function ww_listing_category_template( $category_template ) {
		global $post;
		if ( is_tax ( 'ww_listing_category' ) ) {
			$category_template = dirname( __FILE__ ) . '/category-listing.php';
		}
		return $category_template;
	}
	
	// Add Creative Brief Template
	
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}
	 
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}

		// Return template
		return $template;

	}
			
	// Include Styles
	public function ww_marketplace_frontend_styles() {
		if ( get_post_type() == 'ww_listing'
			or is_post_type_archive('ww_listing')
			or is_tax('ww_listing_category')
			or is_page('marketplace-form') ) {			
			$pluginpath = plugin_dir_url( __FILE__ );
			wp_enqueue_script( 'jquery' ); 
			wp_enqueue_script( 'sidr_js', 'https://cdn.jsdelivr.net/jquery.sidr/2.2.1/jquery.sidr.min.js', array(), '2.2.1', true );
			wp_enqueue_style( 'style-grid', $pluginpath . 'css/style-grid.css', 997 );
			wp_enqueue_style( 'style', $pluginpath . 'css/style-2.css', 998 );
		}
	}
	
	// New User Role
	public function ww_add_marketplace_poster_role() {
		add_role(
			'ww_marketplace_poster',
			'Marketplace Poster',
			array(
				'read' => true,
				'upload_files' => true,
				'edit_ww_listing' => true,
				'edit_ww_listings' => true,
				'publish_ww_listing' => true,
				'delete_ww_listing' => true,
			)
		);
	}
	
	// Add User Caps
	public function ww_marketplace_poster_caps() {
		$role = get_role( 'ww_marketplace_poster', true );
		$role->add_cap( 'read_ww_listing');
		$role->add_cap( 'edit_ww_listing' ); 
	    $role->add_cap( 'edit_ww_listings');
	    $role->add_cap( 'edit_published_ww_listing' ); 
	    $role->add_cap( 'publish_ww_listing' ); 
	    $role->add_cap( 'read_ww_listing' ); 
	    $role->add_cap( 'delete_ww_listing' );
	    
	    $role2 = get_role( 'administrator', true );
        $role2->add_cap( 'read_ww_listing');
        $role2->add_cap( 'read_private_ww_listing' );
        $role2->add_cap( 'edit_ww_listing' );
        $role2->add_cap( 'edit_ww_listings'); 
        $role2->add_cap( 'edit_others_ww_listing' );
        $role2->add_cap( 'edit_published_ww_listing' );
        $role2->add_cap( 'publish_ww_listing' );
        $role2->add_cap( 'delete_others_ww_listing' );
        $role2->add_cap( 'delete_private_ww_listing' );
        $role2->add_cap( 'delete_published_ww_listing' );  
	}
	
	// Remove User Role
	public function ww_remove_marketplace_poster_role() {
		remove_role( 'ww_marketplace_poster' );
	}
	
	// Flush Permalinks	
	public function ww_flush_permalinks() {
		flush_rewrite_rules();
	}
	
	public function ww_listing_save_post( $post_id ) {
 
		if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {	return; }
		
		if( $post_id != 'new' ) { return $post_id; }
		
		$new_post_title = $_POST['acf']['field_5bcf5e3566b89'];
		
		// Create a new post
		$post = array(
			'post_type' 	=> 'ww_listing',
			'post_status' 	=> 'publish',
			'post_title'    => wp_strip_all_tags($new_post_title),
		);
		
		$post_id = wp_insert_post( $post );
		
		do_action( 'acf/save_post' , $post_id );
		
		return $post_id;
				 
	}
	
	// Add 'Archived' Post Status
	public function ww_archive_post_status(){
	    register_post_status('archived', array(
	        'label'                     => _x( 'Archived', 'ww_listing' ),
	        'public'                    => false,
	        'exclude_from_search'       => false,
	        'show_in_admin_all_list'    => true,
	        'show_in_admin_status_list' => true,
	        'label_count'               => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>' ),
	    ) );
	}

	// Using jQuery to add it to post status dropdown
	public function ww_append_post_status_list(){
		global $post;
		$complete = '';
		$label = '';
		
		if($post->post_type == 'ww_listing'){
			if($post->post_status == 'archived'){
				$complete = ' selected="selected"';
				$label = '<span id="post-status-display"> Archived</span>';
			}
			echo ' <script>
					jQuery(document).ready(function($){
						$("select#post_status").append("<option value=\"archived\" '.$complete.'>Archived</option>");
						$(".misc-pub-section label").append("'.$label.'");
					});
				</script> ';
		}
	}
	
	// Automagically Expire Listings
	public function ww_listings_expire() {
		global $wpdb;
		$daystogo = "30";

		$sql =
		"UPDATE {$wpdb->post}
		SET post_status = 'archive'
		WHERE (post_type = 'ww_listing' AND post_status = 'publish')
		AND DATEDIFF(NOW(), post_date) > %d";

		$wpdb->query( $wpdb->prepare( $sql, $daystogo ) );
	}
			
}

new WW_Marketplace();
<?php
    /*
        Plugin Name: ChUI
        Description: This plugin returns native looking themes depending on the mobile device that accesses your website.
        Version: 1.0
        Author: Craig Presti
        Author URI: http://github.com/craigomatic/
        License: GPL2
     */
	require_once("chui.devicetypes.class.php");
    require_once("chui.blogpost.class.php");
	require_once("chui.viewmodel.class.php");
	require_once("chui.menuorder.class.php");
	
    function evaluate_device() {    
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $device = DeviceType::Other;
    
        if (strpos(strtolower($userAgent),'android') !== false) {
            $device = DeviceType::Android;
        }
        else if(strpos(strtolower($userAgent),'iphone') !== false) {
            $device = DeviceType::iOS;
        }
        else if(strpos(strtolower($userAgent),'ipad') !== false) {
            $device = DeviceType::iOS;
        }
        else if(strpos(strtolower($userAgent),'windows phone') !== false) {
            $device = DeviceType::WindowsPhone;
        }
    
        return $device;
    }

    if (!is_feed() && !is_admin()){
	    $device = evaluate_device();
	}    
    
    function chui_load_template($template) {  
		
		if ( isset( $wp_query->query_vars['json'] ))
			return;
	
        $template = plugin_dir_path( __FILE__ )."templates/index.php";
        return $template;
    }   
    
    function chui_redirect_template() {    
		if ( isset( $wp_query->query_vars['json'] ))
			return;
			
        $template = plugin_dir_path( __FILE__ )."templates/index.php";
        return $template;
    }   
    
    function chui_get_header() {
            
    }
    
   function chui_write_menu_items($page, $key, $view_model) {
        $pageId = str_replace(" ", "", $page->post_title);
		
		if($page->ID != $view_model->FrontPageId) {
			echo "<tablecell href='#".$pageId."'>
					<celltitle>".$page->post_title."</celltitle>
				</tablecell>";
		}
	}
							
	function chui_write_views($page, $key, $view_model) {
        $pageId = str_replace(" ", "", $page->post_title);
					
		//TODO: process the children
		//$children = get_page_children($page->ID);		
		//echo '<pre>Children: ' . print_r( $children, true ) . '</pre>';
		$page_for_posts = intval(get_option( 'page_for_posts' ));
		
		$nav_status = 'upcoming';
		
		if(strtolower($page->ID) == strtolower($view_model->RequestedPage))
		{
			$nav_status = 'current';
		}
				
		if($page->ID == $page_for_posts) {
			echo "<view id='".$pageId."' ui-navigation-status='".$nav_status."' ui-background-style='vertical-striped'>
				<navbar>
					<uibutton ui-implements='back' ui-bar-align='left'>
						<label>Back</label>
					</uibutton>
					<h1>".$page->post_title."</h1>					
				</navbar>
				<subview ui-associations='withNavBar'>";
						
						apply_filters('the_content', $page->post_content);
						
						echo "<scrollpanel>
								<tableview ui-tablecell-order='stacked' ui-kind='grouped'>";
						
						foreach($view_model->BlogPosts as $key => $value)
						{							
							$slugPath = str_replace(site_url(), '', $value->Slug);
							
							echo "<tablecell ui-implements='detail-disclosure' href='#blog-detail' class='blog-post-menu' data-blog-path='".$slugPath."'>
									<celltitle class='productTitle'>".$value->Title."</celltitle>
									<cellsubtitle>".$value->Excerpt."</cellsubtitle>
								</tablecell>";
						}						
						
						echo "</tableview>
							</scrollpanel>";
			echo "</subview>
			</view>";
		}
		else {
			echo "<view id='".$pageId."' ui-navigation-status='".$nav_status."' ui-background-style='vertical-striped'>
				<navbar>
					<uibutton ui-implements='back' ui-bar-align='left'>
						<label>Back</label>
					</uibutton>
					<h1>".$page->post_title."</h1>					
				</navbar>
				<subview ui-associations='withNavBar'>
					<div ui-kind='grouped'>
						"
					.apply_filters('the_content', $page->post_content).
						"
					</div>
				</subview>
			</view>";
		}
	}
	
	function chui_enqueue_scripts() {
		wp_enqueue_script(
			'director',
			plugins_url('templates/js/director.min.js', __FILE__ )
		);
	}
	
    if($device != DeviceType::Other) {
        
        //remove the actions cluttering up the head
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'feed_links', 2 );
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'index_rel_link' );
        remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
        remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'locale_stylesheet' );
        remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
        
        add_action('template_redirect', 'chui_redirect_template');
        add_action('get_header', 'chui_get_header');
        add_filter('template_include', 'chui_load_template');
		add_action( 'wp_enqueue_scripts', 'chui_enqueue_scripts' );
    }      

	//add JSON formatter for the blog posts
	function chui_endpoints_add_endpoint() {
			// register a "json" endpoint to be applied to posts and pages
			add_rewrite_endpoint( 'json', EP_PERMALINK | EP_PAGES );
	}
	add_action( 'init', 'chui_endpoints_add_endpoint' );
	 
	function chui_endpoints_template_redirect() {
			global $wp_query;
	 
			// if this is not a request for json or it's not a singular object then bail
			if ( ! isset( $wp_query->query_vars['json'] ) || ! is_singular() )
					return;
	 
			// output some JSON (normally you might include a template file here)
			chui_endpoints_do_json();
			exit;
	}
	add_action( 'template_redirect', 'chui_endpoints_template_redirect' );
	 
	function chui_endpoints_do_json() {
			header( 'Content-Type: application/json' );
	 
			$post = get_queried_object();
			echo json_encode( $post );
	}
	 
	function chui_endpoints_activate() {
			// ensure our endpoint is added before flushing rewrite rules
			chui_endpoints_add_endpoint();
			// flush rewrite rules - only do this on activation as anything more frequent is bad!
			flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'chui_endpoints_activate' );
	 
	function chui_endpoints_deactivate() {
			// flush rules on deactivate as well so they're not left hanging around uselessly
			flush_rewrite_rules();
	}
	register_deactivation_hook( __FILE__, 'chui_endpoints_deactivate' );    	
?>
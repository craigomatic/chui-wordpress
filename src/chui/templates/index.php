<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<title><?php wp_title( '|', true, 'right' );?></title>
	
    <?php
		wp_head();
	
        switch($device)
        {
            case DeviceType::Android:
            {
                echo "<link rel=\"stylesheet\" href=\"".plugin_dir_url(__FILE__)."css/chui.android.min.css\">\n";
                echo "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/iscroll.min.js\"></script>\n";
	            echo "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/chocolatechip.js\"></script>\n";
                echo "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/chui.android.js\"></script>\n";
                
                break;
            }            
            case DeviceType::iOS:
            {
                echo "<link rel='stylesheet' href='".plugin_dir_url(__FILE__)."css/chui.ios.min.css'>";
                echo "<script type='text/javascript' src='".plugin_dir_url(__FILE__)."js/iscroll.min.js'></script>";
                echo "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/chocolatechip.js\"></script>\n";
                echo "<script type='text/javascript' src='".plugin_dir_url(__FILE__)."js/chui.ios.js'></script>";
                
                break;
            }
            case DeviceType::WindowsPhone:
            {
	            echo "<link rel='stylesheet' href='".plugin_dir_url(__FILE__)."css/chui.wp.min.css'>";
                echo "<script type=\"text/javascript\" src=\"".plugin_dir_url(__FILE__)."js/chocolatechip.js\"></script>\n";
                echo "<script type='text/javascript' src='".plugin_dir_url(__FILE__)."js/chui.wp.js'></script>";
                
                break;
            }
        }	
		
		$view_model = new ViewModel();
		$view_model->IsFrontPage = is_front_page();
		$view_model->FrontPageId = $post->ID;
		
		$front_page_content = apply_filters('the_content', $post->post_content);
		
		if(! $view_model->IsFrontPage) {
			$main_view_navigation_status = "traversed";
			
			if ($post->post_parent != 0) {
				$view_model->RequestedPage = $post->post_parent;
			}
			else {
				$view_model->RequestedPage = $post->ID;
			}
		}
		else {
			$main_view_navigation_status = "current";
		}
		
		$query = new WP_Query( 'post_type=post' );
										
		//grab the posts
		while ( $query->have_posts() ) :
			$query->the_post();
			
			$blog_post = new BlogPost();
			$blog_post->Title = get_the_title();
			$blog_post->Id = get_the_ID();
			$blog_post->Slug = get_permalink($blog_post->Id);
			//$blog_post->Content = get_the_content();
											
			if(has_excerpt($blog_post->Id)) {
				$blog_post->Excerpt = get_the_excerpt();
			}
			
			array_push($view_model->BlogPosts, $blog_post);
		endwhile;
		
		$args = array(
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		); 
		
		$view_model->Pages = get_pages($args);
	?>		
	
	<style>
		[ui-kind='grouped'] p {
		 margin: 0 0 10px 0;
		}
		blockquote {
		 margin: 10px;
		 font-style: italic;
		}
		blockquote > p, p:last-child {
		 margin: 0;
		}
		ul {
		 list-style-type: disc;
		 margin: 10px 0 10px 20px;
		}
		ol {
		 list-style-type: decimal;
		 margin: 10px 0 10px 25px;
		}
		h2, h3, h4, h5 {
		 margin: 10px 0;
		 font-weight: bold;
		}
		
		img {
			max-width: 100%;
			height: auto;
		}
	</style>
	
	<script type="text/javascript">
		$(function() {
			$.app.on($.eventStart, 'tablecell', function(cell) {
				 var href;
				 
				 if (cell.hasAttr("href")) {
					href = cell.attr("href");
					window.location = window.location.pathname + "#/" + href.split('#')[1];
				 }
			});
				
			$.app.on($.eventStart, 'navbar > uibutton[ui-implements=back]', function() {
				 window.location = window.location.pathname +  "#/" + $.UINavigationHistory[$.UINavigationHistory.length - 1].split('#')[1]; 
			});
		});
	</script>
</head>
<body>	
    <app ui-background-style="striped">
	    <view id="main" ui-navigation-status="<?php echo $main_view_navigation_status; ?>">
		    <navbar>
			    <h1><?php wp_title( '|', true, 'right' );?></h1>
		    </navbar>
		    <subview ui-associations="withNavBar">
			    <scrollpanel>                    
					<?php $view_model->render_menu( $front_page_content ); ?>
			    </scrollpanel>
		    </subview>				
	    </view>
		
		<view id="blog-detail" ui-navigation-status="upcoming">
			<navbar>
				<uibutton ui-implements='back' ui-bar-align='left'>
					<label>Back</label>
				</uibutton>
				<h1>Detail View</h1>					
			</navbar>
			<subview id='blog-detail-subview' ui-associations='withNavBar'>
				<div id='blog-detail-contents' ui-kind='grouped' style="min-height: 60px;">
					
				</div>
			</subview>
		</view>
		
		<?php		
			array_walk($view_model->Pages, "chui_write_views", $view_model);
		?>
    </app>
	<?php
		wp_footer();
	?>
	
	<script  type='text/javascript'>
		$(function() {
			$('#Blog tableview').on($.userAction, 'tablecell', function (item) {
				var href = location.href.split('#')[0];
				var path = href + item.attr('data-blog-path') + 'json';
				var content = $('#blog-detail-subview');
				
				$('#blog-detail h1').empty();
				$('#blog-detail-contents').empty();
				
				$('#blog-detail-contents').UIActivityIndicator({modal:true, modalMessage:'Loading...'});
				
				$.xhr({
				   url : path,
				   async: true,
				   success : function(data) {
					  var blogPost = JSON.parse(data);
					  
					  $('#blog-detail h1').html(blogPost.post_title);
					  $('#blog-detail-contents').html(blogPost.post_content);
				   },
				   error: function(data) {
					  $('#blog-detail h1').html("Error");
					  $('#blog-detail-contents').html("Unable to retrieve blog post at this time");
					  
					  if (data.status === -1100) {
						 $('#blog-detail-contents').html("Blog post not found");
					  }
				   }
				});				
			});			
			
			<?php							
				array_walk($view_model->Pages, "chui_write_route_functions", $view_model);				
				
				function chui_write_route_functions($value, $key, $view_model) {
					$pageId = str_replace(" ", "", $value->post_title);
					$viewId = $pageId;
					
					if($value->ID == $view_model->FrontPageId) {
						$viewId = "main";
					}
					
					echo "var navigate".$pageId." = function () { 				
							console.log('".$pageId." routed to');
							$.UINavigateToView('#".$viewId."');
						};\r\n";
				}
				
			?>
			
			var routes = {
			
			<?php			
				array_walk($view_model->Pages, "chui_write_routes");
			
				function chui_write_routes($value) {
					$pageId = str_replace(" ", "", $value->post_title);					
					
					echo "'/".$pageId."' : navigate".$pageId . ",\r\n";					
				}
			?>

			};
			
			var router = Router(routes);
			router.init();
		});				
	</script>
</body>
</html>
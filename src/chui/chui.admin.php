<?php
	require_once("chui.menuorder.class.php");

	function chui_default_display_options() {
		$defaults = array(
			'menu_order' => MenuOrder::ContentThenMenu,
			'enable_ios' => true,
			'enable_android' => true,
			'enable_windowsphone' => true
		);
		
		return apply_filters( 'chui_default_display_options', $defaults );
	}	
	
	add_action('admin_menu', 'plugin_admin_add_page');
	
	function plugin_admin_add_page() {
		add_options_page('ChUI Settings', 'ChUI', 'manage_options', 'chui', 'chui_admin_options_page');
	}
	
	add_action('admin_init', 'chui_init_display_options'); 
	
	function chui_init_display_options() {  
	  
		if(get_option( 'chui_display_options') == false) {
			add_option( 'chui_display_options', apply_filters( 'chui_display_options', chui_default_display_options() ) );
		}
			   
		add_settings_section(  
			'chui_display_settings_section',         // ID used to identify this section and with which to register options  
			'Display Options',                  // Title to be displayed on the administration page  
			'chui_display_options_callback', // Callback used to render the description of the section  
			'chui'                           // Page on which to add this section of options  
		);
		
		add_settings_field(   
			'menu_order', 													//id
			'Menu Order', 													//title
			'chui_menu_order_callback', 									//callback that fills the field out
			'chui', 														//page
			'chui_display_settings_section', 								//section
			array(
				'Determines the display order of the home page content.'    //args
			)  
		);
		
		add_settings_field(   
			'enable_ios',
			'Enable iOS Theme',
			'chui_toggle_ios_callback',
			'chui',
			'chui_display_settings_section',
			array(
				'Activate this setting to render the theme on iOS devices.'  
			)  
		);

		add_settings_field(   
			'enable_android',
			'Enable Android Theme',
			'chui_toggle_android_callback',
			'chui',
			'chui_display_settings_section',
			array(
				'Activate this setting to render the theme on Android devices.'  
			)  
		);	

		add_settings_field(   
			'enable_windowsphone',
			'Enable Windows Phone Theme',
			'chui_toggle_windowsphone_callback',
			'chui',
			'chui_display_settings_section',
			array(
				'Activate this setting to render the theme on Windows Phone devices.'  
			)  
		);	

		register_setting( 'chui_display_options', 'chui_display_options');
	} 
	
	function chui_display_options_callback() {  
		echo '<p>Select which devices ChUI should be activated on.</p>';  
	}
	
	function chui_menu_order_callback($args) {
		
		$html =  '<select id="menu_order" name="chui_display_options[menu_order]">
				   <option value="0" ' . selected( get_option('chui_display_options')['menu_order'], MenuOrder::ContentThenMenu, false ) . '>Content First</option>
				   <option value="1" ' . selected( get_option('chui_display_options')['menu_order'], MenuOrder::MenuThenContent, false ) . '>Menu First</option>
				 </select>';
		  
		$html .= '<label for="menu_order"> '  . $args[0] . '</label>';
		  
		echo $html;
	}
	
	function chui_toggle_ios_callback($args) {  
		  
		// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field  
		$html = '<input type="checkbox" id="enable_ios" name="chui_display_options[enable_ios]" value="1" ' . checked(1, get_option('chui_display_options')['enable_ios'], false) . '/>';   
		  
		// Here, we will take the first argument of the array and add it to a label next to the checkbox  
		$html .= '<label for="enable_ios"> '  . $args[0] . '</label>';   
		  
		echo $html;
	}
	
	function chui_toggle_android_callback($args) {  
		  
		// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field  
		$html = '<input type="checkbox" id="enable_android" name="chui_display_options[enable_android]" value="1" ' . checked(1, get_option('chui_display_options')['enable_android'], false) . '/>';   
		  
		// Here, we will take the first argument of the array and add it to a label next to the checkbox  
		$html .= '<label for="enable_android"> '  . $args[0] . '</label>';   
		  
		echo $html;
	}
	
	function chui_toggle_windowsphone_callback($args) {  
		  
		// Note the ID and the name attribute of the element match that of the ID in the call to add_settings_field  
		$html = '<input type="checkbox" id="enable_windowsphone" name="chui_display_options[enable_windowsphone]" value="1" ' . checked(1, get_option('chui_display_options')['enable_windowsphone'], false) . '/>';   
		  
		// Here, we will take the first argument of the array and add it to a label next to the checkbox  
		$html .= '<label for="enable_windowsphone"> '  . $args[0] . '</label>';   
		  
		echo $html;
	}
	
	function chui_admin_options_page() {
		
		echo "<div class='wrap'>
				<div id='icon-themes' class='icon32'></div>		
				<h2>ChUI for Wordpress</h2>";
				
				settings_errors();
				
		echo 	"The following settings can be used to customise the end-user experience.
				<form method='post' action='options.php'>";
					
				settings_fields('chui_display_options');
				do_settings_sections('chui');
				submit_button();
					
		echo 	"</form>
			</div>";
	}
?>
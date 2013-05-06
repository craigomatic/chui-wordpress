<?php

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<title><?php wp_title( '|', true, 'right' );?></title>
	
    
    <?php
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
    ?>
    
    <?php wp_head(); ?>
</head>
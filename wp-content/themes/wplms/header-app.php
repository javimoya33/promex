<?php
//Header File
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<?php
wp_head();
?>
</head>
<body <?php body_class(); ?>>
<div id="global" class="global">
    <div class="pagesidebar">
        <div class="sidebarcontent">    
            <h2 id="sidelogo">
                <a href="<?php echo vibe_site_url('','sidelogo'); ?>"><img src="<?php  echo apply_filters('wplms_logo_url',VIBE_URL.'/assets/images/logo.png','pagesidebar'); ?>" alt="<?php echo get_bloginfo('name'); ?>" /></a>
            </h2>
            <?php
                $args = apply_filters('wplms-mobile-menu',array(
                    'theme_location'  => 'mobile-menu',
                    'container'       => '',
                    'container'       => '',
                    'items_wrap' => '<div class="mobile_icons"><a id="mobile_searchicon"><i class="fa fa-search"></i></a>'.( (function_exists('WC')) ?'<a href="'.WC()->cart->get_cart_url().'"><span class="fa fa-shopping-basket"><em>'.WC()->cart->cart_contents_count.'</em></span></a>':'').'</div><ul id="%1$s" class="%2$s">%3$s</ul>',
                    'menu_class'      => 'sidemenu',
                    'fallback_cb'     => 'vibe_set_menu',
                ));

                wp_nav_menu( $args );
            ?>
        </div>
        <a class="sidebarclose"><span></span></a>
    </div>  
    <div class="login_sidebar">
        <div class="login_content">
        <?php
            vibe_include_template("login/default_login.php");
         ?>
        </div>
    </div>
    <div class="pusher">
        <?php
            $fix=vibe_get_option('header_fix');
        ?>
        <header class="mooc app <?php if(isset($fix) && $fix){echo 'fix';} ?>">
            <?php
                if(is_user_logged_in()){
                    ?>
                    <a id="login_trigger"><?php bp_loggedin_user_avatar( 'type=full' ); ?></a>
                    <?php
                }else{
                    ?>
                    <a id="login_trigger"><span><i class="fa fa-user"></i></span></a>
                    <?php
                }
            ?>
            <div class="<?php echo vibe_get_container(); ?>">
                
                <div class="col-md-5 col-sm-5">    
                    <div id="mooc_menu"> 
                          <?php
                            $args = apply_filters('wplms-main-menu',array(
                                 'theme_location'  => 'main-menu',
                                 'container'       => 'nav',
                                 'menu_class'      => 'menu',
                                 'walker'          => new vibe_walker,
                                 'fallback_cb'     => 'vibe_set_menu'
                             ));
                            wp_nav_menu( $args ); 
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2">    
                   
                        <?php

                            if(is_front_page()){
                                echo '<h1 id="logo">';
                            }else{
                                echo '<h2 id="logo">';
                            }
                        ?>
                        
                            <a href="<?php echo vibe_site_url('','logo'); ?>"><img src="<?php  echo apply_filters('wplms_logo_url',VIBE_URL.'/assets/images/logo.png','header'); ?>" alt="<?php echo get_bloginfo('name'); ?>" /></a>
                        <?php
                            if(is_front_page()){
                                echo '</h1>';
                            }else{
                                echo '</h2>';
                            }
                            
                        ?>
                </div>
                <div class="col-md-5 col-sm-5">
                    <ul class="topmenu">
                        <li><a id="new_searchicon"><i class="fa fa-search"></i></a></li>
                        <?php
                            if ( function_exists('bp_loggedin_user_link') && is_user_logged_in() ) :
                                
                                    if(function_exists('bp_get_total_unread_messages_count')){
                                ?>

                                    <li><a href="<?php echo bp_loggedin_user_domain().'messages'; ?>"><i class="fa fa-envelope-o"></i><?php $n=bp_get_total_unread_messages_count(); echo (($n)?'<span>'.$n.'</span>':''); ?></a></li>
                                <?php
                                    }
                                    
                                    if(function_exists('bp_notifications_get_unread_notification_count')){
                                
                                ?>    
                                    <li><a href="<?php echo bp_loggedin_user_domain().'notifications'; ?>"><i class="fa fa-bell-o"></i><?php $n=bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ); echo (($n)?'<span>'.$n.'</span>':''); ?></a></li>
                                <?php
                                    }
                            endif;    
                                ?>    
                                    <?php
                                    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active') && is_plugin_active( 'woocommerce/woocommerce.php'))) { global $woocommerce;
                                    ?>
                                    <li><a class="smallimg vbpcart"><span class="fa fa-shopping-basket"><?php echo (($woocommerce->cart->cart_contents_count)?'<em>'.$woocommerce->cart->cart_contents_count.'</em>':''); ?></span></a>
                                    <div class="woocart"><?php woocommerce_mini_cart(); ?></div>
                                    </li>
                                    <?php
                                    }
                                    ?>
                                    <?php do_action('wplms_header_top_login'); ?>
                                </ul>
                            <?php
                        ?>
                    </div>

                        <?php
                            $style = vibe_get_login_style();
                            if(empty($style)){
                                $style='default_login';
                            }
                        ?>
                    <div id="vibe_bp_login" class="<?php echo $style; ?>">
                    <?php
                        include_once("templates/login/$style.php");
                     ?>
                   </div>
                <a id="trigger">
                    <span class="lines"></span>
                </a>

            </div>
        </header>

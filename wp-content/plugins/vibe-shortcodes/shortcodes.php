<?php

if ( ! defined( 'ABSPATH' ) ) exit;

 
Class Vibe_Define_Shortcodes{


    function __construct(){

        $this->rand = rand(0,99);
        add_shortcode('d', array($this,'vibe_dropcaps'));
        add_shortcode('pullquote', array($this, 'vibe_pullquote'));
        add_shortcode('sell_content',  array($this,'vibe_sell_content'));
        add_shortcode('social_buttons',  array($this,'vibe_social_buttons'));
        add_shortcode('social_icons', array($this, 'vibe_social_sharing_buttons'));
        add_shortcode('number_counter',  array($this,'vibe_number_counter'));
        add_shortcode('vibe_container',  array($this,'vibe_container'));
        add_shortcode('img',  array($this,'vibe_img'));
        add_shortcode('allbadges',  array($this,'vibe_allbages'));
        add_shortcode('instructor',  array($this,'vibe_instructor'));
        add_shortcode('divider',  array($this,'vibe_divider'));
        add_shortcode('course',  array($this,'vibe_course'));
        add_shortcode('course_category', array($this,'vibe_course_category'));
        add_shortcode('course_button',  array($this,'vibe_course_button'));
        add_shortcode('icon',  array($this,'vibe_icon'));
        add_shortcode('iframevideo',  array($this,'vibe_iframevideo'));
        add_shortcode('iframe',  array($this,'vibe_iframe'));
        add_shortcode('roundprogress', array($this, 'vibe_roundprogress'));
        add_shortcode('pass_fail', array($this,'vibe_pass_fail'));
        add_shortcode('course_search',  array($this,'vibe_course_search'));
        add_shortcode('question',  array($this,'vibe_question'));
        add_shortcode('vibe_site_stats',  array($this,'vibe_site_stats'));
        add_shortcode('course_product',  array($this,'vibe_course_product_details'));
        add_shortcode('match',  array($this,'vibe_match'));
        add_shortcode('select',  array($this,'vibe_select'));
        add_shortcode('fillblank',  array($this,'vibe_fillblank'));
        add_shortcode('form_element',  array($this,'form_element'));
        add_shortcode('form',  array($this,'vibeform'));
        add_shortcode('progressbar',  array($this,'progressbar'));
        add_shortcode('heading',  array($this,'heading'));
        add_shortcode('gallery',  array($this,'gallery'));
        add_shortcode('map',  array($this,'gmaps'));
        add_shortcode('popup',  array($this,'vibe_popupajax'));
        add_shortcode('tagline',  array($this,'tagline'));
        add_shortcode('tooltip',  array($this,'tooltip'));
        add_shortcode( 'tab',  array($this,'vibe_tab' ));
        add_shortcode( 'tabs',  array($this,'vibe_tabs' ));
        add_shortcode('note',  array($this,'note'));
        add_shortcode( 'wpml_lang_selector',  array($this,'wpml_shortcode_func' ));
        add_shortcode('one_half',  array($this,'one_half'));
        add_shortcode('one_third',  array($this,'one_third'));
        add_shortcode('one_fourth',  array($this,'one_fourth'));
        add_shortcode('three_fourth',  array($this,'three_fourth'));
        add_shortcode('two_third',  array($this,'two_third'));
        add_shortcode('one_fifth',  array($this,'one_fifth'));
        add_shortcode('two_fifth',  array($this,'two_fifth'));
        add_shortcode('three_fifth',  array($this,'three_fifth'));
        add_shortcode('four_fifth',  array($this,'four_fifth'));
        add_shortcode('team_social', array($this,'team_social'));
        add_shortcode('team_member', array($this,'team_member'));
        add_shortcode('button', array($this,'button'));
        add_shortcode('alert', array($this,'alert'));
        add_shortcode('agroup', array($this,'agroup'));
        add_shortcode('accordion', array($this,'accordion'));
        add_shortcode('testimonial', array($this,'testimonial'));
        add_shortcode('user_only', array($this,'vibe_useronly'));
        add_shortcode('certificate_student_name', array($this,'vibe_certificate_student_name'));
        add_shortcode('certificate_student_photo', array($this,'vibe_certificate_student_photo'));
        add_shortcode('certificate_student_email', array($this,'vibe_certificate_student_email'));
        add_shortcode('certificate_course', array($this,'vibe_certificate_course'));
        add_shortcode('certificate_student_marks', array($this,'vibe_certificate_student_marks'));
        add_shortcode('certificate_student_field', array($this,'vibe_certificate_student_field'));
        add_shortcode('certificate_student_date', array($this,'vibe_certificate_student_date'));
        add_shortcode('course_completion_date', array($this,'vibe_certificate_course_finish_date'));
        add_shortcode('certificate_code', array($this,'vibe_certificate_code'));
        add_shortcode('certificate_course_field', array($this,'vibe_certificate_course_field'));
        add_shortcode('show_certificates', array($this,'vibe_show_certificates'));
        add_shortcode('certificate_course_duration', array($this,'vibe_certificate_course_duration'));
        add_shortcode('wplms_quiz_score',array($this,'vibe_wplms_quiz_score'));
        add_shortcode('course_instructor_emails',array($this,'vibe_course_instructor_emails'));
        add_shortcode('course_instructor', array($this,'vibe_certificate_course_instructor'));
        add_shortcode('wplms_registration_form',array($this,'wplms_registration_form'));
        add_shortcode('survey_result',array($this,'survey_result'));
        /*
        SPECIAL HOOKS IN INTEGRATION WITH SHORTCODES
        */
       add_action('bp_core_activated_user',array($this,'activate_user'),10,3);
    }


    function generate_rand(){
        $this->rand = wp_generate_password(6,false,false);//rand(0,999);
        return $this->rand;
    }
/*-----------------------------------------------------------------------------------*/
/*	Drop Caps
/*-----------------------------------------------------------------------------------*/

	function vibe_dropcaps( $atts, $content = null ) {
            
        $return ='<span class="dropcap">'.$content.'</span>';
        return $return;
	}
	
/*-----------------------------------------------------------------------------------*/
/*	Pull Quote
/*-----------------------------------------------------------------------------------*/


	function vibe_pullquote( $atts, $content = null ) {
        extract(shortcode_atts(array(
		  'style'   => 'left'
        ), $atts));
        $return ='<div class="pullquote '.$style.'">'.do_shortcode($content).'</div>';
        return $return;
	}
	


/*-----------------------------------------------------------------------------------*/
/*	SELL CONTENT WOOCOMMERCE SHORTCODE
/*-----------------------------------------------------------------------------------*/


	function vibe_sell_content( $atts, $content = null ) {
        extract(shortcode_atts(array(
			'product_id'    	 => '',
	    ), $atts));

        if(is_user_logged_in() && is_numeric($product_id)){
        	$user_id = get_current_user_id();
        	$check = wc_customer_bought_product('',$user_id,$product_id);
        	if($check){
        		echo apply_filters('the_content',$content);
        	}else{  
        		$product = get_product( $product_id );
				if(is_object($product)){
					$link = get_permalink($product_id);

					$check=vibe_get_option('direct_checkout');
        			if(isset($check) && $check)
        				$link.='?redirect';

        			$price_html = str_replace('class="amount"','class="amount" itemprop="price"',$product->get_price_html());

        		echo '<div class="message info">'.
        		sprintf(__('You do not have access to this content. <a href="%s" class="button"> Puchase </a> content for %s','vibe-shortcodes'),$link,$price_html).
        		'</div>';	
        		}else{
        			echo '<div class="message info">'.__('You do not have access to this content','vibe-shortcodes').'</div>';
        		}
        	}
        }else{
        		$product = get_product( $product_id );
				if(is_object($product)){
					$link = get_permalink($product_id);

					$check=vibe_get_option('direct_checkout');
        			if(isset($check) && $check)
        				$link.='?redirect';

        			$price_html = $product->get_price_html();

        		echo '<div class="message info">'.
        		sprintf(__('You do not have access to this content. <a href="%s" class="button"> Puchase </a> content for %s','vibe-shortcodes'),$link,$price_html).
        		'</div>';	
        		}else{
        			echo '<div class="message info">'.__('You do not have access to this content','vibe-shortcodes').'</div>';
        		}
        }

        return $return;
	}
	


/*-----------------------------------------------------------------------------------*/
/*	Social Buttons
/*-----------------------------------------------------------------------------------*/

	function vibe_social_buttons( $atts, $content = null ) {
           $return = social_sharing();
        return $return;
	}

/*-----------------------------------------------------------------------------------*/
/*	Social Sharing Buttons
/*-----------------------------------------------------------------------------------*/

	function vibe_social_sharing_buttons( $atts, $content = null ) {
           $return = vibe_socialicons();
        return $return;
	}
    

/*-----------------------------------------------------------------------------------*/
/*	Number Counter
/*-----------------------------------------------------------------------------------*/

	function vibe_number_counter( $atts, $content = null ) {
           extract(shortcode_atts(array(
		'min'   => 0,
		'max'   => 100,
		'delay' => 0,
		'increment'=>1,
                ), $atts));

        if(strlen($content)>2){
        	$m = do_shortcode($content);
        	if(is_numeric($m))
        		$max = $m;
        }
        wp_enqueue_script( 'counter-js', VIBE_PLUGIN_URL . '/vibe-shortcodes/js/scroller-counter.js',array('jquery'),'1.0',true);
        $return ='<div class="numscroller" data-max="'.$max.'" data-min="'.$min.'" data-delay="'.$delay.'" data-increment="'.$increment.'">'.$min.'</div>';
        return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	Vibe Container
/*-----------------------------------------------------------------------------------*/

	function vibe_container( $atts, $content = null ) {
            extract(shortcode_atts(array(
		'style'   => ''
                ), $atts));
        $return ='<div class="container '.$style.'">'.do_shortcode($content).'</div>';
        return $return;
	}
    

/*-----------------------------------------------------------------------------------*/
/*	IMG
/*-----------------------------------------------------------------------------------*/


	function vibe_img( $atts, $content = null ) {
        extract(shortcode_atts(array(
			'id'   => 0,
			'size' => 'thumb'
        ), $atts));
        $id=trim($id,"'");//intval();
    	$image =wp_get_attachment_image_src($id,$size);
    	$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        $return ='<img src="'.$image[0].'" class="'.$size.'" width="'.$image[1].'" height="'.$image[2].'" alt="'.$alt.'" />';
        return $return;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Pull Quote
/*-----------------------------------------------------------------------------------*/

	function vibe_allbages( $atts, $content = null ) {
            extract(shortcode_atts(array(
				'size'   => '60'
                ), $atts));
            global $wpdb;

            $all_badges = apply_filters('vibe_all_badges', $wpdb->get_results( "
			SELECT post_id,meta_value FROM $wpdb->postmeta
			WHERE 	meta_key 	= 'vibe_course_badge'
			AND meta_value REGEXP '^-?[0-9]+$'
		" ) );

        $user_id = get_current_user_id();
        $return ='<div class="allbadges">';
        if(isset($all_badges) && is_array($all_badges)){
        	$return .='<ul>';
        	foreach($all_badges as $badge){
        		if(is_object($badge)){
        			$badge_title=get_post_meta($badge->post_id,'vibe_course_badge_title',true);
        			$badge_image =wp_get_attachment_image_src( $badge->meta_value, 'full');
        			$check = get_user_meta($user_id,$badge->post_id,true);
        			$return .='<li '.(($check)?'class="finished"':'').'><a class="tip" title="'.$badge_title.'"><img src="'.$badge_image[0].'" alt="'.$badge->post_title.'" width="'.$size.'" />'.(($check)?'<span>'.__('EARNED','vibe-shortcodes').'</span>':'').'</a></li>';
        		}
	        }
	        $return .='</ul>';		
        }
        $return .='</div>';
        return $return;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Instructor
/*-----------------------------------------------------------------------------------*/


	function vibe_instructor( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'id'   => '1'
                ), $atts));
        $instructor = $id;
        $return ='<div class="course_instructor_widget">';
	    $return.= bp_course_get_instructor('instructor_id='.$instructor);
	    $return.= '<div class="description">'.bp_course_get_instructor_description('instructor_id='.$instructor).'</div>';
	    $return.= '<a href="'.get_author_posts_url($instructor).'" class="tip" title="'.__('Check all Courses created by ','vibe-shortcodes').bp_core_get_user_displayname($instructor).'"><i class="icon-plus-1"></i></a>';
	    $return.= '<h5>'.__('More Courses by ','vibe-shortcodes').bp_core_get_user_displayname($instructor).'</h5>';
	    $return.= '<ul class="widget_course_list">';
	    $query = new WP_Query( 'post_type=course&author='.$instructor.'&posts_per_page=5');
	    while($query->have_posts()):$query->the_post();
	    global $post;
	    $return.= '<li><a href="'.get_permalink($post->ID).'">'.get_the_post_thumbnail($post->ID,'thumbnail').'<h6>'.get_the_title($post->ID).'<span>by '.bp_core_get_user_displayname($post->post_author).'</span></h6></a>';
	    endwhile;
	    wp_reset_postdata();
	    $return.= '</ul>';
	    $return.= '</div>'; 
        return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	Divider
/*-----------------------------------------------------------------------------------*/

	function vibe_divider( $atts, $content = null ) {
            extract(shortcode_atts(array(
				'style'   => ''
                ), $atts));
        $return ='<hr class="divider '.$style.'" />';
        return $return;
	}

/*-----------------------------------------------------------------------------------*/
/*	COURSE
/*-----------------------------------------------------------------------------------*/

	function vibe_course( $atts, $content = null ) {
            extract(shortcode_atts(array(
					'id'   => '',
                    'featured_block'=>'course'
                ), $atts));
            $course_query = new WP_Query("post_type=course&p=$id");
            
            if($course_query->have_posts()){
            	while($course_query->have_posts()){
            		$course_query->the_post();
            	   			
            		if(function_exists('thumbnail_generator'))
        				$return = thumbnail_generator($course_query->posts[0],$featured_block,'medium',1,1,1);

            	}
            }

            wp_reset_postdata();
        return $return;
	}

/*-----------------------------------------------------------------------------------*/
/*  COURSE CATEGORY
/*-----------------------------------------------------------------------------------*/

    function vibe_course_category( $atts, $content = null ) {
        extract(shortcode_atts(array(
            'term'   => '',
            'taxonomy'=>'course-cat',
            'description'=>0,
            'padding' => '',
            'background' => '',
            'color'=>'#fff',
            'center'=>1,
            'radius'=>4
        ), $atts));
        
        $term = get_term_by('slug',$term,$taxonomy);
        if(empty($term))
            return;

        if(empty($background)){
            $thumbnail_id = get_term_meta($term->term_id,'course_cat_thumbnail_id',true);
            $background = wp_get_attachment_thumb_url( $thumbnail_id );
        }
        $return = '<div class="course_category" style="'.(empty($padding)?'':(is_numeric($padding)?'padding-top:'.$padding.'px;padding-bottom:'.$padding.'px;':'padding:'.$padding.';')).(empty($radius)?'':'border-radius:'.(is_numeric($radius)?$radius.'px;':$radius.';').';').(empty($center)?'':'text-align:center;').(empty($background)?'':'background:url('.$background.');').'">';
        
        $return .='<a href="'.get_term_link($term).'" style="color:'.$color.';"><strong>'.$term->name.'</strong>';
        $return .= (empty($description)?'':'<p>'.$description.'<p>');
        $return .='</a></div>';    
        return $return;
    }

/*-----------------------------------------------------------------------------------*/
/*  THE COURSE BUTTON
/*-----------------------------------------------------------------------------------*/


    function vibe_course_button( $atts, $content = null ) {
            extract(shortcode_atts(array(
                    'id'   => '',
                ), $atts));
            $return = '';
            if(!empty($id)){
                ob_start();
                the_course_button($id);    
                $return = ob_get_clean();
            }
            
        return $return;
    }


/*-----------------------------------------------------------------------------------*/
/*	Icon
/*-----------------------------------------------------------------------------------*/

	function vibe_icon( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'icon'   => 'icon-facebook',
                'size' => '',
                'bg' =>'',
                'hoverbg'=>'',
                'padding' =>'',
                'radius' =>'',
                'color' => '',
                'hovercolor' => ''
	), $atts));
        $rand = 'icon'.rand(1,9999);
        $return ='<style> #'.$rand.'{'.(isset($size)?'font-size:'.$size.';':'').''.((isset($bg))?'background:'.$bg.';':';').''.(isset($padding)?'padding:'.$padding.';':'').''.(isset($radius)?'border-radius:'.$radius.';':'').''.((isset($color))?'color:'.$color.';':'').'}
            #'.$rand.':hover{'.((isset($hovercolor))?'color:'.$hovercolor.';':'').''.((isset($hoverbg))?'background:'.$hoverbg.';':'').'}</style><i class="'.$icon.'" id="'.$rand.'"></i>';
	   return $return;
	}

    

/*-----------------------------------------------------------------------------------*/
/*	Video
/*-----------------------------------------------------------------------------------*/

	function vibe_iframevideo( $atts, $content = null ) {
	$return = '<div class="fitvids">'.html_entity_decode($content).'</div>';		
       return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	Iframe
/*-----------------------------------------------------------------------------------*/


	function vibe_iframe( $atts, $content = null ) {
		extract(shortcode_atts(array(
		'height'   => '',
		), $atts));
		$return = '<div class="iframecontent" '.((isset($height) && is_numeric($height))?'style="height:'.$height.'px;"':'').'><iframe src="'.html_entity_decode($content).'" width="100%"></iframe></div>';		
       return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	Round Progress
/*-----------------------------------------------------------------------------------*/

	function vibe_roundprogress( $atts, $content = null ) {
	extract(shortcode_atts(array(
                'style' => '',
		'percentage'   => '60',
                'radius' => '',
                'thickness' =>'',
                'color' =>'#333',
                'bg_color' =>'#65ABA6',
	), $atts));
        $rand = 'icon'.rand(1,9999);
        wp_enqueue_script( 'knobjs', VIBE_URL.'/assets/js/old_files/jquery.knob.js' );
        $return ='<figure class="knob" style="width:'.($radius+10).'px;min-height:'.($radius+10).'px;">
                    <input class="dial" data-skin="'.$style.'" data-value="'.$percentage.'" data-fgColor="'.$color.'" data-bgColor="'.$bg_color.'" data-height="'.$radius.'" data-inputColor="'.$color.'" data-width="'.$radius.'" data-thickness="'.($thickness/100).'" value="'.$percentage.'" data-readOnly=true />
                        <div class="knob_content"><h3 style="color:'.$color.';">'.do_shortcode($content).'</h3></div>
                  </figure>';
        return $return;
	}
	


/*-----------------------------------------------------------------------------------*/
/*	WPML Language Selector shortcode
/*-----------------------------------------------------------------------------------*/

//[wpml_lang_selector]
    function wpml_shortcode_func(){
        do_action('icl_language_selector');
    }



/*-----------------------------------------------------------------------------------*/
/*	Note
/*-----------------------------------------------------------------------------------*/


	function note( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'style'   => '',
                'bg' =>'',
                'border' =>'',
                'bordercolor' =>'',
                'color' => ''
	), $atts));
	   return '<div class="notification '.$style.'" style="background-color:'.$bg.';border-color:'.$border.';">
			<div class="notepad" style="color:'.$color.';border-color:'.$bordercolor.';">' . do_shortcode($content) . '</div></div>';
	}

/*-----------------------------------------------------------------------------------*/
/*	Column Shortcode
/*-----------------------------------------------------------------------------------*/


	function one_half( $atts, $content = null ) {
	    $clear='';
	    if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	      $clear='clearfix';
	      
            return '<div class="one_half '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function one_third( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
	  
	   return '<div class="one_third '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function one_fourth( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
             return '<div class="one_fourth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';	}
	

	function three_fourth( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
             return '<div class="three_fourth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}
	

	function two_third( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
            return '<div class="two_third"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function one_fifth( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
            return '<div class="one_fifth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function two_fifth( $atts, $content = null ) {
            return '<div class="two_fifth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function three_fifth( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
            return '<div class="three_fifth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}


	function four_fifth( $atts, $content = null ) {
	$clear='';
	if (isset($atts['first']) && strpos($atts['first'],'first') !== false)
	  $clear='clearfix';
            return '<div class="four_fifth '.$clear.'"><div class="column_content '.(isset($atts['first'])?$atts['first']:'').'">' . do_shortcode($content) . '</div></div>';
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Team
/*-----------------------------------------------------------------------------------*/



	function team_member( $atts, $content = null ) {
            extract(shortcode_atts(array(
                        'style' => '',
                        'pic' => '',
			'name'   => '',
                        'designation' => ''
	    ), $atts));
	    
	    $output  = '<div class="team_member '.$style.'">';
            
            if(isset($pic) && $pic !=''){
                if(preg_match('!(?<=src\=\").+(?=\"(\s|\/\>))!',$pic, $matches )){
                    $output .= '<img src="'.$matches[0].'" class="animate zoom" alt="'.$name.'" />';
                }else{
                    $output .= '<img src="'.$pic.'" class="animate zoom" alt="'.$name.'" />';
                }
            }
            $output .= '<div class="member_info">';
            (isset($name) && $name !='')?$output .= '<h3>'.html_entity_decode($name).''.((isset($designation) && $designation !='')?' <small>[ '.$designation.' ]</small>':'').'</h3>':'';
            
            $output .= '<span class="clear"></span>';
            $output .= '<ul class="team_socialicons">';
            $output .=do_shortcode($content);
            $output .= '</ul></div>
                </div>';
            return $output;
	}


	function team_social( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'icon' => 'icon-facebook',
            'url' => ''
	    ), $atts));
           $class=str_replace('icon-','',$icon);
	   return '<li><a href="'.$url.'" title="'.apply_filters('vibe_shortcodes_team_social',$class).'" class="'.$class.'"><i class="'.$icon.'"></i></a></li>';;
	}
	


/*-----------------------------------------------------------------------------------*/
/*	Buttons
/*-----------------------------------------------------------------------------------*/


	function button( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'url' => '#',
			'target' => '_self',
                        'class' => 'base',
			'bg' => '',
			'hover_bg' => '',
			'color' => '',
                        'size' => 0,
                        'width' => 0,
                        'height' => 0,
                        'radius' => 0,
	    ), $atts));
		
             $rand = 'button'.rand(1,9999);
           $return ='<style> #'.$rand.'{'.(($bg)?'background-color:'.$bg.' !important;':'').''.(($color)?'color:'.$color.' !important;':'').''.(($size!= '0px')?'font-size:'.$size.' !important;':'').''.(($width!= '0px')?'width:'.$width.';':'').''.(($height!= '0px')?'padding-top:'.$height.';padding-bottom:'.$height.';':'').''.(($radius!= '0px')?'border-radius:'.$radius.';':'').'} #'.$rand.':hover{'.(($hover_bg)?'background-color:'.$hover_bg.' !important;':'').'}</style><a target="'.$target.'" id="'.$rand.'" class="button '.$class.'" href="'.$url.'">'.do_shortcode($content) . '</a>';
                
                 return $return;
	}




/*-----------------------------------------------------------------------------------*/
/*	Alerts
/*-----------------------------------------------------------------------------------*/


	function alert( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'style'   => 'block',
                        'bg' => '',
                        'border' =>'',
                        'color' => '',
	    ), $atts));
		
           return '<div class="alert alert-'.$style.'" style="'.(($color)?'color:'.$color.';':'').''.(($bg)?'background-color:'.$bg.';':'').''.(($border)?'border-color:'.$border.';':'').'">'
                     . do_shortcode($content) . '</div>';
	}


/*-----------------------------------------------------------------------------------*/
/*	Accordion Shortcodes
/*-----------------------------------------------------------------------------------*/



	function agroup( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'first'   => '',
        'connect' => '',
	), $atts));
	 $random_number = $this->generate_rand(); 
        if(!empty($connect)){
            $random_number = $connect;
        }  
	   return '<div class="accordion '.(($first)?'load_first':'').'" id="accordion'.$random_number.'">' . 
                   do_shortcode($content) . '</div>';
	}
	
	function accordion( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'title' => 'Title goes here',
            'connect' => ''
	    ), $atts));
        $random_number = $this->rand;   
        if(!empty($connect)){
            $random_number = $connect;
        }
        $new_random_number=strtolower(wp_generate_password(6,false,false));
        $check_url = strpos($content,'http');
        if($check_url !== false && $check_url < 2){
        	return '<div class="accordion-group panel">
                     <div class="accordion-heading">
                        <a href="'.$content.'" class="accordion-toggle collapsed" target="_blank">
                            <i></i> '. $title .'</a>
                    </div>
                   </div>';
        }else{
        	return '<div class="accordion-group panel">
                     <div class="accordion-heading">
                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion'.$random_number.'"  href="#collapse_'.$new_random_number.'">
                            <i></i> '. $title .'</a>
                    </div>
                    <div id="collapse_'.$new_random_number.'" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <p>'. do_shortcode($content) .'</p>
                        </div>
                   </div>
                   </div>';
        }
	   
	}
	





/*-----------------------------------------------------------------------------------*/
/*	Testimonial Shortcodes
/*-----------------------------------------------------------------------------------*/


	function testimonial( $atts, $content = null ) {
	global $vibe_options;
	    extract(shortcode_atts(array(
			'id'    	 => '',
            'length'    => 100,
	    ), $atts));
    
    if($id == 'random'){
    	$args=array('post_type'=>'testimonials', 'orderby'=>'rand', 'posts_per_page'=>'1','fields=ids');
		$testimonials=new WP_Query($args);
		while ($testimonials->have_posts()) : $testimonials->the_post();
			$postdata = get_post(get_the_ID());
		endwhile;	
		wp_reset_postdata();
    }else{
    	$postdata=get_post($id);
    }
    
    if(function_Exists('thumbnail_generator')){
    	$return = thumbnail_generator($postdata,'testimonial',3,$length,0,0);
    }

   return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	User Only
/*-----------------------------------------------------------------------------------*/


	function vibe_useronly( $atts, $content = null ) {
            extract(shortcode_atts(array(
				'id'   => 1
            ), $atts));
            $return = '';
            if(is_user_logged_in()){
            	if(isset($id) ){
	            	if(is_numeric($id)){
	            		if($id == get_current_user_id()){
	            			$return ='<div class="user_only_content">'.do_shortcode($content).'</div>';
	            		}
	            	}else{
	            		$ids = explode(',',$id);
	            		foreach($ids as $id){
	            			if(is_numeric($id) && $id == get_current_user_id()){
		            			$return ='<div class="user_only_content">'.do_shortcode($content).'</div>';
		            		}
	            		}
	            	}
            	}else{
                    $return ='<div class="user_only_content">'.do_shortcode($content).'</div>';
                }
            }
        return $return;
	}
	



/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : Student Name
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_student_name( $atts, $content = null ) {
            $id=$_GET['u'];
            if(isset($id) && $id)
        		return bp_core_get_user_displayname($id);
        	else
        		return '[certificate_student_name]';
	}
	


/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : Student Photo
/*-----------------------------------------------------------------------------------*/

	function vibe_certificate_student_photo( $atts, $content = null ) {
            $id=$_GET['u'];
            if(isset($id) && $id)
        		return bp_core_fetch_avatar(array('item_id'=>$id,'type'=>'thumb'));
        	else
        		return '[certificate_student_photo]';
	}

/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : Student Email
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_student_email( $atts, $content = null ) {
            $id=$_GET['u'];
            if(isset($id) && $id)
        		return get_the_author_meta('user_email',$id);
        	else
        		return '[certificate_student_email]';
	}




/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : COURSE NAME
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_course( $atts, $content = null ) {
            $id=$_GET['c'];
            if(isset($id) && $id)
        		return get_the_title($id);
        	else
        		return '[certificate_course]';
	}



/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : COURSE MARKS
/*-----------------------------------------------------------------------------------*/

	function vibe_certificate_student_marks( $atts, $content = null ) {
            $uid=$_GET['u'];
             $cid=$_GET['c'];
            if(isset($uid) && is_numeric($uid) && isset($cid) && is_numeric($cid)  && get_post_type($cid) == 'course')
        		return get_post_meta($cid,$uid,true);
        	else
        		return '[certificate_student_marks]';
	}

/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : STUDENT FIELD
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_student_field( $atts, $content = null ) {
			extract(shortcode_atts(array(
				'field'    	 => '',
		    ), $atts));
            $uid=$_GET['u'];
            if(isset($uid) && is_numeric($uid) && isset($field) && strlen($field)>3)
        		return bp_get_profile_field_data( 'field='.$field.'&user_id=' .$uid);
        	else
        		return '[certificate_student_field]';
	}


/*-----------------------------------------------------------------------------------*/
/*    CERTIFICATE SHORTCODES  : CERTIFICATE DATE
/*-----------------------------------------------------------------------------------*/


    function vibe_certificate_student_date( $atts, $content = null ) {
           $uid=$_GET['u'];
           $cid=$_GET['c'];
           global $bp,$wpdb;

           if(isset($uid) && is_numeric($uid) && isset($cid) && is_numeric($cid) && get_post_type($cid) == 'course'){
           $course_submission_date = $wpdb->get_var($wpdb->prepare( "
                                SELECT activity.date_recorded FROM {$bp->activity->table_name} AS activity
                                WHERE     activity.component     = 'course'
                                AND     activity.type     = 'student_certificate'
                                AND     user_id = %d
                                AND     item_id = %d
                                ORDER BY date_recorded DESC LIMIT 0,1
                            " ,$uid,$cid));

                  if(isset($course_submission_date)){
                   return date_i18n( get_option( 'date_format' ), strtotime($course_submission_date));                    
                  }else{
                   $date = $wpdb->get_var($wpdb->prepare( "
                                            SELECT activity.date_recorded
                                            FROM {$bp->activity->table_name} AS activity 
                                            LEFT JOIN {$bp->activity->table_name_meta} as meta ON activity.id = meta.activity_id
                                            WHERE     activity.component     = 'course'
                                            AND     activity.type     = 'bulk_action'
                                            AND     meta.meta_key   = 'add_certificate'
                                            AND     meta.meta_value = %d
                                            AND     activity.item_id = %d
                                            ORDER BY date_recorded DESC LIMIT 0,1
                                        " ,$uid,$cid));

                       if(isset($date)){
                        return date_i18n( get_option( 'date_format' ), strtotime($date));                    
                       }
                  }
           }    
       return '[certificate_student_date]';
    }

/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : COURSE COMPLETION DATE
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_course_finish_date( $atts, $content = null ) {
            $uid=$_GET['u'];
            $cid=$_GET['c'];
            global $bp,$wpdb;

            if(isset($uid) && is_numeric($uid) && isset($cid) && is_numeric($cid) && get_post_type($cid) == 'course'){
            $course_submission_date = $wpdb->get_var($wpdb->prepare( "
								SELECT activity.date_recorded FROM {$bp->activity->table_name} AS activity
								WHERE 	activity.component 	= 'course'
								AND 	activity.type 	= 'submit_course'
								AND 	user_id = %d
								AND 	item_id = %d
								ORDER BY date_recorded DESC LIMIT 0,1
							" ,$uid,$cid));

    			return date_i18n(get_option( 'date_format' ), strtotime($course_submission_date));  
        	}	
    	return '[course_completion_date]';
	}


/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : CERTIFICATE CODE
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_code( $atts, $content = null ) {
            $uid=$_GET['u'];
            $cid=$_GET['c'];
            if(isset($uid) && is_numeric($uid) && isset($cid) && is_numeric($cid) && get_post_type($cid) == 'course'){
            	$ctemplate=get_post_meta($cid,'vibe_certificate_template',true);
            	if(isset($ctemplate) && $ctemplate){
            		$code = $ctemplate.'-'.$cid.'-'.$uid;
            	}else{
            		$code = get_the_ID().'-'.$cid.'-'.$uid;
            	}
            	return apply_filters('wplms_certificate_code',$code,$cid,$uid);
            }
            else
        		return '[certificate_code]';
	}


/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : CERTIFICATE COURSE INSTRUCTOR
/*-----------------------------------------------------------------------------------*/


	function vibe_certificate_course_instructor( $atts, $content = null ) {
            $cid=$_GET['c'];
            if(isset($cid) && is_numeric($cid) && get_post_type($cid) == 'course'){
            	$course=get_post($cid);
            	$instructor = apply_filters('wplms_course_instructors',$course->post_author,$course->ID);
            	if(!isset($instructor))
            		return;
            	
            	if(is_array($instructor)){

            	}else{

            	}
            	return get_the_author_meta('display_name',$instructor);
            }
            else
        		return '[course_instructor]';
	}



/*-----------------------------------------------------------------------------------*/
/*	CERTIFICATE SHORTCODES  : CERTIFICATE COURSE FIELD
/*-----------------------------------------------------------------------------------*/

	function vibe_certificate_course_field( $atts, $content = null ) {
			extract(shortcode_atts(array(
			'field'   => '',
            'course_id' =>'',
	    	), $atts));

	    	if(!isset($course) || !is_numeric($course)){
	    		$course_id=$_GET['c'];
	    	}

            if(isset($course_id) && is_numeric($course_id) && get_post_type($course_id) == 'course'){
            	$value = get_post_meta($course_id,$field,true);
            	if(isset($value)){
            		return apply_filters('vibe_certificate_course_field',$value,$atts);
            	}else
        			return '[certificate_course_field]';
            }else
        		return '[certificate_course_field]';
	}
	
/*-----------------------------------------------------------------------------------*/
/*  CERTIFICATE SHORTCODES  : CERTIFICATE COURSE DURATION
/*-----------------------------------------------------------------------------------*/

    function vibe_certificate_course_duration( $atts, $content = null ) {
            extract(shortcode_atts(array(
            'student_id'   => '',
            'course_id' =>'',
            'force'=>'0'
            ), $atts));

            if(!isset($course) || !is_numeric($course)){
                $course_id=$_GET['c'];
            }
            if(!isset($student) || !is_numeric($student)){
                $student_id=$_GET['u'];
            }
            

            if(isset($course_id) && is_numeric($course_id) && get_post_type($course_id) == 'course'){
                
                if(function_exists('bp_course_get_course_duration')){
                    $duration = bp_course_get_course_duration($course_id,$user_id); 
                    if(!empty($force)){
                        $return = $duration;
                    }
                }else{
                    return '[certificate_course_duration]';
                }

                if(empty($force)){
                    global $bp,$wpdb;

                    $start_time = $wpdb->get_var($wpdb->prepare("
                        SELECT date_recorded 
                        FROM {$bp->activity->table_name} 
                        WHERE type ='subscribe_course' 
                        AND item_id=%d 
                        AND component='course' 
                        AND (user_id=%d OR secondary_item_id=%d) 
                        ORDER BY id DESC LIMIT 1", $course_id,$student_id,$student_id));

                    if(empty($start_time)){
                        $return = $duration;
                    }else{
                        $start_timestamp = strtotime($start_time);
                        $end_time = $wpdb->get_var($wpdb->prepare("
                            SELECT date_recorded 
                            FROM {$bp->activity->table_name} 
                            WHERE type ='submit_course' 
                            AND item_id=%d 
                            AND component='course' 
                            AND (user_id=%d OR secondary_item_id=%d) 
                            ORDER BY id DESC LIMIT 1", $course_id,$student_id,$student_id));
                        if(empty($end_time)){
                            $return = time()-$start_timestamp;
                        }else{
                            $return = strtotime($end_time) - $start_timestamp;
                        }
                    }
                }    
                if(!empty($return) && is_numeric($return)){
                    if($return > $duration){
                        $return = $duration;
                    }
                    if(empty($force) || $force == 1){
                        return tofriendlytime($return);
                    }else{
                        return floor($return/$force).' '.calculate_duration_time($force);
                    }
                }else{
                    return '[certificate_course_duration]';
                }
            }else{
                return '[certificate_course_duration]';
            }
    }

/*-----------------------------------------------------------------------------------*/
/*  DISPLAY QUIZ SCORE FOR A USER
/*-----------------------------------------------------------------------------------*/

  function vibe_wplms_quiz_score($atts, $content = null ){
    extract(shortcode_atts(array(
        'id'   => '',
        'user_id'=>'',
        'marks'=>'1',
        'total' => '1'
          ), $atts));

    if(empty($id)){
        global $post;
        if($post->post_type != 'quiz'){
            return '';
        }
        $id=$post->ID;
    }

    if(empty($user_id)){
        $user_id = get_current_user_id();
    }

    if(function_exists('bp_course_get_quiz_questions') && !empty($user_id) ){
        $questions = bp_course_get_quiz_questions($id,$user_id);
        $total_sum =0;
        foreach($questions['ques'] as $key=>$question){
            $total_sum=$total_sum+intval($questions['marks'][$key]);
        }
        $user_marks=get_post_meta($id,$user_id,true);
        if(isset($user_marks)){
            if(!empty($marks) && empty($total)){
                return $user_marks;
            }elseif(!empty($total) && empty($marks)){
                return $total_sum;
            }elseif(!empty($marks) && !empty($total)){
                return sprintf(__('%d out of %d','vibe-shortcodes'),$user_marks,$total_sum);
            }else{
                return __('N.A','vibe-shortcodes');
            }
        }else{
            return __('N.A','vibe-shortcodes');
        }
    }
}

/*-----------------------------------------------------------------------------------*/
/*	Tabs Shortcodes
/*-----------------------------------------------------------------------------------*/

	function vibe_tabs( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'style'   => '',
            'theme'   => '',
            'connect' => ''
	    ), $atts));
            
		$defaults=$tab_icons = array();
                extract( shortcode_atts( $defaults, $atts ) );
		
		// Extract the tab titles for use in the tab widget.
		preg_match_all( '/tab title="([^\"]+)" icon="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );
		
		$tab_titles = array();
        
		if(!count($matches[1])){ 
		preg_match_all( '/tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

		if( isset($matches[1]) ){ $tab_titles = $matches[1];}
		}else{
		if( isset($matches[1]) ){ $tab_titles = $matches[1]; $tab_icons= $matches[2];}
		}
		
		
		$output = '';

        $random_number = $this->generate_rand();
        if(!empty($connect)){
            $random_number = $connect;
        }
		if( count($tab_titles) ){
		    $output .= '<div id="vibe-tabs-'. rand(1, 100) .'" class="tabs tabbable '.$style.' '.$theme.'">';
			$output .= '<ul class="nav nav-tabs clearfix">';

         	foreach( $tab_titles as $i=>$tab ){

                $tabstr=crc32($tab[0]); 

                $check_url = strpos($tab_icons[$i][0],'http');

                if(isset($tab_icons[$i][0]) && $check_url !== false && $check_url<2){
                	$href = $tab_icons[$i][0];
                }else{
                	$href='#tab-'. $tabstr .'-'.$random_number;
	            }

				$output .= '<li><a href="'.$href.'">';
				
				if(isset($tab_icons[$i][0]))
					$output.='<span><i class="' . $tab_icons[$i][0] . '"></i></span>';

				$output .= $tab[0] . '</a></li>';
			}
		    $output .= '</ul><div class="tab-content">';
		    $output .= do_shortcode( $content );
		    $output .= '</div></div>';
		} else {
			$output .= do_shortcode( $content );
		}
		
		return $output;
	}


	function vibe_tab( $atts, $content = null ) { 
		$defaults = array( 'title' => 'Tab','connect'=>'' );
		extract( shortcode_atts( $defaults, $atts ) );

		$random_number = $this->rand;
        if(!empty($connect)){
            $random_number = $connect;
        }
        
        $tabstr=crc32($title); 
		return '<div id="tab-'. $tabstr .'-'.$random_number.'" class="tab-pane"><p>'. do_shortcode( $content ) .'</p></div>';
	}

/*-----------------------------------------------------------------------------------*/
/*	Tooltips
/*-----------------------------------------------------------------------------------*/


	function tooltip( $atts, $content = null ) {
		extract(shortcode_atts(array(
	        'direction'   => 'top',
	        'tip' => 'Tooltip',
	    ), $atts));
		$istyle='';

           return '<a data-rel="tooltip" class="tip" data-placement="'.$direction.'" data-original-title="'.$tip.'">'.do_shortcode($content).'</a>';

	}





/*-----------------------------------------------------------------------------------*/
/*	Taglines
/*-----------------------------------------------------------------------------------*/


	function tagline( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'style'   => '',
                        'bg'   => '',
                        'border'   => '',
                        'bordercolor'   => '',
                        'color'   => '',
	    ), $atts));
           return '<div class="tagline '.$style.'" style="background:'.$bg.';border-color:'.$border.';border-left-color:'.$bordercolor.';color:'.$color.';" >'.do_shortcode($content).'</div>';
	}


/*-----------------------------------------------------------------------------------*/
/*	POPUP
/*-----------------------------------------------------------------------------------*/


	function vibe_popupajax( $atts, $content = null ) {
            extract(shortcode_atts(array(
            	'id'   => '',
                'auto' => 0,
                'classes' =>''
            ), $atts));


  
   $newreturn='';
    if($auto){
     $newreturn .='<script>jQuery(window).load(function(){ jQuery("#anchor_popup_'.$id.'").trigger("click");});</script>'; 
    }
        
        $newreturn .= '<a class="popup-with-zoom-anim ajax-popup-link '.$classes.'" href="'.admin_url('admin-ajax.php').'?ajax=true&action=vibe_popup&id='.$id.'" id="anchor_popup_'.$id.'">
                   '.do_shortcode($content).'</a>';
        return $newreturn;

	}


/*-----------------------------------------------------------------------------------*/
/*	Google Maps shortcode
/*-----------------------------------------------------------------------------------*/


	function gmaps( $atts, $content = null ) { 
                        $map ='<div class="gmap">'.$content.'</div>';
                        return $map;
	}



/*-----------------------------------------------------------------------------------*/
/*	Gallery shortcode
/*-----------------------------------------------------------------------------------*/

	function gallery( $atts, $content = null ) { 
           extract(shortcode_atts(array(
                        'size' => 'normal',
                        'columns'=>5,
                        'ids' => ''
                            ), $atts));

            $output = apply_filters('post_gallery', '', $atts);
            if ( $output != '' )
                return $output;

            $gallery='<div class="gallery '.$size.' columns'.$columns.'">';
            
            
                if(isset($ids) && $ids!=''){
                    $rand='gallery'.rand(1,999);
                    $posts=explode(',',$ids);
                    foreach($posts as $post_id){
                         // IF Ids are not Post Ids
                           if ( wp_attachment_is_image( $post_id ) ) {
                               $attachment_info = wp_get_attachment_info($post_id);
                               
                               $full=wp_get_attachment_image_src( $post_id, 'full' );
                               $thumb=wp_get_attachment_image_src( $post_id, $size );
                               
                               if(is_array($thumb))$thumb=$thumb[0];
                                if(is_array($full))$full=$full[0];
                                
                               $gallery.='<a href="'.$full.'" title="'.$attachment_info['title'].'">
                                            <img src="'.$thumb.'" alt="'.$attachment_info['title'].'" />
                                            </a>';
                            }
                    }
                }
            $gallery.='</div>';
        return $gallery;
	}


/*-----------------------------------------------------------------------------------*/
/*	HEADING
/*-----------------------------------------------------------------------------------*/

	function heading( $atts, $content = null ) { 
             extract(shortcode_atts(array(
                        'style' => '',
                            ), $atts));
                return '<h3 class="heading '.$style.'"><span>'.do_shortcode($content).'</span></h3>';
	}

    




/*-----------------------------------------------------------------------------------*/
/*	PROGRESSBARS
/*-----------------------------------------------------------------------------------*/


    function progressbar( $atts, $content = null ) { 
            extract(shortcode_atts(array(
                         'color' => '',
                         'bar_color' => '#009dd8',
                         'bg' => '',
                         'percentage' => '20'
                             ), $atts));
                
           return '<div class="progressbar_wrap"><strong>'.do_shortcode($content).'</strong>
           <div class="progress" '.(($bg)?'style="background-color:'.$bg.';"':'').'>
             <div class="bar animate stretchRight" style="width: '.$percentage.'%;'.(($color)?'background-color:'.$bar_color.';':'').'"><span>'.$percentage.'%</span></div>
           </div></div>';

    }
    
    


/*-----------------------------------------------------------------------------------*/
/*	FORMS
/*-----------------------------------------------------------------------------------*/

	function vibeform( $atts, $content = null ) { 
            extract(shortcode_atts(array(
			             'to' => '',
                         'subject' => '',
                         'isocharset' => '',
			             ), $atts));

            global $post;
            if($to == '{{instructor}}'){
                $to = $this->vibe_course_instructor_emails('');
            }
            $nonce = wp_create_nonce( 'vibeform_security'.$to);
            return apply_filters('vibe_shortcode_form','<div class="form">
           	 <form method="post" data-to="'.$to.'" data-subject="'.$subject.'" '.(($isocharset)?'class="isocharset"':'').'>'.
                    do_shortcode($content)  
           	 .'<div class="response" data-security="'.$nonce.'"></div></form>
           	 </div>');

	}

	function form_element( $atts, $content = null ) {
            extract(shortcode_atts(array(
            'type' => 'text',
            'validate' => '',
            'options' => '',
            'autofocus'=>0,
            'placeholder' => 'Name'
        ), $atts));
           
            $output='';
            $r =  rand(1,999);

            switch($type){
                case 'text': $output .= '<input type="text" placeholder="'.$placeholder.'" class="form_field text" data-validate="'.$validate.'" '.(empty($autofocus)?'':'autofocus').'/>';
                    break;
                case 'textarea': $output .= '<textarea placeholder="'.$placeholder.'" class="form_field  textarea" data-validate="'.$validate.'"></textarea>';
                    break;
                case 'select': $output .= '<select class="form_field  select" placeholder="'.$placeholder.'">';
                                $output .= '<option value="">'.$placeholder.'</option>';
                                $options  = explode(',',$options);
                                foreach($options as $option){
                                    $output .= '<option value="'.$option.'">'.$option.'</option>';
                                }
                                $output .= '</select>';
                    break;
                case 'captcha': $output .='<i class="math_sum"><span id="num'.$r.'-1">'.rand(1,9).'</span><span> + </span><span id="num'.$r.'-2">'.rand(1,9).'</span><span> = </span></i><input id="num'.$r.'" type="text" placeholder="0" class="form_field text small" data-validate="captcha" />';
                    break;    
                case 'submit':
                    $output .= '<input type="submit" class="form_submit button primary" value="'.$placeholder.'" />';
                    break;
            }

       return $output;
    }

/*-----------------------------------------------------------------------------------*/
/*  INSTRUCTOR EMAILS FOR COURSE ID
/*-----------------------------------------------------------------------------------*/

    function vibe_course_instructor_emails($atts, $content = null ) {
        extract(shortcode_atts(array(
        'course_id' => ''
        ), $atts));

        if(empty($course_id)){
            global $post;
            $course_id = $post->ID;
            if($post->post_type == 'course'){
                $course_id = $post->ID;
                $course_authors = apply_filters('wplms_course_instructors',$post->post_author,$post->ID);
                if(!is_array($course_authors)){
                    $course_authors = array($course_authors);
                }
            }
        }else{
            $instructor_ids = apply_filters('wplms_course_instructors',get_post_field('post_author', $course_id),$course_id); 
            if(!is_array($instructor_ids)){
                $instructor_ids = array($instructor_ids);
            }
        }

        global $post;

        $to = array();
        if(empty($course_authors)){
            return '';
        }
        foreach($course_authors as $instructor_id){
            $user = get_user_by( 'id', $instructor_id);
            $to[] = $user->user_email;
        }
        
        if(is_array($to))
            $to = implode(',',$to);

        return $to;
    }
/*-----------------------------------------------------------------------------------*/
/*	QUIZ SHORTCODE : FILLBLANK
/*-----------------------------------------------------------------------------------*/

	function vibe_fillblank( $atts, $content = null ) {
        global $post; 
        $user_id=get_current_user_id();
        $answers=get_comments(array(
          'post_id' => $post->ID,
          'status' => 'approve',
          'user_id' => $user_id
          ));

        $content =' ';
        if(isset($answers) && is_array($answers) && count($answers)){
            $answer = reset($answers);
            $content = $answer->comment_content;
        }
        if((function_exists('bp_is_user') && bp_is_user()) || (function_exists('bp_is_member') && bp_is_member()))
        	return '____________';


    	$return ='<p class="live-edit" data-model="article" data-url="/articles"><span class="vibe_fillblank" data-editable="true" data-name="content" data-max-length="250" data-text-options="true">'.((strlen($content)>2)?$content:'<p><br></p>').'</span></p>';

    	return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	QUIZ SHORTCODE : SELECT
/*-----------------------------------------------------------------------------------*/

	function vibe_select( $atts, $content = null ) {
        global $post; 

        if(is_user_logged_in()){
            $user_id=get_current_user_id();
            $answers=get_comments(array(
              'post_id' => $post->ID,
              'status' => 'approve',
              'user_id' => $user_id
            ));
        }

        $content ='';
        if(isset($answers) && is_array($answers) && count($answers)){
            $answer = reset($answers);
            $content = $answer->comment_content;
        }   

        $original_options = get_post_meta(get_the_ID(),'vibe_question_options',true);   
        $options = $original_options;

        if(!empty($atts['options']) && strpos($atts['options'], ',') !== false){
            $set_options = explode(',',$atts['options']);
            if(!empty($options)){
                foreach($options as $k=>$option){
                    if(!in_array(($k+1),$set_options)){
                        unset($options[$k]);
                    }
                }
            }
        }
        

        if(!is_array($options) || !count($options))
        	return '&laquo; ______ &raquo;';

        $return = '<select class="vibe_select_dropdown">';
        foreach($options as $key=>$value){
            $t = array_search($value,$original_options);
            $return .= '<option value="'.($t+1).'" '.(($k == $content)?'selected':'').'>'.$value.'</option>';
        }
    	$return .= '</select>';
    	return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	QUIZ SHORTCODE : MATCH
/*-----------------------------------------------------------------------------------*/

	function vibe_match( $atts, $content = null ) {
		global $post; 

        //Get the last marked answer
        if(is_user_logged_in()){
            $user_id=get_current_user_id();
            $answers=get_comments(array(
              'post_id' => $post->ID,
              'status' => 'approve',
              'user_id' => $user_id
            ));    
        }
        
        $string ='';
        if(isset($answers) && is_array($answers) && count($answers)){
            $answer = reset($answers);
            $option_matches = explode(',',$answer->comment_content);
            foreach($option_matches as $k=>$option_match){
            	$string .= ' data-match'.$k.'="'.$option_match.'"';
            }
        }
		return '<div class="matchgrid_options '.((isset($answers) && is_array($answers) && count($answers))?'saved_answer':'').' "'.$string.'>'.do_shortcode($content).'</div>';
	}

/*-----------------------------------------------------------------------------------*/
/*	Course Product
/*-----------------------------------------------------------------------------------*/

	function vibe_course_product_details( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'id' => '',
			'details' => '',
	    ), $atts));
		
		if(isset($id) && is_numeric($id)){
     		$course_id = $id;	
		}else{
			if(isset($_GET['c']) && is_numeric($_GET['c']))
				$course_id=$_GET['c']; // For certificate use
			else
				return;
		}

		if(get_post_type($course_id) == BP_COURSE_CPT){
			$product_id = get_post_meta($course_id,'vibe_product',true);
			if(isset($product_id) && is_numeric($product_id)){
				switch($details){
					case 'sku':
						$return = get_post_meta($product_id,'_sku',true);
					break;
					case 'price':
						$product = wc_get_product( $product_id );
						$return = $product->get_price_html();
					break;
					case 'sales':
						$return = get_post_meta($product_id,'total_sales',true);
					break;
					case 'note':
						$return = get_post_meta($product_id,'_purchase_note',true);
					break;
				}
			}
		}
		return $return;
	}


/*-----------------------------------------------------------------------------------*/
/*	Vibe site stats [vibe_site_stats total=1 courses=1 instructors=1 ]
/*-----------------------------------------------------------------------------------*/

	function vibe_site_stats($atts, $content = null){
		extract(shortcode_atts(array(
		'total'   => 0,
		'courses'   =>0,
		'instructor' => 0,
		'groups' => 0,
		'subscriptions' => 0,
		'sales' => 0,
		'commissions' => 0,
		'posts'=>0,
		'comments'=>0,
		'number'=>0
        ), $atts));
		
		$return = array();
		$users =count_users();
		if($total){
			$return['total'] = $users['total_users'];
			if($number)
				return $return['total'];
		}
		if($instructor){
            $count = $users['avail_roles']['instructor'];
            $flag = apply_filters('wplms_show_admin_in_instructors',1);
            if(isset($flag) && $flag){
                $count += $users['avail_roles']['administrator'];
            }
			$return['instructor'] = $count;

			if($number)
				return $return['instructor'];
		}

		if($courses){
            if(function_exists('bp_course_get_total_course_count')){
                $return['courses'] = bp_course_get_total_course_count( );
            }else{
                $count_posts = wp_count_posts('course');
                $return['courses'] = $count_posts->publish;
            }
            if($number)
                return $return['courses']; 
        }
		
		if($groups){
			global $wpdb,$bp;
			$count = $wpdb->get_results("SELECT count(*) as count FROM {$bp->groups->table_name}",ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['groups']=$count[0]['count'];
			}else{
				$return['groups']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		if($subscriptions){
			global $wpdb,$bp;
			$count = $wpdb->get_results("SELECT count(*) as count FROM {$wpdb->postmeta} WHERE meta_key REGEXP '^[0-9]+$' AND meta_value REGEXP '^[0-9]+$'",ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['subscriptions']=$count[0]['count'];
			}else{
				$return['subscriptions']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		if($sales){
			global $wpdb;
			$count = $wpdb->get_results($wpdb->prepare("SELECT sum(meta_value) as count FROM {$wpdb->postmeta} WHERE meta_key = %s",'_order_total'),ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['sales']=$count[0]['count'];
			}else{
				$return['sales']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		if($commissions){
			global $wpdb;
			$table_name = $wpdb->prefix.'woocommerce_order_itemmeta';
			$q=$wpdb->prepare("SELECT sum(meta_value) as count FROM {$table_name} WHERE meta_key LIKE %s",'commission%');
			$count = $wpdb->get_results($q,ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['commissions']=$count[0]['count'];
			}else{
				$return['commissions']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		if($posts){
			global $wpdb;
			$count = $wpdb->get_results($wpdb->prepare("SELECT count(*) as count FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s",'post','publish'),ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['posts']=$count[0]['count'];
			}else{
				$return['posts']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		if($comments){
			global $wpdb;
			$count = $wpdb->get_results($wpdb->prepare("SELECT count(*) as count FROM {$wpdb->comments} WHERE comment_approved = %d AND comment_post_ID IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s)",1,'post','publish'),ARRAY_A);
			if(is_array($count) && isset($count[0]['count']) && is_numeric($count[0]['count'])){
				$return['comments']=$count[0]['count'];
			}else{
				$return['comments']=0;
			}
			if($number)
				return $count[0]['count'];
		}
		$return_html='';
		if(is_array($return) && count($return)){
			$return_html='<ul class="site_stats">';
			foreach($return as $key=>$value){
				if($value){
					switch($key){
						case 'total':
							$return_html .='<li>'.__('MEMBERS','vibe-shortcodes').'<span>'.$value.'</span></li>';
						break;
						case 'courses':
						$return_html .='<li>'.__('COURSES','vibe-shortcodes').'<span>'.$value.'</span></li>';
						break;
						case 'instructor':
						$return_html .='<li>'.__('INSTRUCTORS','vibe-shortcodes').'<span>'.$value.'</span></li>';
						break;
						case 'groups':
						$return_html .='<li>'.__('GROUPS','vibe-shortcodes').'<span>'.$value.'</span></li>';
						break;
						case 'subscriptions':
						$return_html .='<li>'.__('SUBSCRIPTIONS','vibe-shortcodes').'<span>'.$value.'</span></li>';
						break;
						case 'sales':
						$return_html .='<li>'.__('SALES','vibe-shortcodes').'<span>'.get_woocommerce_currency_symbol("USD").$value.'</span></li>';
						break;
						case 'commissions':
						$return_html .='<li>'.__('EARNINGS','vibe-shortcodes').'<span>'.get_woocommerce_currency_symbol("USD").$value.'</span></li>';
						break;
					}
				}
			}
			$return_html .= '</ul>';
		}
		return $return_html;
	}

    


/*-----------------------------------------------------------------------------------*/
/*	Question
/*-----------------------------------------------------------------------------------*/

	function vibe_question( $atts, $content = null ) {
            extract(shortcode_atts(array(
			'id'   => '',
            ), $atts));
            
            if(!is_numeric($id))
            	return '';


    		$question = new WP_Query(array('p'=>$id,'post_type'=>'question'));

            if($question->have_posts()){
                while($question->have_posts()){
                    $question->the_post();
                    global $post;
                    $hint = get_post_meta($id,'vibe_question_hint',true);
                    $type = get_post_meta($id,'vibe_question_type',true);
                    $return ='<div class="question '.$type.'">';
                    $return .='<div class="question_content">'.apply_filters('the_content',$post->post_content);
                    if(isset($hint) && strlen($hint)>5){
                        $return .='<a class="show_hint tip" tip="'.__('SHOW HINT','vibe-shortcodes').'"><span></span></a>';
                        $return .='<div class="hint"><i>'.__('HINT','vibe-shortcodes').' : '.apply_filters('the_content',$hint).'</i></div>';
                    }
                    $return .='</div>';
                    switch($type){
                        case 'truefalse': 
                        case 'single': 
                        case 'multiple': 
                        case 'sort':
                        case 'match':
                           $options = vibe_sanitize(get_post_meta($id,'vibe_question_options',false));

                          if($type == 'truefalse')
                            $options = array( 0 => __('FALSE','vibe-shortcodes'),1 =>__('TRUE','vibe-shortcodes'));

                          if(isset($options) || $options){
                        
                            $return .= '<ul class="question_options '.$type.'">';
                              if($type=='single'){
                                foreach($options as $key=>$value){
                                  $return .= '<li>
                                            <div class="radio">
                                              <input type="radio" id="'.$post->post_name.$key.'" class="ques'.$id.'" name="'.$id.'" value="'.($key+1).'" />
                                              <label for="'.$post->post_name.$key.'"><span></span> '.do_shortcode($value).'</label>
                                            </div>
                                        </li>';
                                }
                              }else if($type == 'sort'){
                                foreach($options as $key=>$value){
                                  $return .= '<li id="'.($key+1).'" class="ques'.$post->ID.' sort_option">
                                              <label for="'.$post->post_name.$key.'"><span></span> '.do_shortcode($value).'</label>
                                          </li>';
                                }        
                              }else if($type == 'match'){
                                foreach($options as $key=>$value){
                                  $return .= '<li id="'.($key+1).'" class="ques'.$post->ID.' match_option">
                                              <label for="'.$post->post_name.$key.'"><span></span> '.do_shortcode($value).'</label>
                                          </li>';
                                }        
                              }else if($type == 'truefalse'){
                                foreach($options as $key=>$value){
                                  $return .= '<li>
                                            <div class="radio">    
                                            <input type="radio" id="'.$post->post_name.$key.'" class="ques'.$post->ID.'" name="'.$post->ID.'" value="'.$key.'" />
                                            <label for="'.$post->post_name.$key.'"><span></span> '.$value.'</label>
                                            </div>
                                        </li>';
                                }       
                              }else{
                                foreach($options as $key=>$value){
                                  $return .= '<li>
                                            <div class="checkbox">
                                            <input type="checkbox" class="ques'.$post->ID.'" id="'.$post->post_name.$key.'" name="'.$post->ID.$key.'" value="'.($key+1).'" />
                                            <label for="'.$post->post_name.$key.'"><span></span> '.do_shortcode($value).'</label>
                                            </div>
                                        </li>';
                                }
                              }  
                            $return .= '</ul>';
                          }
                        break; // End Options
                        case 'fillblank': 
                        break;
                        case 'select': 
                        break;
                        case 'smalltext': 
                          $return .= '<input type="text" name="'.$k.'" class="ques'.$k.' form_field" value="" placeholder="'.__('Type Answer','vibe-shortcodes').'" />';
                        break;
                        case 'largetext': 
                          $return .= '<textarea name="'.$k.'" class="ques'.$k.' form_field" placeholder="'.__('Type Answer','vibe-shortcodes').'"></textarea>';
                        break;
                      }
                      $return .='<ul class="check_options">';
                      
                      
                        $answer = get_post_meta($id,'vibe_question_answer',true);
                        if(isset($answer) && strlen($answer) && in_array($type,array('single','multiple','truefalse','sort','match','smalltext','select','fillblank'))){
                            $return .='<li><a class="check_answer" data-id="'.$id.'">'.__('Check Answer','vibe-shortcodes').'</a></li>';        
                            $ans_json = array('type' => $type);
                            if(in_array($type,array('multiple'))){
                                $ans_array =  explode(',',$answer);
                                $ans_json['answer'] = $ans_array;
                            }else{
                                $ans_json['answer'] = $answer; 
                        }
                        echo '<script>
                            var ans_json'.$id.'= '.json_encode($ans_json).';
                         </script>';
                      }

                      $explaination = get_post_meta($id,'vibe_question_explaination',true);
                      if(isset($explaination) && strlen($explaination)>2){
                        $return .= '<li><a href="#question_explaination'.$id.'" class="open_popup_link">'.__('Explanation','vibe-shortcodes').'</a></li>';
                      
                        echo '<div id="question_explaination'.$id.'" class="white-popup mfp-hide">
                          '.do_shortcode($explaination).'
                          </div>';
                      }

                    $return .='</ul></div>';
                }
            }
            wp_reset_postdata();	    	
            
        return $return;
	}
    
/*-----------------------------------------------------------------------------------*/
/*  Certificates
/*-----------------------------------------------------------------------------------*/

    function vibe_show_certificates( $atts, $content = null ) {

        extract(shortcode_atts(array(
            'number'=>9,
            'size'=> 'medium',
            'columns'=>3,
            'course' => '',
            'user' => '',
        ), $atts));

        $args = array('post_type'=>'attachment','post_status'=>'inherit','post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png','posts_per_page'=>9);
        if(!empty($user)){
            if($user == 'current' && is_user_logged_in()){$user = get_current_user_id();}
            $args['author'] = $user;
        }

        if(is_numeric($course)){
            $type = get_post_field('post_type',$course);
            if($type == 'course'){
                 $args['post_parent'] = $course;
            }
        }

        $attachments = new WP_Query($args);

        $ids = array();
        if($attachments->have_posts()){
            while(($attachments->have_posts())){
                $attachments->the_post();
                global $post;
                if(strpos($post->post_name, 'certificate_') !== false){
                    $ids[] = get_the_id();
                }
            }
        }
        wp_reset_postdata();
        if(!empty($ids)){
            $ids = implode(',',$ids);
            return do_shortcode('[gallery size="'.$size.'" columns="'.$columns.'" ids="'.$ids.'"]');    
        }else{
            return;
        }
    }

/*-----------------------------------------------------------------------------------*/
/*	Course Search box
/*-----------------------------------------------------------------------------------*/


	function vibe_course_search( $atts, $content = null ) {
            extract(shortcode_atts(array(
		'style'   => 'left'
                ), $atts));
        
        $html ='<form role="search" method="get" class="'.$style.'" action="'.home_url( '/' ).'">
		     			<input type="hidden" name="post_type" value="'.BP_COURSE_SLUG.'" />
		     			<input type="text" value="'.(isset($_GET['s'])?$_GET['s']:'').'" name="s" id="s" placeholder="'.__('Type Keywords..','vibe-shortcodes').'" />
					    <input type="submit" id="searchsubmit" value="'.__('Search','vibe-shortcodes').'" />
                        </form>';

        return $html;
	}

/*-----------------------------------------------------------------------------------*/
/*	Pass Fail shortcodes
/*-----------------------------------------------------------------------------------*/

	function vibe_pass_fail( $atts, $content = null ) {
            extract(shortcode_atts(array(
					'id'   => '',
					'key'   => '',
					'passing_score'   => '',
					'pass'=>0,
					'fail'=>0
                ), $atts));
        
        if(!is_numeric($id)){ 
        	return;
        }
        if(!isset($key) || !$key){
        	$key = get_current_user_id();
        }
        if(!isset($passing_score) || !$passing_score){
        	$post_type=get_post_type($id);
        	if($post_type == 'course'){
        		$passing_score = get_post_meta($id,'vibe_course_passing_percentage',true);
        	}else if($post_type == 'quiz'){
        		$passing_score = get_post_meta($id,'vibe_quiz_passing_score',true);
        	}else
        		return;
        }
        $score = apply_filters('wplms_pass_fail_shortcode',get_post_meta($id,$key,true));

        if($pass && $score >=$passing_score){ 
        	return apply_filters('the_content',$content);
        }

        if($fail && $score < $passing_score){
        	return apply_filters('the_content',$content);
        }
        
        return $return;
	}

    /*-----------------------------------------------------------------------------------*/
    /*   WPLMS REGISTRATION FORMS
    /*-----------------------------------------------------------------------------------*/

    function survey_result($atts, $content = null){
        extract(shortcode_atts(array(
                    'id'=>'',
                    'user_id'=>'',
                    'lessthan'   => '0',
                    'greaterthan'=>'100'
                ), $atts));
        if(empty($id)){
            global $post;
            if($post->post_type == 'quiz')
                $id = $post->ID;
            else if(isset($_GET['action']) && is_numeric($_GET['action'])){
                $post_type = get_post_type($_GET['action']);
                if($post_type == 'quiz')
                    $id = $post->ID;
            }
        }
        if(!is_numeric($id)){ 
            return;
        }
        if(!isset($user_id) || !$user_id){
            $user_id = get_current_user_id();
        }
        $score = apply_filters('wplms_survey_result_shortcode',get_post_meta($id,$user_id,true));
        
        if(isset($greaterthan)){ 
            if($score >=$greaterthan){
                if(isset($lessthan)){
                    if($score <= $lessthan){
                        return apply_filters('the_content',$content);
                    }
                }else{
                    return apply_filters('the_content',$content);
                }
            }
        }else if(isset($lessthan)){
            if($score <= $lessthan){
                return apply_filters('the_content',$content);
            }
        }

    }
    /*-----------------------------------------------------------------------------------*/
    /*   WPLMS REGISTRATION FORMS
    /*-----------------------------------------------------------------------------------*/	

    function wplms_registration_form($atts, $content = null){
        extract(shortcode_atts(array(
                    'name'   => '',
                    'field_meta'=>0
                ), $atts));

        if(empty($name) && function_exists('xprofile_get_field'))
            return;

        $forms = get_option('wplms_registration_forms');
        if(!empty($forms[$name])){
            $fields = $forms[$name]['fields'];
            $settings = $forms[$name]['settings'];
            /*
            STANDARD FIELDS
            */
          
            $return = '<div class="wplms_registration_form"><form action="/" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

            <ul>';
            if(empty($settings['hide_username'])){
                $return .='<li>'.'<label>'.__('Username','vibe-shortcodes').'</label>'.'<input type="text" name="signup_username" placeholder="'.__('Login Username','vibe-shortcodes').'" required></li>';
            }

            $return .='<li>'.'<label>'.__('Email','vibe-shortcodes').'</label>'.'<input type="email" name="signup_email" placeholder="'.__('Email','vibe-shortcodes').'" required></li>';

            $return .='<li>'.'<label '.(empty($settings['password_meter'])?:'for="signup_password"').'>'.__('Password','vibe-shortcodes').'</label>'.'<input type="password" '.(empty($settings['password_meter'])?'':'id="signup_password" class="form_field"').' name="signup_password" placeholder="'.__('Password','vibe-shortcodes').'"></li>';

            if ( bp_is_active( 'xprofile' ) ) : 
                if ( bp_has_profile( array( 'fetch_field_data' => false ) ) ) : 
                    while ( bp_profile_groups() ) : bp_the_profile_group(); 

                        $return_fields = $return_heading = '';
                        if(!empty($settings['show_group_label'])){
                            $return_heading .= '</ul><h3 class="heading"><span>'.bp_get_the_profile_group_name();
                            $return_heading .= '</span></h3><p>'.do_shortcode(bp_get_the_profile_group_description()).'</p><ul>';

                        }

                        while ( bp_profile_fields() ) : bp_the_profile_field();
                        global $field;
                        $fname = str_replace(' ','_',$field->name);
                        if(is_array($fields) && in_array($fname,$fields)){


                            $return_fields .='<li>';
                            $field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
                            ob_start();
                            ?><div<?php bp_field_css_class( 'bp-profile-field' ); ?>>
                            <?php
                            $field_type->edit_field_html();

                            if(!empty($field_meta)){

                                if ( bp_get_the_profile_field_description() ) : ?>
                                <p class="description"><?php bp_the_profile_field_description(); ?></p>
                                <?php endif;

                                    do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

                                    $can_change_visibility = bp_current_user_can( 'bp_xprofile_change_field_visibility' );?>

                                    <p class="field-visibility-settings-<?php echo $can_change_visibility ? 'toggle' : 'notoggle'; ?>" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id(); ?>">

                                    <?php
                                    printf(
                                        __( 'This field can be seen by: %s', 'buddypress' ),
                                        '<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
                                    );
                                    ?>

                                    <?php if ( $can_change_visibility ) : ?>

                                        <a href="#" class="link visibility-toggle-link"><?php esc_html_e( 'Change', 'buddypress' ); ?></a>

                                    <?php endif; ?>
                                    </p>
                                    <?php if ( $can_change_visibility ) : ?>

                                        <div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
                                            <fieldset>
                                                <legend><?php _e( 'Who can see this field?', 'buddypress' ); ?></legend>

                                                <?php bp_profile_visibility_radio_buttons(); ?>

                                            </fieldset>
                                            <a class="link field-visibility-settings-close" href="#"><?php esc_html_e( 'Close', 'buddypress' ); ?></a>
                                        </div>

                                    <?php endif; ?>
                                </div>
                            <?php
                            }
                            $check = ob_get_clean();

                            do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

                            $can_change_visibility = bp_current_user_can( 'bp_xprofile_change_field_visibility' );
                            $return_fields .= $check;

                            $return_fields .='</li>';
                        }
                        endwhile;
                        if(!empty($return_fields)){
                            $return .= $return_heading;
                        }
                        $return .= $return_fields;
                    endwhile;
                endif;
            endif; 
           
            
            $form_settings = apply_filters('wplms_registration_form_settings',array(
                    'hide_username' =>  __('Auto generate username from email','vibe-customtypes'),
                    'password_meter' =>  __('Show password meter','vibe-customtypes'),
                    'show_group_label' =>  __('Show Field group labels','vibe-customtypes'),
                    'google_captcha' => __('Google Captcha','vibe-customtypes'),
                    'auto_login'=> __('Register & Login simultaneously','vibe-customtypes'),
                    'skip_mail' =>  __('Skip Mail verification','vibe-customtypes'),
                    'default_role'=>'',
            ));
           
            foreach($form_settings as $key=>$setting){
                if(!empty($settings[$key])){
                    if(!empty($settings['google_captcha']) && function_exists('vibe_get_option') && $key == 'google_captcha'){
                        require_once('classes/recaptchalib.php');
                        $google_captcha_public_key = vibe_get_option('google_captcha_public_key');
                        if ( ! wp_script_is( 'google-recaptcha', 'enqueued' ) ) {
                            wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js' );    
                        }

                        $return .= '<li><div class="g-recaptcha" data-theme="clean" data-sitekey="'.$google_captcha_public_key.'"></div></li>';
                    }
                    $return .= '<input type="hidden" name="'.$key.'" value="'.$settings[$key].'"/>';
                }
            }

            do_action('wplms_before_registration_form',$name);

            $return .='<li>'.apply_filters('wplms_registration_form_submit_button','<a class="submit_registration_form button">'.__('Register','vibe-shortcodes').'</a>').'</li>';
            //SETTINGS
            
            ob_start();
            wp_nonce_field( 'bp_new_signup' );
            $return .= ob_get_clean();
            $return .= '</ul></form></div>';
        }
        return $return;
    }
    /*
    REGISTRATION FROM DEFAULT ROLE
     */
    function activate_user($user_id,$key,$user){
        $default_role = get_user_meta($user_id,'default_role',true);
        if(!empty($default_role)){
            wp_update_user(array('ID'=>$user_id,'role'=>$default_role));
            $new_user = new WP_User( $user_id );
            $new_user->add_role( $default_role );
        }
    }
}

new Vibe_Define_Shortcodes;

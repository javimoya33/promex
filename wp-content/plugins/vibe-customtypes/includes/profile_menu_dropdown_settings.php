<?php
//Authors : Vibethemes / Skywalker 
 if ( ! defined( 'ABSPATH' ) ) exit;

 class WPLMS_Profile_Menu_Dropdown_Settings{

 	protected $option = 'profile_menu_dropdown_settings';
 	public $profile_menu_dropdown_labels = array();

	public static $instance;
    
    public static function init(){
        if ( is_null( self::$instance ) )
            self::$instance = new WPLMS_Profile_Menu_Dropdown_Settings();
        return self::$instance;
    }

    public function __construct(){
    	/*
    	ADD NEW TAB IN LMS - SETTINGS
    	*/
    	add_filter('wplms_lms_commission_tabs',array($this,'add_profile_menu_dropdown_settings'));
    	// Tab Handle function
    	add_filter('lms_general_settings',array($this,'handle_profile_menu_dropdown_settings'),999991);
    	add_filter('wplms_logged_in_top_menu',array($this,'apply_profile_menu_dropdown_settings'),9999);
    }
    
    function apply_profile_menu_dropdown_settings($settings){
    	
    	$option = $this->get();

    	if(empty($option))
    		return $settings;

    	$option = $option['profile_menu_dropdown_settings'];
    	$profile_menu_dropdown_labels = $this->get_profile_menu_dropdown_labels();

    		$new_settings = array();
    		$profile_menu_details = $option['value'];
    		$detail_privacy = $option['privacy'];
			$drop_down_settings = array();

    		foreach($profile_menu_details as $key => $value){	
    			if($this->check_privacy($detail_privacy[$key])){

    				$option['link'][$key]=preg_replace("/{{siteurl}}/",home_url(), $option['link'][$key]);
    				$option['link'][$key]=preg_replace("/{{userprofile}}/",bp_loggedin_user_domain(), $option['link'][$key]);
    				//regex
    				$drop_down_settings[$value]=array(
			              'icon' => $option['icon'][$key],
			              'label' => $option['label'][$key],
			              'link' => $option['link'][$key],
			              );
	    		}
	    	}
    	return $drop_down_settings;
   	}

    function check_privacy($privacy){

    	$privacy_options = $this->get_privacy_options();

    	$return = false;
    	if(function_exists('bp_course_version')){
    		$version = bp_course_version();	
    	}else{
    		return true;
    	}
    	
    	switch($privacy){

    		case 'all':
    			return true;
    		break;
    		case 'students':
    			if( is_user_logged_in() && !current_user_can('edit_post'))
    				return true;
    		break;
    		case 'instructors':
    			if(is_user_logged_in() && current_user_can('edit_posts'))
    				return true;
    		break;
    		case 'admins':
    			if(is_user_logged_in() && current_user_can('manage_options'))
    				return true;
    		break;
    		default:
    			$return = apply_filters('wplms_course_details_check_privacy',false,$privacy);
    		break;
    	}

    	return $return;
    }

    function add_profile_menu_dropdown_settings($settings){
    	$settings['profile_menu_dropdown_settings'] = _x('Profile Menu Dropdown Settings','','vibe-customtypes');
    	return $settings;
    }

    function handle_profile_menu_dropdown_settings($settings){
    	
    	if(!isset($_GET['sub']) || $_GET['sub'] != 'profile_menu_dropdown_settings')
    		return $settings;
    	
    	$settings = array();
    	$profile_menu_dropdown_settings = apply_filters('profile_menu_dropdown_settings_settings',array(
    			array(
					'label'=>__('Profile Menu Dropdown','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
					'label'=>__('Manage Profile Menu Dropdown','vibe-customtypes' ),
					'type'=> 'profile_menu_dropdown_settings',
				),
    		));

    	$this->handle_save();
    	$this->generate_form($profile_menu_dropdown_settings);

    	return $settings;
    }

    function handle_save(){

		if(!isset($_POST['save_profile_menu_dropdown_settings']) || !wp_verify_nonce($_POST['_wpnonce'],'profile_menu_dropdown_settings')){return;}
    	if(isset($_POST['profile_menu_dropdown_settings'])){
    		$option = $this->get();
			$option['profile_menu_dropdown_settings']=$_POST['profile_menu_dropdown_settings'];
    		$this->put($option);
    	}else{
    		$this->put(array());
    	}
    }
    function generate_form($settings){

    	$option = $this->get();
    	$icons = $this->get_icons();
    	$links = $this->get_links();
    	$profile_menu_dropdown_labels = $this->get_profile_menu_dropdown_labels();
    	echo '<form method="post">';
		wp_nonce_field('profile_menu_dropdown_settings','_wpnonce');   
		echo '<table class="form-table">
				<tbody>';	
		foreach($settings as $setting ){
			echo '<tr valign="top" '.(empty($setting['class'])?'':'class="'.$setting['class'].'"').'>';
			switch($setting['type']){
				case 'heading':
					echo '<th scope="row" class="titledesc" colspan="2"><h3>'.$setting['label'].'</h3></th>';
				break;
				case 'profile_menu_dropdown_settings':
					echo '<td>';
					if(empty($option)){$option = array();}
					if(empty($option['profile_menu_dropdown_settings'])){

						$option['profile_menu_dropdown_settings'] = $more_details = array();

						foreach($profile_menu_dropdown_labels as $k=>$v){
								if(empty($v['callback'])){

									$option['profile_menu_dropdown_settings']['value'][] = $k;
									$option['profile_menu_dropdown_settings']['privacy'][] = 'all';
									$option['profile_menu_dropdown_settings']['label'][] = $k;
									$option['profile_menu_dropdown_settings']['icon'] = $icons;

									$option['profile_menu_dropdown_settings']['link'] = array();
									if(defined('WPLMS_DASHBOARD_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.WPLMS_DASHBOARD_SLUG;
									}

									if(defined('BP_COURSE_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_COURSE_SLUG.'/';
									}

									if(defined('BP_COURSE_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_COURSE_SLUG.'/'.BP_COURSE_STATS_SLUG;
									}

									if(defined('BP_SETTINGS_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_SETTINGS_SLUG;
									}
									if(defined('BP_MESSAGES_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_MESSAGES_SLUG;
									}
									if(defined('BP_NOTIFICATIONS_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_NOTIFICATIONS_SLUG;
									}
									if(defined('BP_GROUPS_SLUG')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.BP_GROUPS_SLUG;
									}
									if(defined('wishlist_slug')){
										$option['profile_menu_dropdown_settings']['link'][] = '{{userprofile}}'.wishlist_slug;
									}
									
								}
						}
					}else{
						foreach($profile_menu_dropdown_labels as $k=>$v){
								if(!in_array($k,$option['profile_menu_dropdown_settings']['value'])){
									$more_details[$k] = $v;
								}
							}
					}	

					$privacy_options = $this->get_privacy_options();
					echo '<input type="submit" name="add_profile_menu_dropdown_detail" class="button" value="'._x('Add New Detail','Adds a new course detail in the course details widget','vibe-customtypes').'" />';
					if(isset($option['profile_menu_dropdown_settings'])){

						echo '<ul class="course_details_list">';
						$profile_menu_details = $option['profile_menu_dropdown_settings'];
						$details = $option['profile_menu_dropdown_settings']['value'];

						foreach($details as $k => $detail){
							echo '<li class="detail_list"><span class="dashicons dashicons-menu"></span> &nbsp; <label> ';
							echo ' <span style=" display: inline-block;width: 30%;">'.$option['profile_menu_dropdown_settings']['label'][$k].'</span>'.'<span style=" display: inline-block;width: 30%;">['.(isset($profile_menu_details['privacy'][$k])?$privacy_options[$profile_menu_details['privacy'][$k]]:$privacy_options['all']).' ] </span><i class="'.(isset($option['profile_menu_dropdown_settings']['icon'][$k])?$option['profile_menu_dropdown_settings']['icon'][$k]:$option['profile_menu_dropdown_settings']['icon'][$detail]).'" style=" display: inline-block;width: 20%"></i>'.'</label>
							<input type="hidden" name="profile_menu_dropdown_settings[value][]" value="'.$detail.'" />
							<input type="hidden" name="profile_menu_dropdown_settings[privacy][]" value="'.(isset($profile_menu_details['privacy'][$k])?$profile_menu_details['privacy'][$k]:'all').'" />
							<input type="hidden" name="profile_menu_dropdown_settings[link][]" value="'.((isset($option['profile_menu_dropdown_settings']['link'][$k]))?$option['profile_menu_dropdown_settings']['link'][$k]:'#').'" />
							<input type="hidden" name="profile_menu_dropdown_settings[label][]" value="'.((isset($option['profile_menu_dropdown_settings']['label'][$k]))?$option['profile_menu_dropdown_settings']['label'][$k]:'').'" />
							<input type="hidden" name="profile_menu_dropdown_settings[icon][]" value="'.((empty($option['profile_menu_dropdown_settings']['icon'][$k]))?(isset($option['profile_menu_dropdown_settings']['icon'][$detail])?$option['profile_menu_dropdown_settings']['icon'][$detail]:''):$option['profile_menu_dropdown_settings']['icon'][$k]).'" />
							    <span class="dashicons dashicons-no"></span></li>';
						}

						/*
						USer Clicked on MOre/Custom Detail
						*/
						if(isset($_POST['add_profile_menu_dropdown_detail'])){

							echo '<li class="detail_list"><span class="dashicons dashicons-menu"></span> &nbsp; ';
							echo '<span class="dashicons dashicons-no"></span>';
							echo'<ul class="custom_ul">';
							echo'<li>';
							echo '<label class="label-with-width">'._x('Option Label','label in settings','vibe-customtypes').'</label><select id="set_custom_menu_option" name="profile_menu_dropdown_settings[value][]">';

							echo '<option value="settings">'._x('Select a user profile menu','label in select profile settings','vibe-customtypes').'</option>';
							foreach($more_details as $key=>$detail){
								echo '<option value="'.$key.'">'.$detail['label'].'</option>';
							}
							echo '<option value="custom">'._x('Custom','label in select profile settings','vibe-customtypes').'</option>';
							echo '</select>';
							echo'</li>';
							//icon select dropdown
							echo'<li>';
							echo '<label class="label-with-width">'._x('Choose Icon','choose icon label in course profile settings','vibe-customtypes').'<a style="color:black; text-decoration:none;" href="http://vibethemes.com/documentation/wplms/knowledge-base/customise-profile-menu-dropdown-wplms-2-6/"><span class="dashicons  dashicons-editor-help"></span></a></label><select id="icon_set" name="profile_menu_dropdown_settings[icon][]">';
							foreach($icons as $key=>$detail){
								echo '<option value="'.$detail.'">'.$detail.'</option>';
							}
							echo '<option value="custom">'._x('Custom','custom link label in select','vibe-customtypes').'</option>';
							echo '</select>';
							echo '<span class="show_icons"><i id="show_icon" class="icon-settings"></i></span>';
							echo'</li>';
							//label select dropdown
							echo'<li>';
							echo '<label class="label-with-width">'._x('Name','label in select profile menu details','vibe-customtypes').'</label><select id="label_set" name="profile_menu_dropdown_settings[label][]">';
							echo '<option value="settings">'._x('Choose option','label in select dropdown in profile menu details','vibe-customtypes').'</option>';
							foreach($more_details as $key=>$detail){
								echo '<option value="'.$key.'">'.$detail['label'].'</option>';
							}
							echo '<option value="custom">'._x('Custom','custom link label in select','vibe-customtypes').'</option>';
							echo '</select>';
							echo'</li>';
							//link select dropdown
							echo'<li>';
							echo '<label class="label-with-width">'._x('Choose Link','label in select dropdown','vibe-customtypes').'<a style="color:black; text-decoration:none;" href="http://vibethemes.com/documentation/wplms/knowledge-base/customise-profile-menu-dropdown-wplms-2-6/"><span class="dashicons  dashicons-editor-help"></span></a></label><select id="link_set" name="profile_menu_dropdown_settings[link][]">';
							foreach($links as $key=>$detail){
								echo '<option value="'.$key.'">'.$detail.'</option>';
							}
							echo '<option value="custom">'._x('Custom','custom link label in select','vibe-customtypes').'</option>';
							echo '</select>';
							echo'</li>';
							//privacy select dropdown
							echo'<li>';
							echo '<label class="label-with-width">'._x('Privacy','custom link label in select','vibe-customtypes').'</label><select name="profile_menu_dropdown_settings[privacy][]" class="privacy_set" >';
							if(!empty($privacy_options)){
								foreach($privacy_options as $p=>$o){
									echo '<option value="'.$p.'">'.$o.'</option>';
								}
							}
							echo '</select>';
							echo'</li></li>';
							
						}
						echo'</ul>';
						echo '</ul>';
					}
					echo '</td>';
				break;
			}
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table><style>span.dashicons.dashicons-editor-help {margin-left:10px }ul.custom_ul li select{width:30% !important;}label.label-with-width{display:inline-block; width:120px;}span.showicons{margin-top:20px;}#show_icon{margin-left:20px;}ul.custom_ul {margin-left:30px !important;}ul.custom_ul li {margin-top:20px;margin-bottom:20px;} input.link_set {margin-left:82px} .detail_list{border:1px solid #eee; padding:8px 15px;background:#fff;max-width:80%;min-width:240px;}.detail_list .dashicons-no{float:right;color:red;} .button.save{background: #E8442F; text-shadow: none; box-shadow: none; border: none;}input.custom_icon { padding-top: 1px !important; padding-left: 5px !important; padding-bottom: 6px !important;} input.custom_value { margin-left: 20px !important; padding-top: 1px !important; padding-left: 5px !important; padding-bottom: 6px !important;} input.custom_label { margin-left: 20px !important; padding-top: 1px !important; padding-left: 5px !important; padding-bottom: 6px !important;}input.custom_label {width:300px} input.custom_value {width:300px} input.custom_icon{width:300px}</style>
			<script>
			jQuery(document).ready(function($){
				$(".course_details_list").sortable({
					"handle":".dashicons-menu",
					 axis: "y"
				});
				$("#set_custom_menu_option").on("change",function(){
					var option = $(this).find("option:selected").val();
					if(option == "custom"){
						console.log("has");
						$(this).attr("name","");
						$(this).parent().append(\'<input type="text" required name="profile_menu_dropdown_settings[value][]" class="custom_value" placeholder="Custom Label">\');
					}
					else{
						$(this).parent().find("input.custom_value").remove();
						$(this).attr("name","profile_menu_dropdown_settings[value][]");
					}
				});
				$("#icon_set").on("change",function(){
					var option = $(this).find("option:selected").val();
					$("i#show_icon").removeClass().addClass(option);
					if(option == "custom"){
						console.log("has");
						$(this).attr("name","");
						$(this).parent().find("span.show_icons").append(\'<input type="text" required class="custom_icon" name="profile_menu_dropdown_settings[icon][]" placeholder="Icon Class">\');
					}
					else{						
						$(this).parent().find("input.custom_icon").remove();
						$(this).attr("name","profile_menu_dropdown_settings[icon][]");
					}
				});
				$("#label_set").on("change",function(){
					var option = $(this).find("option:selected").val();
					if(option == "custom"){
						console.log("has");
						$(this).attr("name","");
						$(this).parent().append(\'<input type="text" required name="profile_menu_dropdown_settings[label][]" class="custom_label" placeholder="Custom Name to be shown on the frontend">\');
					}
					else{						
						$(this).parent().find("input.custom_label").remove();
						$(this).attr("name","profile_menu_dropdown_settings[label][]");
					}
				});
				$("#link_set").on("change",function(){
					var option = $(this).find("option:selected").val();
					if(option == "custom"){
						console.log("has");
						$(this).attr("name","");
						$(this).parent().append(\'<input type="text" required name="profile_menu_dropdown_settings[link][]" class="custom_label" placeholder="Custom Link {{siteurl}}{{userprofile}}">\');
					}
					else{						
						$(this).parent().find("input.custom_label").remove();
						$(this).attr("name","profile_menu_dropdown_settings[link][]");
					}
				});
				$(".dashicons-no").on("click",function(){
					$(this).parent().remove();
					$("input[name=\'save_profile_menu_dropdown_settings\']").addClass("save");
					return false;
				});
			});
			</script>';	
		if(!empty($settings))
			echo '<input type="submit" name="save_profile_menu_dropdown_settings" value="'.__('Save Settings','vibe-customtypes').'" class="button button-primary" /></form>';	
    }

    function get_profile_menu_dropdown_labels(){

    	$profile_menu_dropdown_labels = array();

    	if ( (in_array( 'wplms-dashboard/wplms-dashboard.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || (function_exists('is_plugin_active') && is_plugin_active( 'wplms-dashboard/wplms-dashboard.php')) )) {	
			$profile_menu_dropdown_labels['dashboard'] = array(
			'label'=>_x('Dashboard','label in details array','vibe-customtypes'),
			'callback'=> false,
			'icon' => 'icon-meter',
            'link' => bp_loggedin_user_domain().'WPLMS_DASHBOARD_SLUG' 
			);
		}

		$profile_menu_dropdown_labels['courses'] =  array(
				'label'=>_x('My Courses','label in details array','vibe-customtypes'),
				'callback'=> false,
				'icon' => 'icon-book-open-1',
	            'link' => bp_loggedin_user_domain().BP_COURSE_SLUG
			);

		$profile_menu_dropdown_labels['stats'] = array(
				'label'=>_x('Statistics','label in details array','vibe-customtypes'),					
	            'callback'=> false,
	            'icon' => 'icon-analytics-chart-graph',
	            'link' => bp_loggedin_user_domain().BP_COURSE_SLUG.'/'.BP_COURSE_STATS_SLUG
				);


		if(function_exists('bp_is_active') && bp_is_active('notifications')){
			$profile_menu_dropdown_labels['notifications'] = array(
						'label'=>_x('Notifications','label in details array','vibe-customtypes'),
						'callback'=> false,
						'icon' => 'icon-exclamation',
			            'link' => bp_loggedin_user_domain().BP_NOTIFICATIONS_SLUG 
						);
		}

		if(function_exists('bp_is_active') && bp_is_active('groups')){
				$profile_menu_dropdown_labels['groups'] = array(
						'label'=>_x('Groups','label in details array','vibe-customtypes'),
						'callback'=> false,
						'icon' => 'icon-myspace-alt',
			            'link' => bp_loggedin_user_domain().BP_GROUPS_SLUG 
						);
		}


		$profile_menu_dropdown_labels['settings'] = array(
	            'label' => __('Settings','vibe-customtypes'),
				'callback'=> false,
				'icon' => 'icon-settings',
	            'link' => bp_loggedin_user_domain().BP_SETTINGS_SLUG 
				);

		if(function_exists('bp_is_active') && bp_is_active('messages')){
			$profile_menu_dropdown_labels['inbox'] = array(
						'label'=>_x('Inbox','label in details array','vibe-customtypes'),
						'callback'=> false,
						'icon' => 'icon-letter-mail-1',
			            'link' => bp_loggedin_user_domain().BP_MESSAGES_SLUG
						);
					
		}

		if(class_exists('Wplms_Wishlist_Component')){

			$profile_menu_dropdown_labels['wishlist'] = array(
				'label'=>_x('Wishlist','label in details array','vibe-customtypes'),
				'callback'=> false,
				'icon' => 'icon-heart',
	            'link' => bp_loggedin_user_domain().'wishlist_slug' 
			);
		}

		
		$profile_menu_dropdown_labels = apply_filters('wplms_profile_menu_dropdown_array',$profile_menu_dropdown_labels);

		return $profile_menu_dropdown_labels;
    }
    
    //Function to get privacy Options Starts here
    function get_privacy_options(){
    	$privacy_options = apply_filters('wplms_profile_menu_dropdown_options',array(
						'all'=>_x('All Users','privacy option for profile drop down menu','vibe-customtypes'),
						'students'=>_x('Students','privacy option for profile drop down menu','vibe-customtypes'),
						'instructors'=>_x('Instructors','privacy option for profile drop down menu','vibe-customtypes'),
						'admins'=>_x('Administrators','privacy option for profile drop down menu','vibe-customtypes')
						));

    	return $privacy_options;
    }

    //Function to get Default Values Starts here
    function get(){
    	$status = get_option($this->option);
    	return $status;
    }
    //Function to get privacy Options ends  here
    function put($option){
    	$status = update_option($this->option,$option);
    	return $status;
    }
    //Function to get icon list Starts here
    function get_icons(){

    	return array(
    		'dashboard' => 'icon-meter',
    		'courses' => 'icon-book-open-1',
    		'stats' => 'icon-analytics-chart-graph',
    		'inbox' => 'icon-letter-mail-1',
    		'notifications' => 'icon-exclamation',
    		'groups' => 'icon-myspace-alt',
    		'settings' => 'icon-settings',
    		'wishlist' => 'icon-heart'
    		);
   	 	}
   	//Funtion to get icons list ends here

   	//Funtion to get links list starts here
   	function get_links(){

   		$links = array('{{userprofile}}'.BP_SETTINGS_SLUG  => _x('User Settings','User label in LMS - Settings - Profile drop down menu','vibe-customtypes') ,
    		'{{userprofile}}'.BP_COURSE_SLUG.'/'.BP_COURSE_STATS_SLUG => _x('User My Courses','User label in LMS - Settings - Profile drop down menu','vibe-customtypes'),
    		'{{userprofile}}'.BP_COURSE_SLUG.'/'.BP_COURSE_STATS_SLUG => _x('User Course Stats','User label in LMS - Settings - Profile drop down menu','vibe-customtypes'));

			if(defined('WPLMS_DASHBOARD_SLUG')){
				$links['{{userprofile}}'.WPLMS_DASHBOARD_SLUG]= _x('Dashboard','User label in LMS - Settings - Profile drop down menu','vibe-customtypes');
			}

			if(defined('BP_MESSAGES_SLUG')){
				$links['{{userprofile}}'.BP_MESSAGES_SLUG]= _x('User Messages','User label in LMS - Settings - Profile drop down menu','vibe-customtypes');
			}
			if(defined('BP_NOTIFICATIONS_SLUG')){
				$links['{{userprofile}}'.BP_NOTIFICATIONS_SLUG]= _x('User Notifications','User label in LMS - Settings - Profile drop down menu','vibe-customtypes');
			}
			if(defined('BP_GROUPS_SLUG')){
				$links['{{userprofile}}'.BP_GROUPS_SLUG]=_x('User Groups','User label in LMS - Settings - Profile drop down menu','vibe-customtypes');
			}
			if(defined('wishlist_slug')){
				$links['{{userprofile}}'.wishlist_slug]=_x('Wishlist','User label in LMS - Settings - Profile drop down menu','vibe-customtypes');
			}

			return $links;
   	 	}
}
   	//Funtion to get links list ends here
   add_action('wp_loaded',function(){
WPLMS_Profile_Menu_Dropdown_Settings::init();
},99);

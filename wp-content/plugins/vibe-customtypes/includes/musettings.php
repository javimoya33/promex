<?php

 if ( ! defined( 'ABSPATH' ) ) exit;
include_once('commissions/wplms_commissions_class.php');
include_once('class.export.php');
include_once('class.import.php');

class lms_settings{

	var $option; 

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new lms_settings();
        return self::$instance;
    }

	private function __construct(){
		$this->option = 'lms_settings';
	}

	public function vibe_lms_settings() {
	    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		$this->lms_settings_tabs($tab); 
		$this->get_lms_settings($tab);
	}
	public function vibe_lms_tree() {
		$this->lms_tree(); 
	}
	function lms_tree(){
		global $wpdb;
		$args = array(
			'post_type' => 'course',
			'posts_per_page'=>99,
		);
		if(!current_user_can('manage_options')){
			$args['author'] = get_current_user_id();
		}
		$query = new WP_Query($args);
		if($query->have_posts()){
			?><div class="metabox-holder" ><div class="postbox-container" style="width:80%">
				<div class="meta-box-sortables sortable course_list"><?php
			while($query->have_posts()){
				$query->the_post();
				?>
					<div class="coursebox postbox closed" data-id="<?php the_ID(); ?>">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle"><span><?php the_title(); ?></span></h3>
						<div class="inside">
							<div class="main coursedata">
							</div>
						</div>
					</div>	
				<?php
			}
			wp_nonce_field('security','security');
			?></div></div><?php
		}
        wp_reset_postdata();
        ?>
        <script>
        jQuery(document).ready(function($){
        	$('.coursebox').on('click',function(){
        		var course_id = $(this).attr('data-id');
        		var $this = $(this);
        		if($this.hasClass('loaded')){
        			$this.toggleClass('closed');
        		}else{
	                $.ajax({
	                  type: "POST",
	                  url: ajaxurl,
	                  dataType: 'html',
	                  data: { action: 'load_coursetree', 
	                          security: $('#security').val(),
	                          course_id:course_id,
	                        },
	                  cache: false,
	                  success: function (html) {
	                  	$this.find('.coursedata').append(html);
	                    $this.removeClass('closed');
	                    $this.addClass('loaded');
	                  }
	                });        			
        		}
        	});
        });
        </script>
        <?php
	}

	function lms_settings_tabs( $current = 'general' ) {
	    $tabs = apply_filters('wplms_lms_settings_tabs',array( 
	    		'general' => __('General','vibe-customtypes'), 
	    		'commissions' => __('Commissions','vibe-customtypes'), 
	    		'functions' => __('Functions','vibe-customtypes'),
	    		'import-export' => __('Import/Export','vibe-customtypes'),
	    		'touch' => __('Touch Points','vibe-customtypes'),
	    		'emails' => __('Emails','vibe-customtypes'),
	    		'addons' => __('AddOns','vibe-customtypes'),
	    		));
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=lms-settings&tab=$tab'>$name</a>";

	    }
	    echo '</h2>';
	}

	function get_lms_settings($tab){
		if(isset($_POST['save'])){
			echo $this->lms_save_settings($tab);
		}		
		switch($tab){
			case 'commissions':
				$this->lms_commissions();
			break; 
			case 'commission_history':
				$this->lms_commission_history();
			break; 
			case 'instructor':
				$this->lms_instructor_settings();
			break; 
			case 'functions':
				$this->lms_resolve_adhoc_function();
				$this->lms_functions();
			break; 
			case 'import-export':
				$this->lms_import_export();
			break; 
			case 'touch':
				$this->lms_touch_points();
			break; 
			case 'emails':
				$this->lms_emails();
			break; 
			case 'addons':
				$this->lms_addons();
			break; 
			default:
				$function_name = apply_filters('lms_settings_tab',$tab);
				if(!empty($tab) && function_exists($function_name) && $tab != 'general'){
					$function_name();
				}else{
					$this->lms_general_settings();
				}
				
			break;
		}
	}

	function lms_save_settings($tab){
		if ( !empty($_POST) && check_admin_referer('vibe_lms_settings','_wpnonce') ){
			$lms_settings=array();

			$lms_settings = get_option($this->option);	

			unset($_POST['_wpnonce']);
			unset($_POST['_wp_http_referer']);
			unset($_POST['save']);
			switch($tab){
				case 'instructor':
					$lms_settings['instructor'] = $_POST;
				break;
				case 'student':
					$lms_settings['student'] = $_POST;
				break;
				case 'functions':
					$this->lms_functions();
				break;
				case 'touch':
					$lms_settings['touch'] = $_POST;
				break;
				case 'emails':
					switch($_GET['sub']){
						case 'schedule':
							$option = 'schedule';
						break;
						case 'scheduled_emails':
							$option = 'scheduled_emails';
						break;
						default:
							$option = 'email_settings';
						break;
					}
					
					$lms_settings[$option] = $_POST;
				break;
				default:
					$lms_settings['general'] = $_POST;
				break;
			}
			
			update_option($this->option,$lms_settings);

			echo '<div class="updated"><p>'.__('Settings Saved','vibe-customtypes').'</p></div>';
		}else{
			echo '<div class="error"><p>'.__('Unable to Save settings','vibe-customtypes').'</p></div>';
		}
	}


	function lms_general_settings(){
		echo '<h3>'.__('LMS General Settings','vibe-customtypes').'</h3>';
		echo '<p>'.__('Import LMS functions can be managed from here.','vibe-customtypes').'</p>';

		$template_array = apply_filters('wplms_lms_commission_tabs',array(
			''=> __('General Settings','vibe-customtypes'),
			'registration'=> __('Registration Forms','vibe-customtypes'),
			));
		echo '<ul class="subsubsub">';
		foreach($template_array as $k=>$value){
			echo '<li><a href="?page=lms-settings&tab=general&sub='.$k.'" '.((isset($_GET['sub']) && ($k == $_GET['sub']))?'class="current"':'').'>'.$value.'</a>  &#124; </li>';
		}
		echo '</ul><div class="clear"><hr/>';
		if(!isset($_GET['sub'])){$_GET['sub']='';}
		switch($_GET['sub']){
			case 'registration':
				$this->lms_registration_forms();
			break;
			case 'member_types':
				$this->lms_member_types();
			break;
			case 'group_types':
				$this->lms_group_types();
			break;
			default:
				$settings= apply_filters('lms_general_settings',array(
				array(
					'label'=>__('User Login & Registration Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
						'label' => __('Student Login redirect','vibe-customtypes'),
						'name' =>'student_login_redirect',
						'class' => 'hide',
						'type' => 'select',
						'options'=>apply_filters('wplms_student_login_redirect_filters',array(
							'' => __('Disable','vibe-customtypes'),
							'home' => __('Home page','vibe-customtypes'),
							'profile' => __('Profile page','vibe-customtypes'),
							'mycourses'=> __('My Courses page','vibe-customtypes'),
							'dashboard'=> __('Dashboard page','vibe-customtypes'),
							'same' => __('Same page','vibe-customtypes'),
							)),
						'desc' => __('Default is home page','vibe-customtypes') 
					),
				array(
						'label' => __('Instructor Login redirect','vibe-customtypes'),
						'name' =>'instructor_login_redirect',
						'type' => 'select',
						'options'=>apply_filters('wplms_instructor_login_redirect_filters',array(
							'' => __('Disable','vibe-customtypes'),
							'home' => __('Home page','vibe-customtypes'),
							'profile' => __('Profile page','vibe-customtypes'),
							'mycourses'=> __('My Courses page','vibe-customtypes'),
							'instructing_courses'=> __('Instructing Courses page','vibe-customtypes'),
							'dashboard'=> __('Dashboard page','vibe-customtypes'),
							'same' => __('Same page','vibe-customtypes'),
							)),
						'desc' => __('Default is home page','vibe-customtypes')
					),
				array(
						'label' => __('Enable One session per user','vibe-customtypes'),
						'name' => 'one_session_per_user',
						'type' => 'checkbox',
						'desc' => __('A User can login from one unique user id (excludes administrators)','vibe-customtypes')
					),
				array(
						'label' => __('Hide Administrators in Instructors','vibe-customtypes'),
						'name' =>'admin_instructor',
						'type' => 'checkbox',
						'desc' => __('Hide Administrator in all instructors page & elsewhere','vibe-customtypes')
					),
				array(
						'label' => __('Enable message to Instructor in Course Page','vibe-customtypes'),
						'name' =>'show_message_instructor',
						'type' => 'checkbox',
						'desc' => sprintf(__('Enables a Message icon to send message to Instructor, see %s tutorial %s','vibe-customtypes'),'<a href="http://vibethemes.com/documentation/wplms/knowledge-base/show-contact-instructor-in-course/" target="_blank">','</a>'),
					),
				array(
			            'label' => __('Enable Create course button in instructor profile menu', 'vibe-customtypes'),
			            'name' => 'enable_inst_create_course',
			            'desc' => __('Adds a create course link in intructor profile menu', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Show WooCommerce/Pmpro account in profile', 'vibe-customtypes'),
			            'name' => 'woocommerce_account',
			            'desc' => __('Display WooCommerce account in profile', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
						'label' => __('Set a terms and conditions page for BuddyPress registration','vibe-customtypes'),
						'name' => 'terms_conditions_in_registration',
						'type' => 'cptselect',
						'cpt'=>'page',
						'desc' => __('Set a terms and conditions page in BuddPress registration.','vibe-customtypes')
					),
				array(
			            'label' => __('Enable Student menus', 'vibe-customtypes'),
			            'name' => 'enable_student_menus',
			            'desc' => __('Adds New menu locations for Students', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable Instructor menus', 'vibe-customtypes'),
			            'name' => 'enable_instructor_menus',
			            'desc' => __('Adds New menu locations for Instructors', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Assign Free courses to students on account activation', 'vibe-customtypes'),
			            'name' => 'assign_free_courses',
			            'desc' => __('Enables auto-subscription to all the "free" courses in site to students when they signup/register and activate their account.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
					'label'=>__('Course Home Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
					'label'=>__('Course Members Visibility','vibe-customtypes' ),
					'type'=> 'select',
					'style'=>'',
					'name' => 'vibe_display_course_members',
					'options'=>array(
						0=>__('Everyone','vibe-customtypes'),
						1=>__('Logged in Users','vibe-customtypes'),
						2=>__('Course Users','vibe-customtypes'),
						3=>__('Instructors and Admins','vibe-customtypes'),
					),
					'desc'=> __('Set Course/Members Visibility','vibe-customtypes' ),
				),
				array(
					'label'=>__('Course Curriculum Visibility','vibe-customtypes' ),
					'type'=> 'select',
					'style'=>'',
					'name' => 'vibe_display_course_curriculum',
					'options'=>array(
						0=>__('Everyone','vibe-customtypes'),
						1=>__('Logged in Users','vibe-customtypes'),
						2=>__('Course Users','vibe-customtypes'),
						3=>__('Instructors and Admins','vibe-customtypes'),
					),
					'desc'=> __('Set Course/Curriculum Visibility','vibe-customtypes' ),
				),
				array(
					'label'=>__('Course Events Visibility','vibe-customtypes' ),
					'type'=> 'select',
					'style'=>'',
					'name' => 'vibe_display_course_events',
					'options'=>array(
						0=>__('Everyone','vibe-customtypes'),
						1=>__('Logged in Users','vibe-customtypes'),
						2=>__('Course Users','vibe-customtypes'),
						3=>__('Instructors and Admins','vibe-customtypes'),
					),
					'desc'=> __('Set Course/Events Visibility','vibe-customtypes' ),
				),
				array(
					'label'=>__('Course Activity Visibility','vibe-customtypes' ),
					'type'=> 'select',
					'style'=>'',
					'name' => 'vibe_display_course_activity',
					'options'=>array(
						0=>__('Everyone','vibe-customtypes'),
						1=>__('Logged in Users','vibe-customtypes'),
						2=>__('Course Users','vibe-customtypes'),
						3=>__('Instructors and Admins','vibe-customtypes'),
					),
					'desc'=> __('Set Course/Activity Visibility','vibe-customtypes' ),
				),
				array(
					'label'=>__('Course Drive Visibility','vibe-customtypes' ),
					'type'=> 'select',
					'style'=>'',
					'name' => 'vibe_display_course_drive',
					'options'=>array(
						0=>__('Everyone','vibe-customtypes'),
						1=>__('Logged in Users','vibe-customtypes'),
						2=>__('Course Users','vibe-customtypes'),
						3=>__('Instructors and Admins','vibe-customtypes'),
					),
					'desc'=> __('Set Course/Drive Visibility','vibe-customtypes' ),
				),
				array(
						'label' => __('Disable Instructor display in Courses','vibe-customtypes'),
						'name' => 'disable_instructor_display',
						'type' => 'checkbox',
						'desc' => __('Disables display of instructor in the site. Suitable for 1 instructors','vibe-customtypes')
					),
				array(
						'label' => __('Change Pre-Required Course Condition from submitted to Evaluated','vibe-customtypes'),
						'name' => 'enable_pre_required_on_evaluation',
						'type' => 'checkbox',
						'desc' => __('After enabling this students will have to wait for course evaluation by the instructor to move on to courses which have pre-requisite course under evaluation. Default : Students get access on course finish.','vibe-customtypes')
					),
				array(
						'label' => __('Hide Members section in Single Course page','vibe-customtypes'),
						'name' =>	'hide_course_members',
						'type' => 'checkbox',
						'desc' => __(' Hides member section in course pages','vibe-customtypes')
					),
				array(
						'label' => __('Show curriculum below Course description','vibe-customtypes'),
						'name' =>'course_curriculum_below_description',
						'type' => 'checkbox',
						'desc' => __('Show curriculum below course description','vibe-customtypes')
					),
				array(
			            'label' => __('Disable Course Certificate image mode', 'vibe-customtypes'),
			            'name' => 'disable_certificate_screenshot',
			            'desc' => sprintf(__('Disable course certificate in image mode. %s tutorial %s', 'vibe-customtypes'),'<a href="http://vibethemes.com/documentation/wplms/knowledge-base/disable-certificate-image-mode/" target="_blank">','</a>'),
			            'type' => 'checkbox',
					),
				array(
						'label' => __('Free units should only be accessible to logged in members','vibe-customtypes'),
						'name' =>'force_free_unit_access',
						'type' => 'checkbox',
						'desc' => __('Disable free unit access for the world, only logged in users can view free units.','vibe-customtypes')
					),
				array(
			            'label' => __('Remove Finished Courses from directory', 'vibe-customtypes'),
			            'name' => 'remove_finished_course',
			            'desc' => __('Auto remove finished courses from course directory for user', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
						'label' => __('Fix Course Menu on Scroll','vibe-customtypes'),
						'name' =>'fix_course_menu_on_scroll',
						'type' => 'checkbox',
						'desc' => __('Fix the course menu on scroll only in c2, c3 and c5 layout','vibe-customtypes')
					),
				array(
					'label'=>__('Course Status Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
						'label' => __('Course Timeline Accordion style','vibe-customtypes'),
						'name' =>'curriculum_accordion',
						'type' => 'checkbox',
						'desc' => __('Show curriculum accordion style','vibe-customtypes')
					),
				array(
						'label' => __('Disable ajax in Course unit load','vibe-customtypes'),
						'name' => 'disable_ajax',
						'type' => 'checkbox',
						'desc' => __('Ajax disabled in course unit loads','vibe-customtypes')
					),	
				array(
		            'label' => __('Auto-mark unit complete when user proceeds to next unit', 'vibe-customtypes'),
		            'name' => 'mark_unit_complete_when_next_unit',
		            'desc' => __('Hides "Mark Unit Complete" button and auto marks the unit as completed when user proceeds to next unit.', 'vibe-customtypes'),
		            'type' => 'checkbox',
				),
				array(
						'label' => __('Auto trigger finish course button','vibe-customtypes'),
						'name' =>'finish_course_auto_trigger',
						'type' => 'checkbox',
						'desc' => __('Hides Finish course button and is automatically triggered on completion of all the units and quizzes.','vibe-customtypes')
					),
				array(
			            'label' => __('Skip Course status page', 'vibe-customtypes'),
			            'name' => 'skip_course_status',
			            'desc' => __('Skip the introductory page, course status description on course start or continue', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),			
				array(
					'label'=>__('Course Pricing Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
			            'label' => __('Coming soon courses', 'vibe-customtypes'),
			            'name' => 'course_coming_soon',
			            'desc' => __('Enable coming soon option for courses', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable Course Codes', 'vibe-customtypes'),
			            'name' => 'course_codes',
			            'desc' => __('Student can purchase/access courses by using custom defined codes for courses in course pricing section. Requires BuddyPress Activity.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable Course external link', 'vibe-customtypes'),
			            'name' => 'course_external_link',
			            'desc' => __('Connect "Take this Course" button with an external link, defined in Course Pricing section.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Disable Auto allocation of Free courses', 'vibe-customtypes'),
			            'name' => 'disable_autofree',
			            'desc' => __('Disables auto allocation of free courses', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
					'label'=>__('Drip Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),

				array(
			            'label' => __('Enable Unit Time as Drip Duration', 'vibe-customtypes'),
			            'name' => 'course_unit_drip_section',
			            'desc' => __('Drip feed is applied based on specified Unit time.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable Section Drip feed', 'vibe-customtypes'),
			            'name' => 'course_drip_section',
			            'desc' => __('Drip feed is applied section wise instead of unit wise.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),


				array(
					'label'=>__('Unit settings','vibe-customtypes' ),
					'type'=> 'heading',
				),

				array(
						'label' => __('Show Unit Description in Course curriculum','vibe-customtypes'),
						'name' =>'course_curriculum_unit_description',
						'type' => 'checkbox',
						'desc' => __('Unit descriptions appear below Unit titles in Course curriculum','vibe-customtypes')
					),
				
				array(
						'label' => __('Show User progress in Course Admin','vibe-customtypes'),
						'name' =>'user_progress_course_admin',
						'type' => 'checkbox',
						'desc' => __('Small progress bar is displayed for every user below her name in course -> admin section','vibe-customtypes')
					),
				array(
						'label' => __('Enable Unit/Quiz Start Date time','vibe-customtypes'),
						'name' =>'unit_quiz_start_datetime',
						'type' => 'checkbox',
						'desc' => __('Units and Quizzes start at a particular date and time','vibe-customtypes')
					),
				
				
				array(
			            'label' => __('Unit Comments/Notes', 'vibe-customtypes'),
			            'name' => 'unit_comments',
			            'desc' => __('Enable Unit Comments only where Unit comments are enabled in post settings.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				
				array(
					'label'=>__('Quiz/Assignment Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
						'label' => __('Enable In-Course Quiz','vibe-customtypes'),
						'name' => 'in_course_quiz',
						'type' => 'checkbox',
						'desc' => __('Quizzes open inside course like units','vibe-customtypes')
					),
				array(
						'label' => __('In-Course Quiz questions per page','vibe-customtypes'),
						'name' => 'in_course_quiz_paged',
						'type' => 'number',
						'desc' => __('set number of questions appearing per page in in-course quizzes','vibe-customtypes')
					),
				array(
			            'label' => __('Enable passing score for Quiz', 'vibe-customtypes'),
			            'name' => 'quiz_passing_score',
			            'desc' => __('Set a passing score for every quiz, Student progress to next unit/quiz is restricted if user fails in quiz', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Force Quiz availability to Course students', 'vibe-customtypes'),
			            'name' => 'course_students_quiz',
			            'desc' => __('Only Course students can take the quiz. Quiz must be connected to the course in quiz settings.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Hide correct answers', 'vibe-customtypes'),
			            'name' => 'quiz_correct_answers',
			            'desc' => __('Correct answers in quizzes are not displayed unless student has finished/submitted the course.', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable negative marking', 'vibe-customtypes'),
			            'name' => 'quiz_negative_marking',
			            'desc' => __('Enables negative marking for questions in quizzes', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Show Assignments in Course Curriculum', 'vibe-customtypes'),
			            'name' => 'wplms_course_assignments',
			            'desc' => __('Assignments will be displayed in Course Curriculum', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
						'label' => __('Display Submission time in Course/Quiz/Assignment submissions','vibe-customtypes'),
						'name' => 'submission_meta',
						'type' => 'checkbox',
						'desc' => __('Displays time (eg 2 hrs) with manual submissions, * requires activity to be enabled','vibe-customtypes')
					),
				array(
					'label'=>__('Front End Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
			            'label' => __('Force Administrator Approval on every setting', 'vibe-customtypes'),
			            'name' => 'wplms_force_admin_approval',
			            'desc' => __('Instructors courses will go to pending mode when settings/pricing/curriculum is saved. *Requires Admin Approval enabled in WPLMS - Course Manager', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				array(
			            'label' => __('Enable Front end course deletion', 'vibe-customtypes'),
			            'name' => 'wplms_course_delete',
			            'desc' => __('Instructors will be able to delete course and related content from front end', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				
				array(
					'label'=>__('Miscellaneous Settings','vibe-customtypes' ),
					'type'=> 'heading',
				),
				array(
						'label' => __('Revert pretty permalinks for Courses','vibe-customtypes'),
						'name' => 'revert_permalinks',
						'type' => 'checkbox',
						'desc' => __('Revert permalinks from coursname/admin to coursename/?action-','vibe-customtypes')
					),

				array(
			            'label' => __('Enable Course forum privacy', 'vibe-customtypes'),
			            'name' => 'enable_forum_privacy',
			            'desc' => __('Only course students can access course forums', 'vibe-customtypes'),
			            'type' => 'checkbox',
					),
				
				
				array(
						'label' => __('Default order in course directory','vibe-customtypes'),
						'name' =>'default_order',
						'type' => 'select',
						'options'=>array(
							'date' => __('Recent','vibe-customtypes'),
							'title' => __('Alphabetical','vibe-customtypes'),
							'popular' => __('Number of Students','vibe-customtypes'),
							'rated' => __('Rating','vibe-customtypes'),
							'start_date' => __('Upcoming courses via Start date','vibe-customtypes'),
							'rand'  => __('Random','vibe-customtypes'),
							),
						'desc' => __('Default is menu order','vibe-customtypes')
					),
				array(
						'label' => __('Default order in Members directory (clear cookies to test)','vibe-customtypes'),
						'name' =>'members_default_order',
						'type' => 'select',
						'options'=>array(
							''=> __('None','vibe-customtypes'),
							'active' => __('Last active','vibe-customtypes'),
							'newest' => __('Newest registered','vibe-customtypes'),
							'alphabetical' => __('Alphabetical','vibe-customtypes'),
							),
						'desc' => __('Default is menu order','vibe-customtypes')
					),
				array(
						'label' => __('Limit Number of Courses per Instructor','vibe-customtypes'),
						'name' =>'course_limit',
						'type' => 'number',
						'desc' => __('( 0 for unlimited course per instructor )','vibe-customtypes')
					),
				array(
					'label' => __('Limit Number of Units Created per Instructor','vibe-customtypes'),
					'name' =>'unit_limit',
					'type' => 'number',
					'desc'=>__(' ( 0 for unlimited )','vibe-customtypes')
					),
				array(
					'label' => __('Limit Number of Quiz Created per Instructor ','vibe-customtypes'),
					'name' =>'quiz_limit',
					'type' => 'number',
					'desc' =>__('(0 for unlimited course per instructor )','vibe-customtypes'),
					),
				));

				$this->lms_settings_generate_form('general',$settings);
				break;
		}
	}


	function limit_courses_per_month($monthly_limit){
		if(!$monthly_limit)
			return;
		//Limit posts per month
	    $time_in_days = 30; // 1 means in last day
	    $count = $wpdb->get_var(
	        $wpdb->prepare("
	            SELECT COUNT(*) 
	            FROM $wpdb->posts 
	            WHERE post_status = 'publish' 
	            AND post_type = %s 
	            AND post_author = %s
	            AND post_date >= DATE_SUB(CURDATE(),INTERVAL %s DAY)",
	            'course',
	            get_current_user_id(),
	            $time_in_days
	        )
	    );
	    if ( 0 < $count ) 
	    $count = number_format( $count );

	    if ( $monthly_limit <=$count ) {
	         $errors[] = __('You have reached your monthly post limit','vibe-customtypes');
	    }
	}
	/*
		REGISTRATION FORMS in LMS - SETTINGS
	*/
	function lms_registration_forms(){

		echo '<h3>'.__('Registration Forms','vibe-customtypes').'</h3>';
		echo '<p>'.sprintf(__('Build registration forms for Students and Instructors, refer %s tutorial %s','vibe-customtypes'),'<a href="http://vibethemes.com/documentation/wplms/knowledge-base/custom-registration-forms-in-wplms/">','</a>').'</p>';
		if(!function_exists('bp_xprofile_get_groups')){
			echo _x('xProfile fields not enabled','error message displayed in registration forms when xprofile are disabled','vibe-customtypes');
			return;
		}

		$groups = bp_xprofile_get_groups( array(
			'fetch_fields' => true
		) );

		if(empty($groups)){
			echo _x('No fields found !','error message displayed in registration forms when no xprofile fields exist','vibe-customtypes');
			return;
		}
		global $wp_roles;

		if ( ! isset( $wp_roles ) )
		    $wp_roles = new WP_Roles();

		//Sync with Vibe shortcodes Ajax calls and Shortcode.php
		$form_settings = apply_filters('wplms_registration_form_settings',array(
					'hide_username' =>  __('Auto generate username from email','vibe-customtypes'),
					'password_meter' =>  __('Show password meter','vibe-customtypes'),
					'show_group_label' =>  __('Show Field group labels','vibe-customtypes'),
					'google_captcha' => __('Google Captcha','vibe-customtypes'),
					'auto_login'=> __('Register & Login simultaneously','vibe-customtypes'),
					'skip_mail' =>  __('Skip Mail verification','vibe-customtypes'),
					'default_role' =>  $wp_roles->get_names(),
		));

		/*
			FORM CREATION
		*/
		
		$forms = get_option('wplms_registration_forms');
		
		if(!empty($_POST['wplms_create_registration_from']) && !empty($_POST['wplms_registration_form_security']) && !empty($_POST['wplms_add_registration_form'])){
			if(wp_verify_nonce($_POST['wplms_registration_form_security'],'wplms_security')){
				if(empty($forms)){$forms=array();}
				$name = strtolower(strip_tags($_POST['wplms_add_registration_form']));
				$name = str_replace(' ','_',$name);
				$forms[$name] = array();
				update_option('wplms_registration_forms',$forms);
			}
		}

		

		// SAVE FORM FIELDS
		if(!empty($_POST['wplms_save_registration_fields']) && !empty($_POST['wplms_save_registration_form_fields'])){
			if(wp_verify_nonce($_POST['wplms_save_registration_form_fields'],'wplms_fields_security')){
				if(!empty($forms) && !empty($_POST)){

					foreach($forms as $k=>$v){
						$k = str_replace(' ','_',$k); //Sanitize form names
						$forms[$k]=$v;
					}
					$form_names = array_keys($forms);
					foreach($form_names as $name){
						unset($forms[$name]['fields']);
					}
					
					foreach($_POST as $label=>$value){
						if(!in_array($label,array('wplms_save_registration_form_fields','_wp_http_referer','wplms_save_registration_fields'))){
							$names = explode('|',$label);							
							if(!empty($names) && isset($forms[$names[1]])){
								if(empty($forms[$names[1]])){
									$forms[$names[1]] = array('fields'=>array($names[0]));
								}else if(empty($forms[$names[1]]['fields'])) {
									$forms[$names[1]]['fields'] = array($names[0]);
								}else if(!in_array($names[0],$forms[$names[1]]['fields'])){
									$forms[$names[1]]['fields'][] = $names[0];
								}
							}
						}
					}
					update_option('wplms_registration_forms',$forms);
				}
			}
		}

		

		if(!empty($_POST['wplms_registration_form_sub_security']) && !empty($_POST['registration_form_name'])){
			if(wp_verify_nonce($_POST['wplms_registration_form_sub_security'],'wplms_sub_security')){
				
				if(isset($_POST['default_registration_form'])){ 
					// UNSET ALL DEFAULT KEYS
					foreach($forms as $key=>$f){
						if(!empty($f) && isset($f['default'])){
							unset($forms[$key]['default']);
						}
					}
					//SET THE CURRENT DEFAULT KEY
					if(empty($forms[strip_tags($_POST['registration_form_name'])])){
						$forms[strip_tags($_POST['registration_form_name'])] = array('default'=>1);
					}else{
						$forms[strip_tags($_POST['registration_form_name'])]['default'] = 1;
					}
				}else if($_POST['remove_registration_form']){
					if(isset($forms[strip_tags($_POST['registration_form_name'])])){
						unset($forms[strip_tags($_POST['registration_form_name'])]);
					}
				}
				update_option('wplms_registration_forms',$forms);
			}
		}

		if(!empty($_POST['save_form_settings']) && !empty($_POST['registration_form_name'])){
			if(wp_verify_nonce($_POST['wplms_registration_form_sub_security'],'wplms_sub_security')){
				$forms[$_POST['registration_form_name']]['settings'] = array();
				foreach($_POST as $k => $la){
					if(!in_array($k,array('registration_form_name','wplms_registration_form_sub_security','_wp_http_referer'))){
						$sv = explode('|',$k);
						$forms[$_POST['registration_form_name']]['settings'][$sv[0]]=$la;
					}
				}
				update_option('wplms_registration_forms',$forms);
			}
		}
		if(!empty($forms))
			$form_names = array_keys($forms);

		if(!empty($forms)){
			$default = 0;
			foreach($form_names as $i=>$name){
				if($forms[$name]['default'] == 1){
					$default = $name;
				}
			}
			echo '<h3>'._x('Existing Registration forms','Forms registered in site','vibe-customtypes').'</h3>
			<ul class="registration_field_groups">';
			foreach($form_names as $i=>$name){
				if(empty($default) && $i ==0){$default = $name;}
				$name = str_replace(' ','_',$name);
				echo '<li><form method="post"><label class="field_name">'.$name.'&nbsp;<br>

				<span style="font-weight:400;text-transform:none;"><code id="'.$name.'" onclick="copyToClipboard(\'#'.$name.'\')">[wplms_registration_form name="'.$name.'" field_meta=1]</code>

				</span> <small style="font-weight:200; font-size:12px;text-transform:none;">
				<br>('.__('field_meta for field description & visbility','vibe-customtypes').')</small></label><input type="hidden" value="'.$name.'" name="registration_form_name"><input type="submit" name="default_registration_form" class="button '.(($default == $name)?'button-primary':'').'"  value="'.(($default == $name)?__('Default','vibe-customtypes'):_x('Set as default','set a default registration form','vibe-customtypes')).'">&nbsp;<a class="button" onClick="jQuery(this).parent().find(\'.registration_form_settings\').toggle(200);">'._x('Settings','delete button label','vibe-customtypes').'</a>&nbsp;<input type="submit" name="remove_registration_form" class="button" value="'._x('Delete','delete button label','vibe-customtypes').'">';
				echo '<div class="registration_form_settings" style="display:none;">';
				echo '<ul class="registration_field_groups" style="padding:10px;">';
				
				foreach($form_settings as $key => $label){
					$key = str_replace(' ','_',$key);
					echo '<li>';
					if(is_array($label)){ 
						echo '<label class="field_name">'.__('Assign User role','vibe-customtypes').'</label><select name="'.$key.'|'.$name.'"><option value="">'._x('Default role','role in registration form','vibe-customtypes').'</option>';
						foreach($label as $k=>$l){
							echo '<option value="'.$k.'" '.((isset($forms[$name]['settings'][$key]) && $forms[$name]['settings'][$key] == $k)?'selected':'').'>'.$l.'</option>';
						}
						echo '</select>';
					}else{
						echo '<label class="field_name">'.$label.'</label><input type="checkbox" name="'.$key.'|'.$name.'" '.(isset($forms[$name]['settings'][$key])?'checked':'').'/></li>';	
					}
				}
				
				do_action('wplms_registration_form_setting',$name);
				echo '<li><input type="submit" name="save_form_settings" class="button-primary" value="'._x('Save','save form settings','vibe-customtypes').'" /></li>';
				echo '</ul>';
				echo '</div>';
				wp_nonce_field('wplms_sub_security','wplms_registration_form_sub_security');
			echo '</form></li>';
			}
			echo '</ul>';
			?>
			<script>
			function copyToClipboard(element) {
			    var $temp = jQuery("<input>");
			    jQuery("body").append($temp);
			    $temp.val(jQuery(element).text()).select();
			    document.execCommand("copy");
			    $temp.remove();
			    alert('<?php _e('Shortcode Copied !','vibe-customtypes'); ?>');
			}
			</script>
			<?php
			
		}
		echo '<a id="create_registration_form" onClick="jQuery(this).next().toggle(200);" class="button-primary">'._x('Add Registration Form','create registration form button label','vibe-customtypes').'</a><form method="post" style="display:none;"><br>';
		echo '<input type="text" name="wplms_add_registration_form" style="width:50%;" placeholder="'._x('Type the name of the form, avoid spaces and special characters','enter form name placeholder','vibe-customtypes').'"><input type="submit" name="wplms_create_registration_from" class="button" value="'._x('Add Form','Add form submit button label','vibe-customtypes').'" >';
		wp_nonce_field('wplms_security','wplms_registration_form_security');
		echo '</form>';

		if(empty($forms)){
			echo '<div class="message error"><p>'._x('No Registration forms found !','warning message when no registration forms are found','vibe-customtypes').'</p></div>';
			return;
		}

		
		echo '<br><hr><h3>'._x('Connect Forms with Fields','connect form heading','vibe-customtypes').'</h3>
		<form method="post"><ul class="registration_field_groups">';
		foreach($groups as $group){
			echo '<h4>'._x('Field Group','field group prefix in registration form','vibe-customtypes').' : '.esc_html( apply_filters( 'bp_get_the_profile_group_name', $group->name ) ).'</h4>';
			if ( !empty( $group->fields ) ) {
				echo '<ul class="profile_fields">';
				
				//Form NAMES
				echo '<li><label class="field_name">'._x('Field Name','','vibe-customtypes').'</label>';
				if(!empty($form_names)){
					foreach($form_names as $name){
						echo '<label>'.$name.'</label>';
					}
				}
				echo '</li>';

				//CHECK IF FIELDS ENABLED
				foreach ( $group->fields as $field ) {
					$field = xprofile_get_field( $field->id );
					echo '<li>';
					echo '<label class="field_name">'.$field->name.' ( '.$field->type.''.(empty($field->can_delete)?', '._x('Necessary','necessary fields for buddypress registration','vibe-customtypes'):'').
					')</label>';
					if(!empty($form_names)){
						foreach($form_names as $name){
							$k = str_replace(' ','_',$field->name);
							$name = str_replace(' ','_',$name);
							echo '<label><input type="checkbox" name="'.$k.'|'.$name.'" '.((isset($forms[$name]['fields']) && in_array($k,$forms[$name]['fields']) || empty($field->can_delete))?'checked':'').' value="1"></label>';
						}
					}
					echo '</li>';
				} // end for
				echo '</ul>';
			}else {
				?>

					<p class="nodrag nofields"><?php _e( 'There are no fields in this group.', 'buddypress' ); ?></p>
				<?php
			}
		}
		echo '</ul><br>';

		wp_nonce_field('wplms_fields_security','wplms_save_registration_form_fields');
		echo '<input type="submit" name="wplms_save_registration_fields" value="'._x('Save Form fields','save form fields label in registration forms lms - settings','vibe-customtypes').'" class="button-primary"/>';
		echo '</form>';
	}
	/*
		MEMBER TYPES
	*/
	function lms_member_types(){


		/*
		SAVE MEMBER TYPES
		*/
		if(!empty($_POST['wplms_create_registration_from']) && !empty($_POST['wplms_registration_form_security']) && !empty($_POST['wplms_add_registration_form'])){
			if(wp_verify_nonce($_POST['wplms_registration_form_security'],'wplms_security')){
				if(empty($forms)){$forms=array();}
				$name = strtolower(strip_tags($_POST['wplms_add_registration_form']));
				$forms[$name] = array();
				update_option('wplms_registration_forms',$forms);
			}
		}
		/*
		GENERATE MEMBER TYPES
		*/
		
		?>

		<?php
	}

	/*
		GROUP TYPES
	*/
	function lms_group_types(){
		/*
		SAVE GROUP TYPES
		*/
		if(!empty($_POST['wplms_create_registration_from']) && !empty($_POST['wplms_registration_form_security']) && !empty($_POST['wplms_add_registration_form'])){
			if(wp_verify_nonce($_POST['wplms_registration_form_security'],'wplms_security')){
				if(empty($forms)){$forms=array();}
				$name = strtolower(strip_tags($_POST['wplms_add_registration_form']));
				$forms[$name] = array();
				update_option('wplms_registration_forms',$forms);
			}
		}
		/*
		GENERATE GROUP TYPES
		*/
	}
	/*
	FUNCTIONS TAB IN LMS - SETTINGS
	 */
	function lms_functions(){
		do_action('wplms_admin_custom_admin_panel');
		echo '<h3>'.__('LMS Functions [ For Ad-Hoc Management]','vibe-customtypes').'</h3>';
		echo '<p>'.__('Import LMS functions can be managed from here.','vibe-customtypes').'</p>';

		$template_array = apply_filters('wplms_lms_commission_tabs',array(
			''=> __('Sync Areas','vibe-customtypes'),
			'adhoc'=> __('Ad Hoc','vibe-customtypes'),
			));
		echo '<ul class="subsubsub">';
		foreach($template_array as $k=>$value){
			echo '<li><a href="?page=lms-settings&tab=functions&sub='.$k.'" '.(($k == $_GET['sub'])?'class="current"':'').'>'.$value.'</a> '.(($k=='template')?'':' &#124; ').' </li>';
		}
		echo '</ul><div class="clear"><hr/>';
		switch($_GET['sub']){
			case 'adhoc':
				echo '<form method="post"><ul class="lms-settings">';
				echo '<li><label>'.__('Custom Field Value','vibe-customtypes').'</label><input type="text" name="id" placeholder="ID"><input type="text" name="field_name" placeholder="Field Name"><input type="text" name="field_value" placeholder="Field Value"><input type="submit" name="set_field" class="button button-primary" value="Set Field" />';
				echo '<li><label>'.__('Custom Field for Student Value','vibe-customtypes').'</label><input type="text" name="student_id" placeholder="Student ID"><input type="text" name="field_name_student" placeholder="Field Name"><input type="text" name="field_value_student" placeholder="Field Value"><input type="submit" name="set_field_for_student" class="button button-primary" value="Set Field" />';
				echo '<li><label>'.__('Current Time Stamp ','vibe-customtypes').'</label><span>'.time().'</span></li>';
				wp_nonce_field('vibe_admin_adhoc','_vibe_admin_adhoc');
				echo '</ul></form>';
			break;
			default:
				echo '<h3>'.__('Synchronise LMS Data','vibe-customtypes').'</h3><p>'.__('Only required in special cases when data goes out of sync.','vibe-customtypes').'</p>';
				echo '<table class="form-table">
						<tbody>';
				$sync_settings = apply_filters('wplms_sync_settings',array(
					array(
						'id'=>'course_students',
						'label'=>__('Course - Students','vibe-customtypes'),
						'description'=>__('Verify Course Student Status and expiry for all students and courses','vibe-customtypes'),
					),
					array(
						'id'=>'unit_students',
						'label'=>__('Unit - Students','vibe-customtypes'),
						'description'=>__('Verify student unit completion for reusability','vibe-customtypes'),
					),
					array(
						'id'=>'quiz_results',
						'label'=>__('Quiz Results','vibe-customtypes'),
						'description'=>__('Verify Quiz Student Status and results for all students and quizzes','vibe-customtypes'),
					),
					array(
						'id'=>'course_forums',
						'label'=>__('Course Forums','vibe-customtypes'),
						'description'=>__('Auto-subscribe course users to course forums.','vibe-customtypes'),
					),
				));

				foreach($sync_settings as $setting){
					echo '<tr valign="top">
							<th scope="row" class="titledesc">
								<label>'.$setting['label'].'</label>
								<p style="font-weight: 400;color: #888;">'.$setting['description'].'</p>
							</th>
							<td class="forminp"><a class="button sync_resync" data-id="'.$setting['id'].'">'.__('Sync Now','vibe-customtypes').'</a></td>
						</tr>';
				}
				
				//sync_resync_js  JS CALL
						wp_nonce_field('sync_resync','sync_security');
				?>
				<script>
					jQuery(document).ready(function($){

						$('.sync_resync').on('click',function(){
							var $this = $(this);
							$this.after('<span class="status">Starting ...</span><div class="progress_wrap" style="margin: 30px 0;width: 300px;"><div class="progress" style="height: 10px;border-radius: 5px;"><div class="bar" style="width: 5%;"></div></div></div>');
							//Show progress bar
							$.ajax({
					          	type: "POST",
					          	url: ajaxurl,
					          	dataType: "json",
					          	data: { action: 'sync_resync', 
					                  id: $this.attr('data-id'),
					                  security: $('#sync_security').val(),
					                },
					          	cache: false,
					          	success: function (json) {

					            	$this.parent().find('.progress_wrap .bar').css('width','10%');
					            	$this.parent().find('span.status').text('fetch '+json.length+' results, sync in progress...');
					            	var defferred = [];
					            	var current = 0;
					            	$.each(json,function(i,item){
					            		defferred.push(item);
					            	});
					            	recursive_step(current,defferred,$this);
					            	//$.each() RUN loop on json and increment progress bar
					            	$('body').on('end_recursive_sync',function(){
					            		$.ajax({
								          	type: "POST",
								          	url: ajaxurl,
								          	data: { action: $this.attr('data-id'), 
								                  security: $('#sync_security').val(),
								                },
								          	cache: false,
								          	success: function (text) {
								          		$this.parent().find('span.status').text(text);
								            	//Complete the progress
								            	$this.parent().find('.progress_wrap .bar').css('width','100%');
								            	setTimeout(function(){$this.parent().find('.progress_wrap,.status').hide(200);},3000);
								          	}
								        });
					            	});
					          	}
					        });
						});


						function recursive_step(current,defferred,$this){
						    if(current < defferred.length){
						        $.ajax({
						            type: "POST",
						            url: ajaxurl,
						            data: defferred[current],
						            cache: false,
						            success: function(){ 
						                current++;
						                $this.parent().find('span.status').text(current+'/'+defferred.length+' complete, sync in progress...');
						                var width = 10 + 90*current/defferred.length;
						                $this.parent().find('.bar').css('width',width+'%');
						                if(defferred.length == current){
						                    $('body').trigger('end_recursive_sync');
						                }else{
						                    recursive_step(current,defferred,$this);
						                }
						            }
						        });
						    }else{
						    	$('body').trigger('end_recursive_sync');
						    }
						}//End of function

					});
				</script>
				<?php		
			break;
		}

		
	}

	function lms_settings_generate_form($tab,$settings=array()){
		echo '<form method="post">';
		wp_nonce_field('vibe_lms_settings','_wpnonce');
		echo '<table class="form-table">
				<tbody>';
		
		$lms_settings=get_option($this->option);

		foreach($settings as $setting ){
			echo '<tr valign="top" '.(empty($setting['class'])?'':'class="'.$setting['class'].'"').'>';
			switch($setting['type']){
				case 'heading':
					echo '<th scope="row" class="titledesc" colspan="2"><h3>'.$setting['label'].'</h3></th>';
				break;
				case 'link':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><a href="'.$setting['value'].'" class="button">'.$setting['button_label'].'</a>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'select':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><select name="'.$setting['name'].'">';
					foreach($setting['options'] as $key=>$option){
						echo '<option value="'.$key.'" '.(isset($lms_settings[$tab][$setting['name']])?selected($key,$lms_settings[$tab][$setting['name']]):'').'>'.$option.'</option>';
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'checkbox':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="checkbox" name="'.$setting['name'].'" '.(isset($lms_settings[$tab][$setting['name']])?'CHECKED':'').' />';
					echo '<span>'.$setting['desc'].'</span>';
				break;
				case 'number':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="number" name="'.$setting['name'].'" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:'').'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'cptselect':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp">';
					echo '<select name="'.$setting['name'].'"><option value="">'.__('Select','vibe-customtypes').' '.$setting['cpt'].'</option>';
					global $wpdb;
					$cpts = '';
					if($setting['cpt']){
						$cpts = $wpdb->get_results("
							SELECT ID,post_title 
							FROM {$wpdb->posts} 
							WHERE post_type = '".$setting['cpt']."' 
							AND post_status='publish' 
							ORDER BY post_title DESC LIMIT 0,999");	
					}
					if(is_array($cpts)){
						foreach($cpts as $cpt){
							echo '<option value="'.$cpt->ID.'" '.((isset($lms_settings[$tab][$setting['name']]) && $lms_settings[$tab][$setting['name']] == $cpt->ID)?'selected="selected"':'').'>'.$cpt->post_title.'</option>';
						}
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'title':
					echo '<th scope="row" class="titledesc"><h3>'.$setting['label'].'</h3></th>';
					echo '<td class="forminp"><hr /></td>';
				break;
				case 'color':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp forminp-color"><input type="text" name="'.$setting['name'].'" class="colorpicker" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:'').'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'hidden':
					echo '<td><input type="hidden" name="'.$setting['name'].'" value="1"/></td>';
				break;
				case 'touchpoint': 
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><strong>'.__('STUDENT','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[student][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[student][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[student][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['student'].'" class="button">','</a>');
					echo '</td></tr><tr valign="top">';
					echo '<th scope="row"></th>';
					echo '<td class="forminp"><strong>'.__('INSTRUCTOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['instructor'].'" class="button">','</a>');
					echo '</td>
						<tr><td colspan="3"><hr></td>';
				break;
				case 'touchpoint_admin': 
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><strong>'.__('INSTRUCTOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['instructor'].'" class="button">','</a>');
					echo '</td></tr><tr valign="top">';
					echo '<th scope="row"></th>';
					echo '<td class="forminp"><strong>'.__('ADMINISTRATOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[admin][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[admin][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[admin][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['admin'].'" class="button">','</a>');
					echo '</td>
						<tr><td colspan="3"><hr></td>';
				break;
				default:
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="text" name="'.$setting['name'].'" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:'').'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
			}
			
			echo '</tr>';
		}
		echo '</tbody>
		</table>';
		if(!empty($settings))
			echo '<input type="submit" name="save" value="'.__('Save Settings','vibe-customtypes').'" class="button button-primary" /></form>';
	}	

	function lms_commissions(){
		echo '<h3>'.__('Instructor Commissions','vibe-customtypes').'</h3>';
		echo '<p>'.__('Configure and pay commissions to instructors.','vibe-customtypes').'</p>';

		$template_array = apply_filters('wplms_lms_commission_tabs',array(
			''=> __('Set Commissions','vibe-customtypes'),
			'pay'=> __('Pay Commissions','vibe-customtypes'),
			));
		echo '<ul class="subsubsub">';
		foreach($template_array as $k=>$value){
			echo '<li><a href="?page=lms-settings&tab=commissions&sub='.$k.'" '.(($k == $_GET['sub'])?'class="current"':'').'>'.$value.'</a> '.(($k=='template')?'':' &#124; ').' </li>';
		}
		echo '</ul><div class="clear"><hr/>';
		switch($_GET['sub']){
			case 'pay':
				$this->lms_commission_payments();
			break;
			default:
				if(function_exists('wplms_'.$_GET['sub'])){
					$fx = 'wplms_'.$_GET['sub'];
					$fx();
				}else{
					$this->lms_commission_settings();
				}
			break;
		}
	}
	// Functioning ===== of SETTINGS
	function lms_resolve_adhoc_function(){
		if ( !isset($_POST['_vibe_admin_adhoc']) || !wp_verify_nonce($_POST['_vibe_admin_adhoc'],'vibe_admin_adhoc') )
		 return;
		else{
			do_action('wplms_admin_custom_admin_process');
			if(isset($_POST['set_field'])){
				$id=$_POST['id'];
				$field_name=$_POST['field_name'];
				$field_value=$_POST['field_value'];
				if(isset($id)){
					if(update_post_meta($id,$field_name,$field_value))
						echo '<div id="moderated" class="updated below-h2"><p>'.__('Field Value Changed','vibe-customtypes').'</p></div>';
					else
						echo '<div id="moderated" class="error below-h2"><p>'.__('Error Field value not changed','vibe-customtypes').'</p></div>';
				}else{
					echo '<div id="moderated" class="error below-h2"><p>'.__('Error Field value not entered','vibe-customtypes').'</p></div>';
				}
			}
			if(isset($_POST['set_field_for_student'])){
				$student_id=$_POST['student_id'];
				$field_name=$_POST['field_name_student'];
				$field_value=$_POST['field_value_student'];
				if(strpos($field_value,'|')){
					$field_value=explode('|',$field_value);
				}

				if(isset($student_id)){
					if(update_user_meta($student_id,$field_name,$field_value))
						echo '<div id="moderated" class="updated below-h2"><p>'.__('Student Value Changed','vibe-customtypes').'</p></div>';
					else
						echo '<div id="moderated" class="error below-h2"><p>'.__('Student value not changed','vibe-customtypes').'</p></div>';
				}else{
					echo '<div id="moderated" class="error below-h2"><p>'.__('Student value not entered','vibe-customtypes').'</p></div>';
				}
			}
		}
	}

	function lms_commission_settings(){
		echo '<h3>'.__('Set Instructor Commisions','vibe-customtypes').'</h3>';

		if(isset($_POST['set_commission'])){
			if(update_option('instructor_commissions',$_POST['commission']))
				echo '<div id="moderated" class="updated below-h2"><p>'.__('Instructor Commissions Saved','vibe-customtypes').'</p></div>';
			else
				echo '<div id="moderated" class="error below-h2"><p>'.__('Instructor Commissions not saved, contact Site-Admin !','vibe-customtypes').'</p></div>';
			$commission = $_POST['commission'];
		}else{
			$commission = get_option('instructor_commissions');
		} 

		$courses = get_posts('post_type=course&post_status=any&posts_per_page=-1');
		
		echo '<form method="POST"><div class="postbox instructor_info">
						<h3><label>'.__('Course Name','vibe-customtypes').'</label><span>'.__('Instructor','vibe-customtypes').'</span><span>'.__('PERCENTAGE','vibe-customtypes').'</span></h3>
						<div class="inside">
							<ul>';
		foreach($courses as $course){
				$instructors=apply_filters('wplms_course_instructors',$course->post_author,$course->ID);
				$cval=array();
				if(isset($commission) && is_array($commission)){
					$instructor_commission = vibe_get_option('instructor_commission');
					if(empty($instructor_commission)){
						$instructor_commission=0;
					}
					if(isset($instructors) && is_array($instructors)){
						foreach($instructors as $k=>$instructor){
							if(!isset($commission[$course->ID][$instructor])){
								$cval[$k] = $instructor_commission;
							}else{
								$cval[$k] = $commission[$course->ID][$instructor];		
							}
							
						}
					}else{
						if(!isset($commission[$course->ID][$course->post_author])){
							$val = $instructor_commission;	
						}else{
							$val = $commission[$course->ID][$course->post_author];	
						}
					}
				}else{
					$val = $instructor_commission;
				}

			 	if(isset($instructors) && is_array($instructors)){
					foreach($instructors as $k=>$instructor){
						echo '<li><label>'.$course->post_title.'</label><span>'.get_the_author_meta('display_name',$instructor).'</span><span><input type="number" name="commission['.$course->ID.']['.$instructor.']" class="small-text" value="'.$cval[$k].'" /></span></li>';
					}	
				}else	
					echo '<li><label>'.$course->post_title.'</label><span>'.get_the_author_meta('display_name',$course->post_author).'</span><span><input type="number" name="commission['.$course->ID.']['.$course->post_author.']" class="small-text" value="'.$val.'" /></span></li>';
		}

		echo '</ul>
						</div>
					</div>
					<input type="submit" class="button-primary" name="set_commission" value="'.__('Set Commisions','vibe-customtypes').'">
			   </form>';
	}

	function lms_commission_payments(){
		echo '<h3>'.__('Pay Instructor Commisions','vibe-customtypes').'</h3>';

		
		if(isset($_POST['set_time'])){
			$start_date=$_POST['start_date'];
			$end_date=$_POST['end_date'];
		}
		
		if(isset($_POST['payment_complete'])){
			$post = array();
			$post['post_title'] = sprintf(__('Commission Payments on %s','vibe-customtypes'),date('Y-m-d H:i:s'));
			$post['post_status'] = 'publish';
			$post['post_type'] = 'payments';
			$post_id = wp_insert_post( $post, $wp_error );
			if(isset($post_id) && $post_id){
				update_post_meta($post_id,'vibe_instructor_commissions',$_POST['instructor']);
				update_post_meta($post_id,'vibe_date_from',$_POST['start_date']);
				update_post_meta($post_id,'vibe_date_to',$_POST['end_date']);
				echo '<div id="moderated" class="updated below-h2"><p>'.__(' Commission Payments Saved','vibe-customtypes').'</p></div>';
			}else
				echo '<div id="moderated" class="error below-h2"><p>'.__('Commission payments not saved !','vibe-customtypes').'</p></div>';
		}

		
		echo '<form method="POST" name="payment">';
		$posts = get_posts( array ('post_type'=>'payments', 'orderby' => 'date','order'=>'DESC', 'numberposts' => '1' ) );
		foreach($posts as $post){
			$date=$post->post_date;
			$id=$post->ID;
		}
		if(isset($date))
		echo '<strong>LAST PAYMENT : '.date("G:i | D , M j Y", strtotime($date)).'</strong> <a href="'.get_edit_post_link( $id ).'" class="small_link">'.__('CHECK NOW','vibe-customtypes').'</a><br /><br />';
			
		if(!isset($start_date))
			$start_date =  date('Y-m-d', strtotime( date('Ym', current_time('timestamp') ) . '01' ) );
		if(!isset($end_date))
			$end_date = date('Y-m-d', current_time( 'timestamp' ) );	

		echo '<strong>'.__('SET TIME PERIOD','vibe-customtypes').' :</strong><input type="text" name="start_date" id="from" value="'.$start_date.'" class="date-picker-field">
					 <label for="to">&nbsp;&nbsp; To:</label> 
					<input type="text" name="end_date" id="to" value="'.$end_date.'" class="date-picker-field">
					<input type="submit" class="button" name="set_time" value="Show"></p>';

		if(isset($_POST['set_time'])){	

		

		echo '<div class="postbox instructor_info">
						<h3><label>'.__('Instructor Name','vibe-customtypes').'</label><span>'.__('Commission','vibe-customtypes').' ('.get_woocommerce_currency_symbol().')</span><span>'.__('PAYPAL EMAIL','vibe-customtypes').'</span><span>'.__('Select','vibe-customtypes').'</span><span>'.__('Pay via PayPal','vibe-customtypes').'</span></h3>
						<div class="inside">
							<ul>';

					$order_data = new WPLMS_Commissions;
					$instructor_data=$order_data->instructor_data($start_date,$end_date);

					$instructors = get_users('role=instructor');		
					foreach ($instructors as $instructor) {
						$instructor_email = $instructor->user_email;
						if(function_exists('xprofile_get_field_data')){
							$field= vibe_get_option('instructor_paypal_field');
							if( xprofile_get_field_data( $field, $instructor->ID )){
								 $instructor_email=xprofile_get_field_data( $field, $instructor->ID );
							}
						}

						        echo '<li><label>'. $instructor->user_nicename.'</label>
						        <span><input type="number" id="'.$instructor->user_login.'_amount" name="instructor['.$instructor->ID.'][commission]" class="text" value="'.(isset($instructor_data[$instructor->ID])?$instructor_data[$instructor->ID]:0).'" /></span>
						        <span><input type="text" id="'.$instructor->user_login.'_email" name="instructor['.$instructor->ID.'][email]"  value="' . $instructor_email . '" /></span>
						        <span><input type="checkbox" name="instructor['.$instructor->ID.'][set]" class="checkbox" value="1" /></span>
						        <span>
						        <a id="'.$instructor->user_login.'_payment" class="button">'.__('Pay via PayPal','vibe-customtypes').'</a>
						        
						        </span>
						        <script>
						        	jQuery(document).ready(function($){
						        		$("#'.$instructor->user_login.'_payment").click(function(){
						        			var amount =$("#'.$instructor->user_login.'_amount").val();
						        			var email =$("#'.$instructor->user_login.'_email").val();
						        			$(\'<form name="_xclick" action="https://www.paypal.com/in/cgi-bin/webscr" method="post" target="_blank"><input type="hidden" name="cmd" value="_xclick"><input type="hidden" name="business" value="\'+email+\'"><input type="hidden" name="currency_code" value="'.get_woocommerce_currency().'"><input type="hidden" name="item_name" value="'.__('Instructor Commission','vibe-customtypes').'"><input type="hidden" name="amount" value="\'+amount+\'"></form>\').appendTo($(this)).submit();
						        		});
						        	});
						        </script>
						        </li>';
						    }	
					   echo '</ul>
						</div>
					</div>
					<input type="submit" class="button-primary" name="payment_complete" value="'.__('Mark as Paid','vibe-customtypes').'">
			   ';	
		}	  

		echo '</form>'; 			
	}	


	function lms_commission_history(){

	}

	function lms_import_export(){
		$url='';
		$wplms_export= new wplms_export();
		$wplms_import = new wplms_import();
		if(isset($_POST['export'])){
			$url=$wplms_export->generate_report();
		}

		echo '<h3>'.__('Import/Export WPLMS Elements','vibe-customtypes').'</h3>';
		echo '<p>'.__('Download and upload in CSV format. Import/Export WPLMS elements with user statuses: Courses, Quizzes, Units, Assignments, Questions and Events.','vibe-customtypes').'</p>';
		
		echo '<hr/><h3>'.__('EXPORT SETTINGS','vibe-customtypes').'</h3>';
		

		$wplms_export->generate_form($url);

		echo '<hr/>';
		echo '<div style="background:#FFF;display:inline-block;padding:20px 30px 30px; margin:30px 0;border-radius:2px;">';
		if(isset($_POST['import'])){
			if(current_user_can('manage_options'))
				$wplms_import->process_upload();
		}
			$wplms_import->generate_form();
		echo '</div>';	
	}

	function lms_touch_points(){
		echo '<h3>'.__('User Touch Points','vibe-customtypes').'</h3>';
		echo '<p>'.__('Set touch points for Students and Instructors in WPLMS. Connect with Student or Instructor via following touch points','vibe-customtypes').' <a href="http://vibethemes.com/documentation/wplms/knowledge-base/touch-points-emails-messages-notifications/">'.__('Learn more','vibe-customtypes').'</a></p>';

			   
			$this->settings = $this->get_touch_points();
			$this->lms_settings_generate_form('touch',$this->settings);

	}

	static function get_touch_points(){

		$settings=array(
				'course_announcement'=>array(
									'label' => __('Announcements','vibe-customtypes'),
									'name' =>'course_announcement',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_announcement&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_announcement&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_dashboard_course_announcement',
									'params'=>4,
								),
				'course_news'=>array(
									'label' => __('News','vibe-customtypes'),
									'name' =>'course_news',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_news&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_news&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'publish_post',
									'params'=>2,
								),
				'course_subscribed'=>array(
									'label' => __('Course Subscribed','vibe-customtypes'),
									'name' =>'course_subscribed',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_subscribed&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_subscribed&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_subscribed',
									'params'=>3,
								),
				'course_added'=>array(
									'label' => __('User added to Course','vibe-customtypes'),
									'name' =>'course_added',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_added&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_added&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_bulk_action',
									'params'=>3,
								),
				'course_start'=>array(
									'label' => __('User Starts a Course ','vibe-customtypes'),
									'name' =>'course_start',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_start&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_start&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_start_course',
									'params'=> 2,
								),

				'course_certificate'=>array(
									'label' => __('Course Certificate','vibe-customtypes'),
									'name' =>'course_certificate',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_certificate&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_certificate&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_certificate_earned',
									'params'=>4,
								),
				'course_badge'=>array(
									'label' => __('Course Badge','vibe-customtypes'),
									'name' =>'course_badge',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_badge&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_badge&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_badge_earned',
									'params'=>4,
								),
				
				'course_reset'=>array(
									'label' => __('Course Reset by Instructor','vibe-customtypes'),
									'name' =>'course_reset',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_reset&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_reset&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_reset',
									'params'=>2,
								),
				'course_retake'=>array(
									'label' => __('Course Retake by User','vibe-customtypes'),
									'name' =>'course_retake',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_retake&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_retake&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_retake',
									'params'=>2,
								),
				'course_submit'=>array(
									'label' => __('Course Submit','vibe-customtypes'),
									'name' =>'course_submit',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_submit&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_submit&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_submit_course',
									'params'=>2,
								),
				'course_evaluation'=>array(
									'label' => __('Course Evaluation','vibe-customtypes'),
									'name' =>'course_evaluation',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_evaluation&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_evaluation&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_evaluate_course',
									'params'=>3,
								),
				'course_review'=>array(
									'label' => __('Course Reviews','vibe-customtypes'),
									'name' =>'course_review',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_review&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_review&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_review',
									'params'=>3,
								),
				'course_unsubscribe'=>array(
									'label' => __('Unsubscribe Course','vibe-customtypes'),
									'name' =>'course_unsubscribe',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_course_unsubscribe&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_unsubscribe&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_unsubscribe',
									'params'=>3,
								),
				'unit_complete'=>array(
									'label' => __('Unit marked complete by User','vibe-customtypes'),
									'name' =>'unit_complete',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_unit_complete&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_unit_complete&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_unit_complete',
									'params'=>3,
								),
				'unit_instructor_complete'=>array(
									'label' => __('Unit marked complete by Instructor for Student','vibe-customtypes'),
									'name' =>'unit_instructor_complete',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_unit_instructor_complete&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_unit_instructor_complete&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_unit_instructor_complete',
									'params'=>3,
								),
				'unit_instructor_uncomplete'=>array(
									'label' => __('Unit marked incomplete by Instructor for Student','vibe-customtypes'),
									'name' =>'unit_instructor_uncomplete',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_unit_instructor_uncomplete&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_unit_instructor_uncomplete&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_unit_instructor_uncomplete',
									'params'=>3,
								),
				'unit_comment'=>array(
									'label' => __('Unit comment added by User','vibe-customtypes'),
									'name' =>'unit_comment',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_unit_comment&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_unit_comment&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_course_unit_comment',
									'params'=>4,
								),
				'start_quiz'=>array(
									'label' => __('Quiz Start by user','vibe-customtypes'),
									'name' =>'start_quiz',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_start_quiz&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_start_quiz&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_start_quiz',
									'params'=>2,
								),
				'quiz_submit'=>array(
									'label' => __('Quiz Submitted by user','vibe-customtypes'),
									'name' =>'quiz_submit',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_quiz_submit&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_quiz_submit&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_submit_quiz',
									'params'=>2,
								),
				'quiz_reset'=>array(
									'label' => __('Quiz Reset by Instructor','vibe-customtypes'),
									'name' =>'quiz_reset',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_quiz_reset&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_quiz_reset&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_quiz_reset',
									'params'=>2,
								),
				'quiz_retake'=>array(
									'label' => __('Quiz Retake by User','vibe-customtypes'),
									'name' =>'quiz_retake',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_quiz_retake&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_quiz_retake&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_quiz_retake',
									'params'=>2,
								),
				'quiz_evaluation'=>array(
									'label' => __('Quiz Evaluation','vibe-customtypes'),
									'name' =>'quiz_evaluation',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_quiz_evaluation&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_quiz_evaluation&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_evaluate_quiz',
									'params'=>4,
								),
				'start_assignment'=>array(
									'label' => __('Assignment Start by user','vibe-customtypes'),
									'name' =>'start_assignment',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_start_assignment&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_start_assignment&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_start_assignment',
									'params'=>2,
								),
				'assignment_submit'=>array(
									'label' => __('Assignment Submitted by user','vibe-customtypes'),
									'name' =>'assignment_submit',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_assignment_submit&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_assignment_submit&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_submit_assignment',
									'params'=>2,
								),
				'assignment_evaluation'=>array(
									'label' => __('Assignment Evaluation','vibe-customtypes'),
									'name' =>'assignment_evaluation',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_assignment_evaluation&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_assignment_evaluation&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_evaluate_assignment',
									'params'=>4,
								),
				'assignment_reset'=>array(
										'label' => __('Assignment Reset by Instructor','vibe-customtypes'),
										'name' =>'assignment_reset',
										'value' => array(
											'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_assignment_reset&post_type=bp-email'),
											'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_assignment_reset&post_type=bp-email'),
										),
										'type' => 'touchpoint',
										'hook' => 'wplms_assignment_reset',
										'params'=>2,
								),
				'user_course_application'=> array(
									'label' => __('Student applied for Course','vibe-customtypes'),
									'name' =>'user_course_application',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_user_course_application&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_user_course_application&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_user_course_application',
									'params'=>2,
								),
				'manage_user_application'=> array(
									'label' => __('Instructor approves/rejects user application','vibe-customtypes'),
									'name' =>'manage_user_application',
									'value' => array(
										'student' => admin_url('edit.php?taxonomy=bp-email-type&term=student_manage_user_application&post_type=bp-email'),
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_manage_user_application&post_type=bp-email'),
									),
									'type' => 'touchpoint',
									'hook' => 'wplms_manage_user_application',
									'params'=>3,
								),
				'course_go_live'=> array(
									'label' => __('Instructor Publishes a Course or Sends for Approval','vibe-customtypes'),
									'name' =>'course_go_live',
									'value' => array(
										'instructor' => admin_url('edit.php?taxonomy=bp-email-type&term=instructor_course_go_live&post_type=bp-email'),
										'admin' => admin_url('edit.php?taxonomy=bp-email-type&term=admin_course_go_live&post_type=bp-email'),
									),
									'type' => 'touchpoint_admin',
									'hook' => 'wplms_course_go_live',
									'params'=>2,
								),
							);
		return apply_filters('wplms_touch_points',$settings);
	}
	function lms_emails(){
		echo '<h3>'.__('Email Settings','vibe-customtypes').'</h3>';
		echo '<p>'.__('Configure email template for Emails.','vibe-customtypes').'</p>';

		$template_array = apply_filters('wplms_email_template_array',array(
			''=> __('Email Options','vibe-customtypes'),
			'schedule'=> __('Email Schedule','vibe-customtypes'),
			'scheduled_emails'=> __('Scheduled Emails','vibe-customtypes'),
			));
		echo '<ul class="subsubsub">';
		foreach($template_array as $k=>$value){
			echo '<li><a href="?page=lms-settings&tab=emails&sub='.$k.'" '.(($k == $_GET['sub'])?'class="current"':'').'>'.$value.'</a> '.(($k=='template')?'':' &#124; ').' </li>';
		}
		echo '</ul>';
		switch($_GET['sub']){
			case 'schedule':
				$this->email_schedule();
			break;
			case 'scheduled_emails':
				$this->scheduled_emails();
			break;
			default:
				$this->lms_email_settings();
			break;
		}
	}

	function lms_email_settings(){


			$settings=array(
						
						array(
							'label' => __('FROM "Name"','vibe-customtypes'),
							'name' =>'from_name',
							'type' => 'text',
							'desc' => __('Name from which the email will be sent','vibe-customtypes')
						),
						array(
							'label' => __('FROM "Email"','vibe-customtypes'),
							'name' =>'from_email',
							'type' => 'text',
							'desc' => __('Email from which the email will be sent','vibe-customtypes')
						),
						array(
							'label' => __('Contact Form email','vibe-customtypes'),
							'type' => 'link',
							'value'=>admin_url('edit.php?taxonomy=bp-email-type&term=wplms_contact_form_email&post_type=bp-email'),
							'button_label'=> _x('Edit email template','lms settings button','vibe-customtypes'),
							'desc' => __('Contact Form email sent when using contact form shortcode.','vibe-customtypes')
						),
						array(
							'label' => __('Edit Activation email','vibe-customtypes'),
							'type' => 'link',
							'value'=>admin_url('edit.php?taxonomy=bp-email-type&term=core-user-registration&post_type=bp-email'),
							'button_label'=> _x('Edit email template','lms settings button','vibe-customtypes'),
							'desc' => __('Activation email sent by BuddyPress on user registration.','vibe-customtypes')
						),
						array(
							'label' => __('Edit Forgot Password email','vibe-customtypes'),
							'type' => 'link',
							'value'=>admin_url('edit.php?taxonomy=bp-email-type&term=wplms_forgot_password&post_type=bp-email'),
							'button_label'=> _x('Edit email template','lms settings button','vibe-customtypes'),
							'desc' => __('Forgot password email sent by WordPress when user clicks on forgot password link.','vibe-customtypes')
						),
				);
			$this->settings = apply_filters('wplms_email_settings',$settings);
			$this->lms_settings_generate_form('email_settings',$settings);
	}
	
	function email_schedule(){
		$settings=array(
						array(
							'label' => sprintf(__('Schedule Drip Feed Email %s Edit Email %s','vibe-customtypes'),'<a href="'.admin_url('edit.php?taxonomy=bp-email-type&term=wplms_drip_mail&post_type=bp-email').'" class="button">','</a>'),
							'name' =>'drip_schedule',
							'type' => 'title'
						),
						array(
							'label' => __('Enable Drip Feed Email','vibe-customtypes'),
							'name' =>'drip',
							'type' => 'select',
							'options'=>array(
								'no' => __('No','vibe-customtypes'),
								'yes' => __('Yes','vibe-customtypes'),
								),
							'desc' => __('Email students when the drip feed units are available','vibe-customtypes')
						),
						array(
							'label' => __('Schedule Email','vibe-customtypes'),
							'name' =>'drip_schedule',
							'type' => 'select',
							'options'=>array(
								'24' => __('Before 24 Hours of unit availability','vibe-customtypes'),
								'12' => __('Before 12 Hours of unit availability','vibe-customtypes'),
								'6' => __('Before 6 Hours of unit availability','vibe-customtypes'),
								'1' => __('Before 1 Hours of unit availability','vibe-customtypes'),
								'0' => __('When Unit is available','vibe-customtypes'),
							),
							'desc' => __('Accuracy of email depends upon site traffic and resources.','vibe-customtypes')
						),
						

						array(
							'label' => sprintf(__('Schedule Course Expiry Email %s Edit Email %s','vibe-customtypes'),'<a href="'.admin_url('edit.php?taxonomy=bp-email-type&term=wplms_expire_mail&post_type=bp-email').'" class="button">','</a>'),
							'name' =>'e_s',
							'type' => 'title'
						),
						array(
							'label' => __('Enable Course Expire Email','vibe-customtypes'),
							'name' =>'expire',
							'type' => 'select',
							'options'=>array(
								'no' => __('No','vibe-customtypes'),
								'yes' => __('Yes','vibe-customtypes'),
								),
							'desc' => __('Email students when the course expires','vibe-customtypes')
						),
						array(
							'label' => __('Schedule Email','vibe-customtypes'),
							'name' =>'expire_schedule',
							'type' => 'select',
							'options'=>array(
								'24' => __('Before 24 Hours of Course expiry','vibe-customtypes'),
								'12' => __('Before 12 Hours of Course expiry','vibe-customtypes'),
								'6' => __('Before 6 Hours of Course expiry','vibe-customtypes'),
								'1' => __('Before 1 Hours of Course expiry','vibe-customtypes'),
								'0' => __('When Course expires','vibe-customtypes'),
							),
							'desc' => __('Accuracy of email depends upon site traffic and resources.','vibe-customtypes')
						),
						
						array(
							'label' => sprintf(__('Schedule Inactive Users Email %s Edit Email %s','vibe-customtypes'),'<a href="'.admin_url('edit.php?taxonomy=bp-email-type&term=wplms_inactive_user&post_type=bp-email').'" class="button">','</a>'),
							'name' =>'inactive_schedule',
							'type' => 'title'
						),
						array(
							'label' => __('Enable Inactive Users Email','vibe-customtypes'),
							'name' =>'inactive',
							'type' => 'select',
							'options'=>array(
								'no' => __('No','vibe-customtypes'),
								'yes' => __('Yes','vibe-customtypes'),
								),
							'desc' => __('Email users when they are inactive on the website for a specifc time period','vibe-customtypes')
						),
						array(
							'label' => __('Schedule Inactivity Email (in days)','vibe-customtypes'),
							'name' =>'inactivity_schedule',
							'type' => 'number',
							'desc' => __('Accuracy of email depends upon site traffic and resources.','vibe-customtypes')
						),
				);
			$this->settings = apply_filters('wplms_email_schedule',$settings);
			$this->lms_settings_generate_form('schedule',$this->settings);
			echo '<style>.form-table th{width:250px;}</style>';
	}


	function scheduled_emails(){

		if(!function_exists('_get_cron_array'))
			return;
		if(!empty($_POST) && isset($_POST['remove_schedule'])){
			if( wp_verify_nonce($_POST['remove_scheduled_email_security'],'remove_scheduled_email_'.$_POST['timestamp']) && !empty($_POST['cron_key'])){
				$key = $_POST['cron_key'];
				$crons = _get_cron_array();
				$timestamp =  $_POST['timestamp'];
				$hook = $_POST['hook'];
			   
			    unset( $crons[$timestamp][$hook][$key] );
			    if ( empty($crons[$timestamp][$hook]) ){
			        unset( $crons[$timestamp][$hook] );
			    }
			    if ( empty($crons[$timestamp]) ){
			        unset( $crons[$timestamp] );
			    }
			    _set_cron_array( $crons );
			    unset($_POST['remove_scheduled_email_security']);
			    echo '<div class="clear"></div><div class="updated notice is-dismissible"><p>'.__('Cron removed.','vibe-customtypes').'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Dismiss this notice.','vibe-customtypes').'</span></button></div>';
			}else{
				echo '<div class="clear"></div><div class="notice notice-error"><p>'.__('There was an error while removing cron.','vibe-customtypes').'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Dismiss this notice.','vibe-customtypes').'</span></button></div>';
			}
		}
		echo '<br style="clear:both"><table class="form-table">';
		
		$crons =  _get_cron_array();
		if(!empty($crons)){
			echo '<script>var confirm_message_cron= "'.__('Are you sure you want to remove this schedule?','vibe-customtypes').'";</script>';
			$format = get_option('date_format') . ' - '. get_option('time_format');
			$data = '';
			$check_emails = 0;
			foreach($crons as $timestamp => $cron){
				if(isset($cron['wplms_send_course_expiry_mail']) || isset($cron['wplms_send_drip_mail'])){
					$check_emails++;
					if(isset($cron['wplms_send_course_expiry_mail'])){
						$value = $cron['wplms_send_course_expiry_mail'];
						$data = '<form method="post" id="expiry" class="remove_schedule" action="?page=lms-settings&tab=emails&sub=scheduled_emails"><input name="hook" value="wplms_send_course_expiry_mail" type="hidden"  data-hook="wplms_send_course_expiry_mail" >';
						$data .= '<input type="hidden" name="remove_scheduled_email_security" value="'.wp_create_nonce('remove_scheduled_email_'.$timestamp).'" id="remove_scheduled_email_security">';
						foreach($value as $v){
							
							$data .= '<input type="hidden" name="cron_key" value="'.md5(serialize($v['args'])).'"> ';
							$data .= '<input name="timestamp" type="hidden" value="'.$timestamp.'">';
							$data .= '<input type="submit" class="button-primary remove_schedule_submit button" name="remove_schedule" value="'.__('Remove Schedule','vibe-customtypes').'"></form>';
							$course_id = $v['args'][0];
							$user_id = $v['args'][1];
							break;
						}
					}else{
						$value = $cron['wplms_send_drip_mail'];
						$data = '<form method="post" id="drip" class="remove_schedule" action="?page=lms-settings&tab=emails&sub=scheduled_emails"><input name="hook" value="wplms_send_drip_mail" type="hidden"  data-hook="wplms_send_drip_mail" >';
						$data .='<input type="hidden" name="remove_scheduled_email_security" value="'.wp_create_nonce('remove_scheduled_email_'.$timestamp).'" id="remove_scheduled_email_security">';
						foreach($value as $v){
							
							$data .= '<input name="timestamp" type="hidden" value="'.$timestamp.'">';
							$data .= '<input type="hidden" name="cron_key" value="'.md5(serialize($v['args'])).'"> ';
							$data .= '<input type="submit" class="button-primary remove_schedule_submit button" name="remove_schedule" value="'.__('Remove Schedule','vibe-customtypes').'"></form>';
							
							$course_id = $v['args'][1];
							$user_id = $v['args'][2];
							break;
						}
					}
					 
					echo '<tr><th><label>'.(isset($cron['wplms_send_course_expiry_mail'])?__('Course Expiry mail','vibe-customtypes'):__('Drip feed mail','vibe-customtypes')).'</label></th><td>'.get_date_from_gmt ( date( 'Y-m-d H:i:s', $timestamp ), $format ).'</td><td>'.get_the_title($course_id).'</td><td>'.(!empty($user_id)?bp_core_get_user_displayname($user_id):__('N.A','vibe-customtypes')).'</td><td>'.$data.'</td></tr>';

				}
			}
			
			if($check_emails == 0){
				echo '<div class="message"><h3>'.__('No Scheduled emails','vibe-customtypes').'</h3></div>';
			}
		}else{
			echo '<div class="message"><h3>'.__('No Scheduled emails','vibe-customtypes').'</h3></div>';
		}
		echo '</table>';
	}
	function lms_addons(){
		
		$addons = apply_filters('wplms_lms_addons',array(
			'wplms-batches' =>array(
					'label'=> __('WPLMS Batches','vibe-customtypes'),
					'sub'=> __('Course Batches for WPLMS','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'license_key'=>'wplms_batches_license_key',
					'link' => 'http://www.vibethemes.com/downloads/wplms-batches/',
					'extra'=>array('Enable Course Batches','Supports Class TimeTable'),
					'activated'=> (is_plugin_active('wplms-batches/wplms-batches.php')?true:false),
					'price'=>'BUY $29',
					'class'=>'featured'
				),
			'wplms-s3' =>array(
					'label'=> __('WPLMS S3','vibe-customtypes'),
					'sub'=> __('Secure files using Amazon S3.','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'license_key'=>'wplms_s3_license_key',
					'link' => 'http://www.vibethemes.com/downloads/wplms-s3/',
					'extra'=>array('Secure Files with expiring Links','Host Videos/Audios/Files on Amazon S3','Supports Instructor Privacy'),
					'activated'=> (is_plugin_active('wplms-s3/wplms-s3.php')?true:false),
					'price'=>'BUY $29',
					'class'=>'featured'
				),
			'wplms-mailchimp' =>array(
					'label'=> __('WPLMS Mailchimp','vibe-customtypes'),
					'sub'=> __('Mailchimp Lists for WPLMS','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'license_key'=>'wplms_mailchimp_license_key',
					'link' => 'http://www.vibethemes.com/downloads/wplms-mailchimp/',
					'extra'=>array('Sync Email Lists','Auto subscribe lists'),
					'activated'=> (is_plugin_active('wplms-mailchimp/wplms-mailchimp.php')?true:false),
					'price'=>'BUY $19',
					'class'=>'featured'
				),
			'wplms-woocommerce' =>array(
					'label'=> __('WPLMS WooCommerce','vibe-customtypes'),
					'sub'=> __('Integrate WPLMS with WooCommerce.','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> 'woocommerce',
					'license_key'=>'wplms_woocommerce_license_key',
					'link' => 'http://www.vibethemes.com/downloads/wplms-woocommerce/',
					'extra'=>array('Full WooCommerce support','Supports Variable Pricing'),
					'activated'=> (is_plugin_active('wplms-woocommerce/wplms-woocommerce.php')?true:false),
					'price'=>'BUY $19',
					'class'=>'featured'
				),
			'wplms-wishlist' =>array(
					'label'=> __('WPLMS Wishlists','vibe-customtypes'),
					'sub'=> __('Create unlimited, sharable wishlists.','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'license_key'=>'wplms_wishlist_license_key',
					'link' => 'http://www.vibethemes.com/downloads/wplms-wishlist/',
					'extra'=>array('Add to WishList','Create Collections'),
					'activated'=> (is_plugin_active('wplms-wishlist/wplms-wishlist.php')?true:false),
					'price'=>'<del>BUY $29</del> FREE',
					'class'=>'featured'
				),
			'wplms-course-custom-nav' => array(
					'label'=> __('WPLMS Custom Course Navigation','vibe-customtypes'),
					'sub'=> __('Customise Course Sections','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'link' => 'http://vibethemes.com/documentation/wplms/knowledge-base/wplms-course-custom-nav-plugin/',
					'extra'=>array('Add Custom sections in Course','Customise Course creation process'),
					'activated'=> (is_plugin_active('wplms-course-custom-nav/wplms-course-custom-nav.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'wplms-courseware-migrate' => array(
					'label'=> __('WP Courseware to WPLMS Migration','vibe-customtypes'),
					'sub'=> __('Migrate Courses from WP Courseware to WPLMS.','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'link' => 'http://vibethemes.com/documentation/wplms/knowledge-base/wp-courseware-wplms-migrate-plugin/',
					'extra'=>array('One Click migration process'),
					'activated'=> (is_plugin_active('wplms-courseware-migrate/wplms_courseware_migrate.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'wplms-coauthors-plus' => array(
					'label'=> __('WPLMS CoAuthors Plus','vibe-customtypes'),
					'sub'=> __('Integrate  WP CoAuhors plus plugin','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> 'co-authors-plus',
					'link' => 'https://wordpress.org/plugins/wplms-coauthors-plus/',
					'extra'=>array('Enable Multiple instructors per course'),
					'activated'=> (is_plugin_active('WPLMS-Coauthors-Plus/wplms-coauthor-plus.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'wplms-badgeos' => array(
					'label'=> __('WPLMS BadgeOS','vibe-customtypes'),
					'sub'=> __('Connect BadgeOS badges with WPLMS','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> 'badgeos',
					'link' => 'https://wordpress.org/plugins/wplms-badgeos/',
					'extra'=>array('Create Custom badges','Award Badges on various Course tasks'),
					'activated'=> (is_plugin_active('wplms-badgeos/wplms-badgeos.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'wplms-dwqa' => array(
					'label'=> __('WPLMS DW Q&A','vibe-customtypes'),
					'sub'=> __('Integrate DW Questions & Answer with WPLMS','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> 'dw-question-answer',
					'link' => 'https://wordpress.org/plugins/wplms-dwqa/',
					'extra'=>'',
					'activated'=> (is_plugin_active('wplms-dwqa/wplms-dwqa.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'wplms-edd' => array(
					'label'=> __('WPLMS Easy Digital Downloads','vibe-customtypes'),
					'sub'=> __('Connect Easy Digital downloads with WPLMS','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-cart"></span>',
					'requires'=> 'easy-digital-downloads',
					'link' => 'http://vibethemes.com/documentation/wplms/knowledge-base/wplms-edd-addon/',
					'extra'=>'',
					'activated'=> (is_plugin_active('easy-digital-downloads/easy-digital-downloads.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'bp-social-connect' => array(
					'label'=> __('BP Social Connect','vibe-customtypes'),
					'sub'=> __('Connect your BuddyPress site with Social networks Login &  Registration','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'link' => 'https://wordpress.org/plugins/bp-social-connect/',
					'extra'=>array('Integrate Facebook Login/Register','Integrate Google Plus Login/Register'),
					'activated'=> (is_plugin_active('bp-social-connect/bp_social_connect.php')?true:false),
					'price'=>0,
					'class'=>''
				),
			'bp-profile-cover' => array(
					'label'=> __('BP Profile Cover','vibe-customtypes'),
					'sub'=> __('Add cover images to Profiles','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-portfolio"></span>',
					'requires'=> '',
					'link' => 'https://wordpress.org/plugins/bp-profile-cover/',
					'extra'=>'',
					'price'=>0,
					'activated'=> (is_plugin_active('bp-profile-cover/loader.php')?true:false),
					'class'=>''
				),
			'wplms-custom-certificate-codes'=> array(
					'label'=> __('WPLMS Custom Certificate Codes','vibe-customtypes'),
					'sub'=> __('Define custom certificate codes for Certificates','vibe-customtypes'),
					'icon'=> '<span class="dashicons dashicons-media-interactive"></span>',
					'requires'=> '',
					'link' => 'http://vibethemes.com/documentation/wplms/knowledge-base/custom-certificate-codes-plugin/',
					'extra'=>array('Define Custom Certificate code pattern','Generate codes for existing certificates'),
					'price'=>0,
					'activated'=> (is_plugin_active('wplms-custom-certificate-codes/wplms_custom_certificate_codes.php')?true:false),
					'class'=>''
				),
			));
		?>
		<?php
		foreach($addons as $key=>$addon){ 
			if(!empty($addon) && !empty($addon['label'])){

			$class = apply_filters('wplms_addon_class','',$addon);

			?>
				<div class="wplms_addon_block">
					<div class="inside <?php echo $class.' '.(($addon['activated'])?'active':''); ?>">
						<?php echo (empty($addon['price'])?'<span class="free">FREE</span>':'<span class="free premium">'.$addon['price'].'</span>'); ?>
						<h3 class=""><?php echo $addon['label']; ?><span><?php echo $addon['sub']; ?></span></h3>
						<?php 
						if(!empty($addon['extra'])){
							if(is_array($addon['extra'])){
								echo '<ul>';
								foreach($addon['extra'] as $ex){
									echo '<li>'.$ex.'</li>';
								}
								echo '</ul>';
							}else{
								echo $addon['extra'];
							}
						}
						if(!empty($addon['license_key']) && $addon['activated']){
							$val = get_option($addon['license_key']);
							?>
							<div class="activate_license">
                                <form action="<?php  echo admin_url( 'admin.php?page=lms-settings&tab=addons'); ?>" method="post">
                                    <input type="text" id="<?php echo $addon['license_key']; ?>" name="license_key" class="vibe_license_key" value="<?php echo $val ?>" placeholder="<?php _e('Enter License Key','vibe-customtypes'); ?>" />
                                    <?php 
                                    if(!empty($val) && strpos($class,'invalid') === false){    ?>
                                    <input type="submit" class="button primary" name="<?php echo $addon['license_key']; ?>" value="Deactivate" />
                                    <?php
                                    }else{
                                        ?>
                                    <input type="submit" class="button primary" name="<?php echo $addon['license_key']; ?>" value="Activate" />
                                    <?php
                                    }
                                    wp_nonce_field( $key, $key);
                                    ?>
                                </form>
                            </div>
							<a target="_blank" class="button button-primary activate_license_toggle"><?php _e('License Key','vibe-customtypes'); ?></a>
							<?php
						}
						?>
						<a href="<?php echo $addon['link']; ?>" target="_blank" class="button"><?php _e('Learn more','vibe-customtypes'); ?></a>
					</div>
				</div>
		<?php
			}
		}
		?>
		<div class="clear">	</div>
		</div>
		<?php
	}
}


class wplms_miscellaneous_settings{

	var $option; 

	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new wplms_miscellaneous_settings();
        return self::$instance;
    }

	private function __construct(){
		$this->settings=get_option('lms_settings');
		add_action('wplms_before_create_course_header',array($this,'front_end_check_course_limit'));
		add_action( 'admin_head-post-new.php', array($this,'check_course_limit' ));
		
		add_action('wp_ajax_load_coursetree',array($this,'load_coursetree'));
		add_action('wp_ajax_vibe_update_license_key',array($this,'update_license_key'));
		add_action('wp_ajax_lms_import_wplms_emails',array($this,'import_wplms_emails'));

		/*==== Sync Functions ====*/
		add_action('wp_ajax_sync_resync',array($this,'sync_resync'));
		add_action('wp_ajax_sync_resync_course_students',array($this,'sync_resync_course_students'));
		add_action('wp_ajax_course_students',array($this,'end_course_students_sync'));

		add_action('wp_ajax_sync_resync_quiz_results',array($this,'sync_resync_quiz_results'));
		add_action('wp_ajax_quiz_results',array($this,'end_quiz_results_sync'));

		add_action('wp_ajax_sync_resync_unit_students',array($this,'sync_resync_unit_students'));
		add_action('wp_ajax_unit_students',array($this,'end_unit_students_sync'));

		add_action('wp_ajax_sync_resync_course_forums',array($this,'sync_resync_course_forums'));
		add_action('wp_ajax_course_forums',array($this,'end_course_forums_sync'));
	}
	

	function import_wplms_emails(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'vibe_lms_settings') ){
		    _e('Security check Failed. Contact Administrator.','vibe-customtypes');
		    die();
		}

	}
	
	function check_course_limit() {

		$lms_settings = $this->settings;

		if(!isset($lms_settings) || !is_array($lms_settings))
			return;

	    global $userdata;
	    global $post_type;
	    

	    global $wpdb;

	    if(in_array('instructor',$userdata->roles)){ 
			if( $post_type === 'course' && isset($lms_settings['general']['course_limit']) && $lms_settings['general']['course_limit']) {
				$course_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'course' AND post_author = $userdata->ID" );
				if( $course_count >= $lms_settings['general']['course_limit'] ) { wp_die( "Course Limit Exceeded" ); }
			} elseif( $post_type === 'unit' && isset($lms_settings['general']['unit_limit']) && $lms_settings['general']['unit_limit']) {
				$unit_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'unit' AND post_author = $userdata->ID" );
				if( $unit_count >= $lms_settings['general']['unit_limit'] ) { wp_die( "Unit Limit Exceeded" ); }
			} elseif( $post_type === 'quiz' && isset($lms_settings['general']['quiz_limit']) && $lms_settings['general']['quiz_limit']) {
				$quiz_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'quiz' AND post_author = $userdata->ID" );
				if( $quiz_count >= $lms_settings['general']['quiz_limit'] ) { wp_die( "Quiz Limit Exceeded" ); }
			}
		}
		return;
	}

	function front_end_check_course_limit(){
		$lms_settings = $this->settings;

		if(!isset($lms_settings) || !is_array($lms_settings))
			return;

	    global $userdata;
	    

	    global $wpdb;

	    if(in_array('instructor',$userdata->roles)){ 
			if( isset($lms_settings['general']['course_limit']) && $lms_settings['general']['course_limit']) {
				$course_count = $wpdb->get_var( "SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'course' AND post_author = $userdata->ID" );
				if( $course_count >= $lms_settings['general']['course_limit'] ) { wp_die( "Course Limit Exceeded" ); }
			}
		}
		return;
	}

	function load_coursetree(){
		$course_id = $_POST['course_id'];
	    if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'security') || !is_numeric($course_id)){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		$curriculum = get_post_meta($course_id,'vibe_course_curriculum',true);
		if(isset($curriculum) && is_array($curriculum)){
			echo '<ul class="course_curriculum">';
			foreach ($curriculum as $key => $value) {
				if(is_numeric($value)){
					echo '<li><a href="'.get_edit_post_link($value).'">'.get_post_type($value).' : '.get_the_title($value).'</a>';
					if(get_post_type($value) == 'unit'){
						$assignments = get_post_meta($value,'vibe_assignment',true);
						if(!empty($assignments) && is_array($assignments)){
							echo '<ul class="assignments">';
							foreach($assignments as $assignment){
								echo '<li><a href="'.get_edit_post_link($assignment).'">'.__('Assignment','vibe-customtypes').' : '.get_the_title($assignment).'</a></li>';
							}
							echo '</ul>';
						}
					}
					echo '</li>';
				}else{
					echo '<li><strong>'.$value.'</strong></li>';
				}
			}
			echo '</ul>';
		}
		die();
	}

	function update_license_key(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'security')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		if(empty($_POST['addon']) || empty($_POST['key'])){
			_e('Unable to update key.','vibe-customtypes');
			die();
		}
		update_option($_POST['addon'],$_POST['key']);
		echo apply_filters('wplms_addon_license_key_updated',__('Key Updated.','vibe-customtypes'));
		die();
	}
	/*
	SYNC FUNCTIONS
	 */
	function sync_resync(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		$this->deleted= 0;
		switch($_POST['id']){
			case 'course_students':
				global $wpdb;
				$data = $wpdb->get_results("SELECT p.ID as course_id,m.user_id as user_id FROM {$wpdb->posts} as p LEFT JOIN {$wpdb->usermeta} as m ON p.ID = m.meta_key WHERE p.post_type = 'course' AND p.post_status = 'publish'");
				$course_data = array();
				if(!empty($data)){
					$security = wp_create_nonce('sync_resync_course_students');
					foreach($data as $d){

						if(empty($course_data[$d->course_id])){
							$course_data[$d->course_id]=array('action'=>'sync_resync_course_students','security'=>$security,'course_id'=>$d->course_id,'users' => array($d->user_id));
						}else{
							if(!in_array($d->user_id,$course_data[$d->course_id]['users'])){
								$course_data[$d->course_id]['users'][] = $d->user_id;
							}
						}
					}
				}
				echo json_encode($course_data);
			break;
			case 'quiz_results':
				global $wpdb;
				$data = $wpdb->get_results("SELECT p.ID as quiz_id,m.user_id as user_id FROM {$wpdb->posts} as p LEFT JOIN {$wpdb->usermeta} as m ON p.ID = m.meta_key WHERE p.post_type = 'quiz' AND p.post_status = 'publish'");
				$quiz_data = array();
				if(!empty($data)){
					$security = wp_create_nonce('sync_resync_quiz_results');
					foreach($data as $d){

						if(empty($quiz_data[$d->quiz_id]) && !empty($d->user_id)){
							$quiz_data[$d->quiz_id]=array('action'=>'sync_resync_quiz_results','security'=>$security,'quiz_id'=>$d->quiz_id,'users' => array($d->user_id));
						}else{
							if(!in_array($d->user_id,$quiz_data[$d->quiz_id]['users']) && !empty($d->user_id)){
								$quiz_data[$d->quiz_id]['users'][] = $d->user_id;
							}
						}
					}
				}
				echo json_encode($quiz_data);
			break;
			case 'unit_students':
				global $wpdb;
				$data = $wpdb->get_results("SELECT p.ID as unit_id,m.user_id as user_id FROM {$wpdb->posts} as p LEFT JOIN {$wpdb->usermeta} as m ON p.ID = m.meta_key WHERE p.post_type = 'unit' AND p.post_status = 'publish'");
				$unit_data = array();
				if(!empty($data)){
					$security = wp_create_nonce('sync_resync_unit_students');
					foreach($data as $d){
						if(empty($unit_data[$d->unit_id]) && !empty($d->user_id)){
							$unit_data[$d->unit_id]=array('action'=>'sync_resync_unit_students','security'=>$security,'unit_id'=>$d->unit_id,'users' => array($d->user_id));
						}else{
							if(!in_array($d->user_id,$unit_data[$d->unit_id]['users']) && !empty($d->user_id)){
								$unit_data[$d->unit_id]['users'][] = $d->user_id;
							}
						}
					}
				}
				echo json_encode($unit_data);
			break;
			case 'course_forums':
				global $wpdb;
				$data = $wpdb->get_results("SELECT p.ID as course_id, m.meta_value as forum_id FROM {$wpdb->posts} as p LEFT JOIN {$wpdb->postmeta} as m ON p.ID = m.post_id WHERE p.post_type = 'course' AND p.post_status = 'publish' AND meta_key = 'vibe_forum'");
				$course_data = array();
				if(!empty($data)){
					$security = wp_create_nonce('sync_resync_course_forums');
					foreach($data as $d){
						if(!empty($d->forum_id)){
							$course_data[$d->course_id]=array('action'=>'sync_resync_course_forums','security'=>$security,'course_id'=>$d->course_id,'forum_id' => $d->forum_id);
						}
					}
				}
				echo json_encode($course_data);
			break;
			case 'instructor_commissions':
			break;
		}
		die();
	}

	function sync_resync_course_students(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync_course_students')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		$course_id = $_POST['course_id'];
		$users = $_POST['users'];
		$users_string = implode(',',$users);
		if(empty($users_string)){$users_string='0';}
		global $wpdb;

		//Remove Redundant users
		$c = $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id = $course_id AND meta_key REGEXP '^[0-9]+$' AND meta_value REGEXP '^[0-9]+$' AND meta_key NOT IN ($users_string)");
		if($c && is_numeric($c)){$this->deleted += $c;};

		$x = $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE user_id NOT IN ($users_string) AND meta_key = 'course_status$course_id'");	
		if($x && is_numeric($x)){$this->deleted += $x;};
		 
		

		if(!empty($users) && is_array($users)){
			foreach($users as $user_id){
				$status = get_user_meta($user_id,'course_status'.$course_id,true);
				if(empty($status)){
					update_user_meta($user_id,'course_status'.$course_id,1);
				}
				$course_marks = get_post_meta($course_id,$user_id,true);
				if(!isset($course_marks)){
					update_post_meta($course_id,$user_id,0);
				}
			}
		}
		die();
	}

	function end_course_students_sync(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		if(empty($this->deleted)){$this->deleted=0;}

		printf(__('%s redundant records removed. Course Student verification completed.','vibe-customtypes'),$this->deleted);
		die();
	}

	function sync_resync_quiz_results(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync_quiz_results')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		global $wpdb;
		$quiz_id = $_POST['quiz_id'];
		$users = $_POST['users'];
		if(!empty($users) && function_exists('bp_course_get_quiz_questions')){
			foreach($users as $user_id){
				$questions = bp_course_get_quiz_questions($quiz_id,$user_id);
				if(!empty($questions)){
		    		$quess=$questions['ques'];
		    		$marks=$questions['marks'];
		    		$ques_string = implode(',',$quess);
		    		$answer_ids = $wpdb->get_results("SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID IN ($ques_string) AND user_id = $user_id LIMIT 0,1");
		    		if(!empty($answer_ids)){
		    			foreach($answer_ids as $answer_id){
		    				update_comment_meta($answer_id->comment_ID,'quiz_id',$quiz_id);
		    			}
		    		}
		    	}
			}
		}
		die();
	}

	function end_quiz_results_sync(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		echo __('Quiz results sync complete !','vibe-customtypes');
		die();
	}

	function sync_resync_unit_students(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync_unit_students')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		global $wpdb;
		$unit_id = $_POST['unit_id'];
		$users = $_POST['users'];
		if(!empty($users) && function_exists('bp_course_get_unit_course_id')){
			$course_id = bp_course_get_unit_course_id($unit_id);

			if(!empty($course_id)){
				foreach($users as $user_id){
					$time = get_user_meta($user_id,$unit_id,true); 
					if(!empty($time)){
						bp_course_update_user_unit_completion_time($user_id,$unit_id,$course_id,$time);
						delete_user_meta($user_id,$unit_id);
					}
				}
			}
		}
		die();
	}

	function end_unit_students_sync(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		echo __('Course - Units - Student sync complete !','vibe-customtypes');
		die();
	}

	function sync_resync_course_forums(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync_course_forums')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		$course_id = $_POST['course_id'];
		$forum_id = $_POST['forum_id'];
		if(function_exists('bp_course_get_course_students') && !empty($forum_id) && function_exists('bbp_add_user_forum_subscription')){
			$instructor_id = get_post_field('post_author',$course_id);
			bbp_add_user_forum_subscription( $instructor_id, $forum_id);
			$students = bp_course_get_course_students($course_id,0,9999);
			if(!empty($students['students'])){
				foreach($students['students'] as $user_id){
				      bbp_add_user_forum_subscription( $user_id, $forum_id);
				}
			}
		}
		die();
	}

	function end_course_forums_sync(){
		if ( !isset($_POST['security']) || !wp_verify_nonce($_POST['security'],'sync_resync')){
	         _e('Security check Failed. Contact Administrator.','vibe-customtypes');
	        die();
		}
		echo __('Course - Forums sync complete !','vibe-customtypes');
		die();
	}
}

wplms_miscellaneous_settings::init();
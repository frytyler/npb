<?php
// global $wp_rewrite; $wp_rewrite->flush_rules();
if (!function_exists('add_action')) { header('Status: 403 Forbidden'); header('HTTP/1.1 403 Forbidden'); exit("This page can't be loaded outside of WordPress!"); }
define('NPB_TITLE', 'National Prayer Breakfast');
define('NPB_HOME', get_option("home"));
define('THEME_TEMPLATE_DIR', get_bloginfo('template_directory'));
define('THEME_TEMPLATE_DIR_SCRIPTS', THEME_TEMPLATE_DIR.'/scripts');
define('THEME_TEMPLATE_DIR_IMAGES', THEME_TEMPLATE_DIR.'/images');
define('THEME_TEMPLATE_DIR_TEMPLATES', THEME_TEMPLATE_DIR.'/templates');
define('ASSETS', NPB_HOME.'/wp-content/themes/NationalPrayerBreakfast/assets');
include_once(__DIR__.'/vendor/Parsedown.php');
if(!class_exists('NPB')):
class NPB {

	var $VERSION = '1.0';
	var $TWIG = NULL;
	var $PARSEDOWN = NULL;
	var $LANG = NULL;
	var $CONTACT_FORM = NULL;
	var $NEWS_LETTER_FORM = NULL;

	/* 
	 *	@function 	__construct
	 */
	public function __construct( )
	{
		global $wpdb;
		$this->LANGS = array('en','fr');
		register_nav_menus( array( 'Primary' => 'Main Navigation' ) );
		register_nav_menus( array( 'Top' => 'Secondary Navigation' ) );
		register_nav_menus( array( 'Footer' => 'Footer Navigation' ) );
		if ( defined( "SP_CORE_ACTIVE_FLAG" ) )
		{
			$this->TWIG = sp_new_twig_engine(__DIR__.'/templates');
		}

		// Markdown parser
		$this->PARSEDOWN = new Parsedown();

		$this->CONTACT_FORM = $wpdb->prefix . "contact_form";
		$this->NEWS_LETTER_FORM = $wpdb->prefix . "news_letter_form";
		$this->SPLASH_EMAIL = $wpdb->prefix . "splash_email_signup";
 	}

	/**
	 *	@function	reconfigure_dashboard
	 *	@params 	void
	 *	@return 	void
	 */	
	public function reconfigure_dashboard() {
		global $wp_meta_boxes;
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	}
	public function my_admin_notice( )
	{
    	if( !defined( "SP_CORE_ACTIVE_FLAG" ) )
    		echo '<div class="updated" style="font-size: 1.2em;"><p><a href="plugins.php">'.__('<strong>This theme requires the \'SP Core\' Plugin</strong> please click here to install').'</a></p></div>';
	}
	public function wp_enqueue_admin_scripts( )
	{
		wp_enqueue_script('jquery');
		wp_enqueue_media();
	}
	public function wp_enqueue_admin_styles( )
	{
		// wp_enqueue_style('wp', THEME_TEMPLATE_DIR_SCRIPTS.'/wp.css');
	}
	/**
	 *	@function	remove_submenus
	 *	@params 	void
	 *	@return 	void
	 */	
	public function remove_submenus() { global $submenu; unset($submenu['index.php'][10]); /*Removes 'Updates'*/ unset($submenu['edit.php'][16]); /*Removes 'Tags'*/ }

	public function remove_menu_items() {	
		global $menu,$current_user;
		get_currentuserinfo();
		$restricted = array(__('Links'), __('Comments'), __('Tools'));
		end ($menu);
		while (prev($menu)){
			$value = explode(' ',$menu[key($menu)][0]);
			if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
				unset($menu[key($menu)]);
			}
		}
	}

	/**
	 *	@function	delete_submenu_items
	 *	@params 	void
	 *	@return 	void
	 */	
	public function delete_submenu_items() {remove_submenu_page('themes.php', 'theme-editor.php');remove_submenu_page('plugins.php', 'plugin-editor.php');}

	/**
	 *	@function	widgets_and_sidebar
	 *	@params 	void
	 *	@return 	void
	 */
	public function widgets_and_sidebar(){
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Search');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_Categories');
		unregister_widget('WP_Widget_Links');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Recent_Posts');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Tag_Cloud');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_Text');
		if (function_exists('register_sidebar')) {
			register_sidebar(array(
				'name'          => 'General Sidebar',
				'id'            => 'sidebar-1',
				'description'   => __('This is the general sidebar', 'NPB'),
				'before_widget' => '<section class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>'
			));
			register_sidebar(array(
				'name'          => 'Homepage',
				'id'            => 'homepage_sidebar',
				'description'   => __('This is the sidebar below the feature area.', 'NPB'),
				'before_widget' => '<section class="widget box col_1of3 %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>'
			));
			register_sidebar(array(
				'name'          => 'Events',
				'id'            => 'events_sidebar',
				'description'   => __('This is the sidebar on the events page.', 'NPB'),
				'before_widget' => '<section class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>'
			));
		}
	}
	public function wp_enqueue_scripts( )
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('flexslider', ASSETS.'/scripts/flexslider/flexslider.js', array('jquery'));
		// wp_enqueue_script( "google-maps-v3", "https://maps.googleapis.com/maps/api/js?key={$this->MAPS_API_KEY}&amp;sensor=false" );
        wp_enqueue_script('app', ASSETS.'/scripts/app.js', array('jquery'));
	}

	public function wp_enqueue_styles( )
	{
		wp_enqueue_style('flexslider', ASSETS.'/scripts/flexslider/flexslider.css');
	}
	/**
	 *	@function	footer
	 *	@params 	void
	 *	@return 	void
	 */
	public function footer() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				app.init();
			});
		</script>
		<?php
	}

	public function admin_head()
	{
		?>
		 
		<style>
			#adminmenu .menu-icon-features div.wp-menu-image:before {
				content: '\f313';
			}
			#adminmenu .menu-icon-speakers div.wp-menu-image:before {
				content: "\f307";
			}
			#adminmenu .menu-icon-events div.wp-menu-image:before {
				content: "\f123";
			}
		</style>
		 
		<?php
	}

	/**
	 *	@function	admin_menu
	 *	@params 	<void>
	 *	@return 	<void>
	 */	
	public function admin_menu() { 
		@$this->remove_submenus();
		@$this->remove_menu_items();
		@$this->delete_submenu_items();
		@$this->menu_pages(); 
		@$this->add_posts_page_panels();
		@$this->theme_save_posts();
	}

	/* 
	 *	@function 	create_post_types
	 */
	
	public function create_post_types( )
	{
		register_post_type(
			'features', 
			array(
				'labels' => array(
					'name'=>__('Features'), 
					'singular_name'=>__('Feature')
				),
				'taxonomies'=> array('category'),
				'supports' => array('title','editor','thumbnail'), 
				'public'=>true, 
				'menu_position'=>5,  
				'rewrite'=>false,
				'menu_icon'=>''
			)
		);
		register_post_type(
			'events', 
			array(
				'labels' => array(
					'name'=>__('Events'), 
					'singular_name'=>__('Event')
				),
				'taxonomies'=> array('category'),
				'supports' => array('title','editor','thumbnail'), 
				'public'=>true, 
				'menu_position'=>5,  
				'rewrite'=>false,
				'menu_icon'=>''
			)
		);
		register_post_type(
			'speakers', 
			array(
				'labels' => array(
					'name'=>__('Speakers'), 
					'singular_name'=>__('Speaker')
				),
				'taxonomies'=> array('category'),
				'supports' => array('title','editor','thumbnail'), 
				'public'=>true, 
				'menu_position'=>5,  
				'rewrite'=>false,
				'menu_icon'=>''
			)
		);
	}
	/**
	 *	@function	add_posts_page_panels
	 *	@params 	void
	 *	@return 	void
	 */	
	public function add_posts_page_panels( )
	{
		if(function_exists('add_meta_box')) {
			$screens = array( 'post', 'page', 'features', 'events', 'speakers' );
			foreach( $screens as $screen):
				add_meta_box('npb_metatags_options',__('Metatag / Keyword Options'), array(&$this,'metatags_metabox'), $screen);
			endforeach;
			add_meta_box('npb_featureinfo',__('Feature Info'), array(&$this,'featureinfo_metabox'), 'features', 'normal', 'high');
			add_meta_box('npb_eventinfo',__('Event Info'), array(&$this,'eventinfo_metabox'), 'events', 'normal', 'high');
			add_meta_box('npb_speakerinfo',__('Speaker Info'), array(&$this,'speakerinfo_metabox'), 'speakers', 'side', 'high');
			add_meta_box('npb_pagecat',__('Post Category'), array(&$this,'pagecat_metabox'), 'page', 'side');
			add_meta_box('youtube_embed_code',__('YouTube ShortCode'), array(&$this,'youtube_embed_code'), 'post', 'side', 'low');
		}
	}
	public function metabox_styles()
	{
		$template = array();
		$template["ids"] = array(
			"npb_metatags_options",
			"npb_featureinfo",
			"npb_speakerinfo",
			"npb_eventinfo",
			"npb_pagecat"
		);
		echo $this->TWIG->render('metabox.css.twig', $template);
	}
	/**
	 *	@function	theme_save_posts
	 *	@params 	void
	 *	@return 	void
	 */	
	public function theme_save_posts( ) 
	{
		add_action('save_post', array(&$this,'metatags_save_postdata'));
		add_action('save_post', array(&$this,'featureinfo_save_postdata'));
		add_action('save_post', array(&$this,'eventinfo_save_postdata'));
		add_action('save_post', array(&$this,'speakerinfo_save_postdata'));
		add_action('save_post', array(&$this,'pagecat_save_postdata'));
	}

	public function youtube_embed_code() {
		global $wpdb;
		?>
		<p>Did you want to embed?</p>
        <ol>
        	<li>Grab the ID of the YouTube video EX:<small>(http://youtu.be/<strong>...</strong>)</small> <strong>"..." is the ID</strong></li>
            <li>Replace the "ID GOES HERE" in the sample snippet below with the actual YouTube ID</li>
            <li>Copy and Paste that entire snippet into the POST / PAGE content area</li>
        </ol>
        <p><input type="text" name="youtube_embed" value="&#91;youtube&#93;ID GOES HERE&#91;&#47;youtube&#93;" class="input widefat" /></p>
		<?php			
	}
	
	public function metatags_metabox( )
	{
		$template = array( );
		$template["field"] = get_post_meta($_REQUEST["post"], 'meta_keywords',true);
		$template["langs"] = $this->LANGS;
		wp_nonce_field('npb_meta_keywords_action', 'npb_meta_keywords_noncename');
		echo $this->TWIG->render('metabox.metatags.twig',$template);
	}
	public function metatags_save_postdata( $post_id )
	{
		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!wp_verify_nonce($_POST['npb_meta_keywords_noncename'], 'npb_meta_keywords_action')) return;
		if ('page' == $_POST['post_type']) { if (!current_user_can('edit_page', $post_id)) return; }
		else { if (!current_user_can('edit_post', $post_id)) return; }
		$fields = array( );
		foreach($this->LANGS AS $lang):
			$fields[$lang]["meta_keywords"] = sp_clean_post($_POST['meta_keywords_'.$lang]);
		endforeach;
		if( empty( $fields ) )
			delete_post_meta($post_id, 'meta_keywords');
		else
			update_post_meta($post_id, 'meta_keywords', $fields);
	}

	public function featureinfo_metabox( )
	{
		$template = array( );
		$template["field"] = get_post_meta($_REQUEST["post"], 'featureinfo', true);
		$template["langs"] = $this->LANGS;
		wp_nonce_field('npb_featureinfo_action', 'npb_featureinfo_noncename');
		echo $this->TWIG->render('metabox.featureinfo.twig',$template);
	}

	public function featureinfo_save_postdata( $post_id )
	{
		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!wp_verify_nonce($_POST['npb_featureinfo_noncename'], 'npb_featureinfo_action')) return;
		if ('features' == $_POST['post_type']) { if (!current_user_can('edit_page', $post_id)) return; }
		else { if (!current_user_can('edit_post', $post_id)) return; }
		$fields = array( );
		foreach($this->LANGS as $lang):
			$fields[$lang]["feature_title"] = sp_clean_post($_POST["feature_title_".$lang]);
			$fields[$lang]["feature_description"] = sp_clean_post($_POST["feature_description_".$lang]);
			$fields[$lang]["btn_text"] = sp_clean_post($_POST["btn_text_".$lang]);
			$fields[$lang]["btn_link"] = sp_clean_post($_POST["btn_link_".$lang]);
			$fields[$lang]["image_link"] = sp_clean_post($_POST["image_link_".$lang]);
			$fields[$lang]["youtube_link"] = sp_clean_post($_POST["youtube_link_".$lang]);

			// Store the youtube id if it's available.
			if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', sp_clean_post($_POST["youtube_link_".$lang]), $match)) {
			    $fields[$lang]["youtube_id"] = $match[1];
			}

		endforeach;
		$fields["color"] = sp_clean_post($_POST["color"]);
		$fields["alignment"] = sp_clean_post($_POST["alignment"]);
		if( empty( $fields ) )
			delete_post_meta($post_id, 'featureinfo');
		else
			update_post_meta($post_id, 'featureinfo', $fields);
	}

	public function eventinfo_metabox( )
	{
		$template = array( );
		$template["field"] = get_post_meta($_REQUEST["post"], 'eventinfo', true);
		$template["langs"] = $this->LANGS;
		wp_nonce_field('npb_eventinfo_action', 'npb_eventinfo_noncename');
		echo $this->TWIG->render('metabox.eventinfo.twig',$template);
	}

	public function eventinfo_save_postdata( $post_id )
	{
		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!wp_verify_nonce($_POST['npb_eventinfo_noncename'], 'npb_eventinfo_action')) return;
		if ('events' == $_POST['post_type']) { if (!current_user_can('edit_page', $post_id)) return; }
		else { if (!current_user_can('edit_post', $post_id)) return; }
		$fields = array( );
		foreach($this->LANGS as $lang):
			$fields[$lang]['location_name']    = sp_clean_post($_POST['location_name_'.$lang]);
			$fields[$lang]['location_link']    = $_POST['location_link_'.$lang];
			$fields[$lang]['location_address'] = $_POST['location_address_'.$lang];
			$fields[$lang]['date']             = sp_clean_post($_POST['date_'.$lang]);
			$fields[$lang]['button_text']      = sp_clean_post($_POST['button_text_'.$lang]);
			$fields[$lang]['button_link']      = sp_clean_post($_POST['button_link_'.$lang]);
		endforeach;
		if( empty( $fields ) )
			delete_post_meta($post_id, 'eventinfo');
		else
			update_post_meta($post_id, 'eventinfo', $fields);
	}

	public function speakerinfo_metabox( )
	{
		$template = array( );
		$template["field"] = get_post_meta($_REQUEST["post"], 'speakerinfo', true);
		$template["langs"] = $this->LANGS;
		wp_nonce_field('npb_speakerinfo_action', 'npb_speakerinfo_noncename');
		echo $this->TWIG->render('metabox.speakerinfo.twig',$template);
	}

	public function speakerinfo_save_postdata( $post_id )
	{
		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!wp_verify_nonce($_POST['npb_speakerinfo_noncename'], 'npb_speakerinfo_action')) return;
		if ('speakers' == $_POST['post_type']) { if (!current_user_can('edit_page', $post_id)) return; }
		else { if (!current_user_can('edit_post', $post_id)) return; }
		$fields = array( );
		foreach($this->LANGS as $lang):
			$fields[$lang]['twitter_link'] = sp_clean_post($_POST["twitter_link_".$lang]);
			$fields[$lang]['facebook_link'] = sp_clean_post($_POST["facebook_link_".$lang]);
			$fields[$lang]['position'] = sp_clean_post($_POST["position_".$lang]);
		endforeach;
		if( empty( $fields ) )
			delete_post_meta($post_id, 'speakerinfo');
		else
			update_post_meta($post_id, 'speakerinfo', $fields);
	}

	public function pagecat_metabox( )
	{
		$template = array( );
		$template["field"] = get_post_meta($_REQUEST["post"], 'pagecat', true);
		if (empty($template["field"])) {
			$template["field"] = array();
		}
		wp_nonce_field('npb_pagecat_action', 'npb_pagecat_noncename');
		ob_start();
		wp_terms_checklist('', array('selected_cats' => $template["field"]["catids"]));
		$template["cats"] = ob_get_contents();
		ob_end_clean();
		echo $this->TWIG->render('metabox.pagecat.twig',$template);
	}

	public function pagecat_save_postdata( $post_id )
	{
		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!wp_verify_nonce($_POST['npb_pagecat_noncename'], 'npb_pagecat_action')) return;
		// if ('page' == $_POST['post_type']) { if (!current_user_can('edit_page', $post_id)) return; }
		else { if (!current_user_can('edit_post', $post_id)) return; }
		$fields = array( );
		$fields["catids"] = $_POST["post_category"];
		if( empty( $fields ) )
			delete_post_meta($post_id, 'pagecat');
		else
			update_post_meta($post_id, 'pagecat', $fields);
	}

	/**
	 *	@function	menu_pages
	 *	@params 	<void>
	 *	@return 	<void>
	 */	
	public function menu_pages() {
		add_menu_page('General Settings', 'General Settings', 'administrator', 'general_settings', array($this,'menu_page_general'));
		add_menu_page('How to', 'How to', 'administrator', 'help_area', array($this,'menu_page_howto'), 'dashicons-lightbulb');
	}

	public function menu_page_howto( )
	{
		if( class_exists( 'SP_BLOCK' )):
			$block = new SP_BLOCK();
			$block->add_tab('panel_gallery_help','Creating a Gallery',array(&$this,'panel_gallery_help'));
			$block->add_tab('panel_tags_help','HTML Tags',array(&$this,'panel_tags_help'));
			$block->add_tab('panel_page_help','Creating a Page',array(&$this,'panel_page_help'));
			$block->add_tab('panel_menu_help','Creating a Menu',array(&$this,'panel_menu_help'));
			$block->add_tab('panel_splash_help','Splash Page Help',array(&$this,'panel_splash_help'));
			$block->add_tab('panel_feature_help','Feature Image',array(&$this,'panel_feature_help'));
			$block->draw_block('How to...','Theme: National Prayer Breakfast','Step by step guides to building the site');
		else:
			echo '<div id="message" class="updated">Please install SP Core</div>';
		endif;
	}

	public function panel_page_help( )
	{
		$markdown = $this->TWIG->render('help/panel.page.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}	

	public function panel_menu_help( )
	{
		$markdown = $this->TWIG->render('help/panel.menu.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}	

	public function panel_tags_help( )
	{
		$markdown = $this->TWIG->render('help/panel.tags.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}

	public function panel_splash_help( )
	{
		$markdown = $this->TWIG->render('help/panel.splash.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}

	public function panel_gallery_help( )
	{
		$markdown = $this->TWIG->render('help/panel.gallery.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}

	public function panel_feature_help( )
	{
		$markdown = $this->TWIG->render('help/panel.feature.twig', array('images' => ASSETS.'/images/help/'));
		echo $this->PARSEDOWN->text($markdown); 
	}

	/**
	 *	@function	menu_page_general
	 *	@params 	<void>
	 *	@return 	<void>
	 */	
	public function menu_page_general()
	{
		if( class_exists( 'SP_BLOCK' )):
			$block = new SP_BLOCK();
			$block->add_tab('panel_site_settings','Site Settings',array(&$this,'panel_site_settings'));
			$block->add_tab('panel_feature_settings','Feature Settings',array(&$this,'panel_feature_settings'));
			$block->add_tab('panel_contact_settings','Contact Settings',array(&$this,'panel_contact_settings'));
			$block->add_tab('panel_newsletter_settings','News Letter Settings',array(&$this,'panel_newsletter_settings'));
			$block->add_tab('panel_social_settings','Social Media Links',array(&$this,'panel_social_settings'));
			$block->add_tab('panel_splash_settings','Splash Page Settings',array(&$this,'panel_splash_settings'));
			$block->draw_block('General Settings','Theme: National Prayer Breakfast','General theme configuration');
		else:
			echo '<div id="message" class="updated">Please install SP Core</div>';
		endif;		
	}

	public function panel_site_settings( )
	{
		$template = array( );
		$template["msg"] = NULL;
		$template["hidden_field_name"] = md5("site_settings");
		$template["langs"] = $this->LANGS;
		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$fields = array( );
			foreach($this->LANGS as $lang):
				$fields[$lang]['site_title'] = @sp_clean_post($_POST['site_title_'.$lang]);
				$fields[$lang]['by_line'] = @sp_clean_post($_POST['by_line_'.$lang]);
			endforeach;
			$fields['show_ribbon'] = ($_POST['show_ribbon'] ? 1 : 0);
			update_option('npb_site_settings', $fields);
			$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => 'Site settings were successfully updated.'));
		}
		$template["field"] = get_option('npb_site_settings');
		echo $this->TWIG->render('panel.site.twig', $template);
	}

	public function panel_feature_settings()
	{
		$template = array( );
		$template["msg"] = NULL;
		$template["hidden_field_name"] = md5("feature_settings");
		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$fields = array( );
			$fields["feature_speed"] = @sp_clean_post($_POST["feature_speed"]);
			$fields["feature_count"] = @sp_clean_post($_POST["feature_count"]);
			update_option('npb_feature_options', $fields);
			$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => 'Feature settings were successfully updated.'));
		}
		$template["field"] = get_option('npb_feature_options');
		echo $this->TWIG->render('panel.feature.twig', $template);
	}

	public function panel_social_settings( )
	{
		$template                      = array( );
		$template["msg"]               = NULL;
		$template["hidden_field_name"] = md5("social_settings");
		$template["sites"]             = array( "Twitter", "Facebook", "Instagram", "Youtube" );
		$template["langs"]             = $this->LANGS;

		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$fields = array( );
			foreach($this->LANGS as $lang):
				foreach($template["sites"] as $site):
					$site = strtolower($site);
					$fields[$lang][$site] = @sp_clean_post($_POST[$site.'_'.$lang]);
				endforeach;
			endforeach;
			update_option('npb_social_options', $fields);
			$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => 'Social settings were successfully updated.'));
		}
		$template["field"] = get_option('npb_social_options');
		echo $this->TWIG->render('panel.social.twig', $template);
	}

	public function panel_contact_settings( )
	{
		$template = array( );
		$template["msg"] = NULL;
		$template["hidden_field_name"] = md5("contact_settings");
		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$fields = array( );
			$fields["to_names"] = @sp_clean_post($_POST["contact_to_names"]);
			$fields["to_emails"] = @sp_clean_post($_POST["contact_to_emails"]);
			foreach($this->LANGS as $lang):
				$fields[$lang]["button_text"] = @sp_clean_post($_POST["contact_button_text_".$lang]);
				$fields[$lang]["success_msg"] = @sp_clean_post($_POST["contact_success_msg_".$lang]);
				$fields[$lang]["error_msg"] = @sp_clean_post($_POST["contact_error_msg_".$lang]);
			endforeach;
			update_option('npb_contact_options', $fields);
			$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => 'Contact settings were successfully updated.'));
		}
		$template["field"] = get_option('npb_contact_options');
		$template["langs"] = $this->LANGS;
		echo $this->TWIG->render('panel.contact.twig', $template);
	}

	public function panel_newsletter_settings( )
	{
		echo '<form method="post">
				<input type="hidden" name="news_letter_form_download" value="1">
				<p>CSV of News Letter widget submissions: <button type="submit" class="button button-primary">Download</button></p>
			</form>';
	}

	public function button_shortcode($atts, $content=NULL) {
		extract(shortcode_atts(array('href' => '', 'type' => ''),$atts));
		$button .= '<p><a href="'. $href .'" class="btn '.$type.'">'. $content .'</a></p>';
		return $button;
	}

	public function panel_splash_settings()
	{
		$template = array( );
		$template["msg"] = NULL;
		$template["hidden_field_name"] = md5("splash_settings");
		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$fields                    = array( );
			$fields["desktop_banner"]  = @sp_clean_post($_POST["desktop_banner"]);
			$fields["mobile_banner"]   = @sp_clean_post($_POST["mobile_banner"]);
			$fields["error_message"]   = @sp_clean_post($_POST["error_message"]);
			$fields["success_message"] = @sp_clean_post($_POST["success_message"]);
			$fields["message"]         = $_POST["message"];
			$fields["email_label"]     = @sp_clean_post($_POST["email_label"]);
			$fields["fullname_label"]  = @sp_clean_post($_POST["fullname_label"]);
			$fields["button_label"]    = @sp_clean_post($_POST["button_label"]);
			$fields["to_name"]         = @sp_clean_post($_POST["to_name"]);
			$fields["to_email"]        = @$_POST["to_email"];

			if (!sp_verify_email_addresses($_POST["to_email"])) {
				$emailmsg = '<strong>NOTE: E-mail addresses are invalid</strong>';
			}
			update_option('npb_splash_settings', $fields);
			$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => 'Splash settings were successfully updated. ' . $emailmsg));
		}
		$template["field"] = get_option('npb_splash_settings');
		$template["field"]["message"] = stripslashes($template["field"]["message"]);
		echo $this->TWIG->render('panel.splash.twig', $template);
	}

	public function get_splash_form()
	{
		global $wpdb;
		$settings                        = get_option('npb_splash_settings');
		$template                        = array( );
		$fields                          = array( );
		$fields["hidden_field"]["id"]    = 'splash_form';
		$fields["hidden_field"]["name"]  = md5('splash_form');
		$fields["hidden_field"]["value"] = 1;
		$fields["fullname"]["id"]        = 'splash_name';
		$fields["fullname"]["name"]      = 'splash_name';
		$fields["fullname"]["value"]     = '';
		$fields["fullname"]["label"]     = $settings["fullname_label"];
		$fields["email"]["id"]           = 'splash_email';
		$fields["email"]["name"]         = 'splash_email';
		$fields["email"]["value"]        = '';
		$fields["email"]["label"]        = $settings["email_label"];
		$template["fields"]              = $fields;
		$template["desktop_banner"]      = $settings["desktop_banner"];
		$template["mobile_banner"]       = $settings["mobile_banner"];
		$template["message"]             = stripslashes($settings["message"]);
		$template["button_text"]         = $settings["button_label"];
		$template["msg"]                 = '';

		if ( isset( $_POST[$fields["hidden_field"]["name"]] )) {
			$name  = @sp_clean_post($_POST["splash_name"]);
			$email = is_email($_POST["splash_email"]);
			if ($email) {
				$user = $wpdb->get_results("SELECT * FROM {$this->SPLASH_EMAIL} WHERE fullname = '$name' AND email = '$email';");
				if ( empty($user) ) {
					// do insert into Email Signup table.
					$new_email_signup = $wpdb->insert(
						$this->SPLASH_EMAIL,
						array(
							'fullname'  => $name,
							'email' => $email
						),
						array( '%s', '%s' )
					);
					// Send out email if notifications are on.
					if ($settings["to_email"]) {
						$send_email = sp_get_email_addresses($settings["to_name"],$settings["to_email"]);
						$headers = sp_mail_headers('System Notification','no-replay@canadaprayerbreakfast.ca');
						$headers = $send_email["cc"] . $headers;
						$message ="<b>Name</b>: $name<br /><b>Email</b>: $email<br />";
						$mail = wp_mail($send_email["to"], "Email Signup Form", $message, $headers);
					}
					if (1 == $new_email_signup) {
						$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => $settings["success_message"]));	
						$template["fields"]["fullname"]["value"] = $template["fields"]["email"]["value"] = '';
						$_POST = array( );
					}
				} else {
					$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'warning', 'msg' => 'E-mail has already been entered.'));	
				}
			} else {
				$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'danger', 'msg' => $settings["error_message"]));
				$template["fields"]["fullname"]["value"] = $_POST["splash_name"];
				$template["fields"]["email"]["value"] = $_POST["splash_email"];
			}
		}

		echo $this->TWIG->render('section.splashform.twig', $template);
	}

	public function downloads( )
	{
		global $wpdb;
		if (isset($_POST["contact_form_download"])) { 
			$result = $wpdb->get_results("SELECT name AS Name, email AS Email, postalcode AS PostalCode, message AS Message FROM $this->CONTACT_FORM ORDER BY id ASC;", ARRAY_A);
			@$this->form_extraction($result, 'National-Prayer-Breakfast-Contact-Form'); exit(); 
		}
		if (isset($_POST["news_letter_form_download"])) { 
			$result = $wpdb->get_results("SELECT name AS Name, email AS Email, postalcode AS PostalCode FROM $this->NEWS_LETTER_FORM ORDER BY id ASC;", ARRAY_A);
			@$this->form_extraction($result, 'National-Prayer-Breakfast-News-Letter-Form'); exit(); 
		}
		if (isset($_POST["splash_page_email_download"])) { 
			$result = $wpdb->get_results("SELECT fullname AS Name, email AS Email FROM $this->SPLASH_EMAIL ORDER BY id ASC;", ARRAY_A);
			@$this->form_extraction($result, 'National-Prayer-Breakfast-Splash-Page-Signup'); exit(); 
		}
	}

	public function form_extraction($result, $name) {
	    // Used for mock times
	    global $wpdb;
	    $date = new DateTime();
	    $ts = $date->format( 'Y-m-d H:i:s' );

	    // A name with a time stamp, to avoid duplicate filenames
	    $filename = $name."-$ts.csv";

	    // Tells the browser to expect a CSV file and bring up the
	    // save dialog in the browser
	    header( 'Content-Type: text/csv' );
	    header( 'Content-Disposition: attachment;filename='.$filename);

	    // This opens up the output buffer as a "file"
	    $fp = fopen('php://output', 'w');

	    // Get the first record
	    $hrow = $result[0];

	    // Extracts the keys of the first record and writes them
	    // to the output buffer in CSV format
	    fputcsv($fp, array_keys($hrow));

	    // Then, write every record to the output buffer in CSV format
	    foreach ($result as $data) {
	        fputcsv($fp, $data);
	    }

	    // Close the output buffer (Like you would a file)
	    fclose($fp);
	}

	public function db_install() 
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$is_contact_form_created = get_option('contact_form');
		$is_news_letter_created = get_option('newsletter_form');
		$is_splash_table_created = get_option('splash_email_table');
		// echo $is_splash_table_created;
		if ($is_contact_form_created !== 0) {
			$sql = "CREATE TABLE " . $this->CONTACT_FORM . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`email` varchar(120) NOT NULL,
				`name` varchar(120) NOT NULL,
				`postalcode` varchar(10) NOT NULL,
				`message` LONGTEXT NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			dbDelta($sql);
			update_option('contact_form', 1);			
		}
		if ($is_news_letter_created !== 0) {
			$sql = "CREATE TABLE " . $this->NEWS_LETTER_FORM . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`email` varchar(120) NOT NULL,
				`postalcode` varchar(10) NOT NULL,
				`name` varchar(120) NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			dbDelta($sql);
			update_option('newsletter_form', 1);			
		}
		if ($is_splash_table_created !== 1) {
			$sql = "CREATE TABLE " . $this->SPLASH_EMAIL . " (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`email` varchar(120) NOT NULL,
				`fullname` varchar(120) NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			dbDelta($sql);
			update_option('splash_email_table', 1);			
		}
	}

	/*
 	 *	FRONTEND FUNCTIONS
	 */

	public function get_news_image( $id, $size='medium' ) 
	{
		$files = get_children('post_parent='.$id.'&post_type=attachment&post_mime_type=image');
		if($files) :
			$keys = array_reverse(array_keys($files));
			$j=0;
			$num = $keys[$j];
			$image=wp_get_attachment_image($num,$size,false);
			$imagepieces = explode('"', $image);
			$imagepath = $imagepieces[1];
			$thumb=wp_get_attachment_thumb_url($num);
			$temp = $thumb;
		endif;
		return $temp;
	}

	public function get_menu($location = 'Primary', $container_class = 'main-nav') {
		$args = array(
			'theme_location' => $location,
			'container' => 'nav',
			'container_class' => 'nav '. $container_class,
			'echo' => 0,
			'depth' => 2,
			'menu_class' => 'nav '. $container_class,
		);
		return wp_nav_menu( $args );
	}

	public function get_language_toggle( )
	{
		$template = array( );
		if (function_exists('pll_the_languages')) {
			$template["anchor"] = pll_the_languages(['echo' => 0, 'hide_current' => 1, 'post_id' => get_the_ID()]);
		}
		echo $this->TWIG->render('section.language.twig', $template);
	}
	
	public function get_logo($location = "header")
	{
		$link = @sp_translate(NPB_HOME,NPB_HOME.'?lang=fr');
		$logo_en = 'National Prayer <br /><span class="larger">Breakfast</span>';
		$logo_fr = 'Le Petit déjeuner de<br/<span class="larger">Prière national</span>';
		$crest = '';
		if ($location == 'header') {
			$crest = '<span class="crest"></span>';
		} 
		$logo = '<h1 class="logo '.sp_translate('en','fr').'">'.$crest.@sp_translate($logo_en,$logo_fr).'<span class="flag"></span></h1>';
		return $logo;
	}

	public function get_footer()
	{
		$template = array( );
		$site_settings = get_option('npb_site_settings');
		$template["SiteTitle"] = @sp_translate($site_settings['en']['site_title'],$site_settings['fr']['site_title']);
		$template["ByLine"] = @sp_translate($site_settings['en']['by_line'],$site_settings['fr']['by_line']);
		$template["SiteMap"] = $this->get_menu('Footer', 'footer-nav');
		$template["sites"] = array( "twitter", "facebook", "instagram", "youtube" );
		$template["logo"] = @$this->get_logo('');
		
		$social_links = get_option('npb_social_options');
		$template["social"] = @sp_translate($social_links['en'],$social_links['fr']);
		echo $this->TWIG->render("section.footer.twig", $template);
	}
	
	public function get_meta_keywords( )
	{
		global $post;
		if (is_404()) return;
		if ( is_front_page( ) ) return;
		$postmeta = get_post_meta($post->ID, 'meta_keywords', true);
		$keywords = htmlspecialchars(trim( @sp_translate( $postmeta['en']['meta_keywords'], $postmeta['fr']['meta_keywords'] ) ));
		$temp = NULL;
		if ($keywords) { $temp = chr(13).'<meta name="keywords" content="'.$keywords.'" />'.chr(13).chr(10); }
		return $temp;
	}

	public function get_queried_posts( $args = NULL, $file = NULL, $customtwig = NULL, $force_return = NULL )
	{
		global $post, $q_config;
		$template = array( );
		$query = new WP_Query( $args );
		$x=0;
		while ($query->have_posts()) : $query->the_post();
			$template["posts"][$x]["id"]        = get_the_id();
			$template["posts"][$x]["title"]     = get_the_title();
			$template["posts"][$x]["date"]      = strtotime( $query->post->post_date );  //get_the_date( );
			$template["posts"][$x]["link"]      = get_permalink();
			$template["posts"][$x]["excerpt"]   = get_the_excerpt();
			$template["posts"][$x]["content"]   = do_shortcode(get_the_content());
			if (class_exists('MultiPostThumbnails')) :
				$image_fr = wp_get_attachment_url( MultiPostThumbnails::get_post_thumbnail_id(get_post_type(),'featured-image-fr',$post->ID));
			endif;
			$image_en = wp_get_attachment_url( get_post_thumbnail_id($query->post->ID) );
			$image_fr = ($image_fr != '') ? $image_fr : $image_en;
			$template["posts"][$x]["thumbnail"] = @$this->get_news_image( $query->post->ID );
			$template["posts"][$x]["image"]     = @sp_translate($image_en, $image_fr);
			$post_meta_keys = get_post_custom_keys();
			$y=0;
			if(!empty($post_meta_keys))
			{
				foreach($post_meta_keys as $key):
					$valuet = trim($key);
				    if ( '_' == $valuet{0} ) continue;
					$template["posts"][$x][$key] = get_post_meta($post->ID, $key, true);
					$y++;
				endforeach;
			}
			$x++;
		endwhile; wp_reset_postdata();
		
		$nextlinktext = "Older Articles";
		$prevlinktext = "Newer Articles";

		$template["pagination"] = '
        <ul class="pager"> 
            <li class="previous">'.get_next_posts_link( '&larr; ' . $nextlinktext, $query->max_num_pages).'</li>
            <li class="next">'.get_previous_posts_link( $prevlinktext . ' &rarr;', $query->max_num_pages).'</li>  
        </ul>';

		$template["lang"] = 'en';
		if(function_exists('pll_current_language')) {
			$template["lang"] = pll_current_language();
		}

		if ( $customtwig )
			$template["settings"] = $customtwig;

		if($file)
			$temp = $this->TWIG->render($file,$template);
		else
			$temp = $this->TWIG->render('section.post.twig',$template);

		if ( $force_return == 'return' )
			return $temp;
		else 
			echo $temp;
	}

	public function get_single( $post, $file = NULL, $force_return = NULL )
	{
		global $q_config;
		$template = array();
		$template["posts"][0]["title"]     = get_the_title( $post->ID );
		$template["posts"][0]["date"]      = strtotime( $post->post_date );  //get_the_date( );
		$template["posts"][0]["link"]      = get_permalink( $post->ID );
		$template["posts"][0]["excerpt"]   = get_the_excerpt( $post->ID );
		$template["posts"][0]["content"]   = do_shortcode( get_the_content( $post->ID ) );
		if (class_exists('MultiPostThumbnails')) :
		    $image_fr = wp_get_attachment_url( MultiPostThumbnails::get_post_thumbnail_id(get_post_type(),'featured-image-fr',$post->ID));
		endif;
		$image_en = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		$image_fr = ($image_fr != '') ? $image_fr : $image_en;
		$template["posts"][0]["image"] = @sp_translate($image_en, $image_fr);
		$template["posts"][0]["thumbnail"] = @$this->get_news_image( $post->ID );
		// $template["posts"][0]["image"]     = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		
		$post_meta_keys = get_post_custom_keys($post->ID);
		if(!empty($post_meta_keys))
		{
			foreach($post_meta_keys as $key):
				$valuet = trim($key);
			    if ( '_' == $valuet{0} ) continue;
				$template["posts"][0][$key] = get_post_meta($post->ID, $key, true);
			endforeach;
		}

		$template["pagination"] = NULL;
		$template["issingle"] = true;
		$template["lang"] = ($q_config['language']) ? $q_config['language']: 'en';

		if($file)
			$temp = $this->TWIG->render($file,$template);
		else
			$temp = $this->TWIG->render('section.post.twig',$template);

		if ( $force_return == 'return' )
			return $temp;
		else 
			echo $temp;
	}

	public function get_post_meta( $post )
	{
		$temp = array( );
		$post_meta_keys = get_post_custom_keys($post->ID);
		if(!empty($post_meta_keys))
		{
			foreach($post_meta_keys as $key):
				$valuet = trim($key);
			    if ( '_' == $valuet{0} ) continue;
				$temp[0][$key] = get_post_meta($post->ID, $key, true);
			endforeach;
		}
		return $temp;
	}

	public function get_event( $post )
	{
		$template = array( );
		$template["event"] = $this->get_single($post, 'section.event.twig', 'return');

		if (class_exists('MultiPostThumbnails')) :
		    $image_fr = wp_get_attachment_url( MultiPostThumbnails::get_post_thumbnail_id('events','featured-image-fr',$post->ID));
		endif;
		$image_en = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		$image_fr = ($image_fr != '') ? $image_fr : $image_en;
		$template["banner"] = @sp_translate($image_en, $image_fr);

		// Collect Sidebar into template variable. 
		ob_start();
		get_sidebar('events');
		$template["sidebar"] = ob_get_contents();
		ob_end_clean();
		

		$args = array("post_type" => "speakers", "orderby" => "date", "order" => "DESC");
		$template["speakers"] = $this->get_queried_posts($args, 'section.speaker.twig', NULL, 'return');
		
		echo $this->TWIG->render('section.event.page.twig', $template);
	}

	public function get_feature()
	{
		$settings = get_option('npb_feature_options');
		$args = array("post_type" => "features", "posts_per_page" => $settings["feature_count"], "orderby" => "date", "order" => "DESC");
		$this->get_queried_posts($args, 'section.feature.twig', $settings);
	}

	public function get_ribbon($location = NULL)
	{
		$option = get_option('npb_site_settings');
		if ($option['show_ribbon'] == 1) {
			echo '<span class="ribbon '. $location .'"></span>';
		}
	}

	public function get_contact_page( )
	{
		global $wpdb;
		$template = array( );
		$template["hidden_field_name"] = md5("contact_form");
		$settings = get_option('npb_contact_options');
		$template["post"] = $_POST;
		$success = @sp_translate($settings['en']["success_msg"],$settings['fr']["success_msg"]);
		$error = @sp_translate($settings['en']["error_msg"],$settings['fr']["error_msg"]);
		$template["button_text"] = @sp_translate($settings['en']["button_text"],$settings['fr']["button_text"]);
		if( isset( $_POST[$template["hidden_field_name"]] ) )
		{
			$name       = sp_clean_post($_POST["contact_name"]);
			$email      = (is_email($_POST["contact_email"])) ? $_POST["contact_email"] : false;
			$postalcode = strtoupper(sp_clean_post($_POST["contact_postalcode"]));
			$message    = sp_clean_post($_POST["contact_message"]);
			if( $name && $email && $postalcode && $message ) 
			{

				$new_contact_form = $wpdb->insert(
					$this->CONTACT_FORM,
					array(
						'name' => $name,
						'email' => $email,
						'postalcode' => $postalcode,
						'message' => $message
					),
					array( '%s', '%s', '%s', '%s' )
				);

				$send_email = @sp_get_email_addresses($settings["to_names"],$settings["to_emails"]);
				$headers = @sp_mail_headers($name,$email);
				$headers = $send_email["cc"] . $headers;
				$message ="<b>Name</b>: $name<br /><b>Email</b>: $email<br /><b>Postal Code</b>: $postalcode<br /><br />$message";
				$mail = wp_mail($send_email["to"], "Contact Form Submission", $message, $headers);
				if($mail)
				{
					$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => $success));
					$template["post"] = '';
				}
			} else {
				$template["msg"] = $this->TWIG->render('partial.msg.twig', array('type' => 'error', 'msg' => $error));
			}
		}

		$label               = array( );
		$label["name"]       = @sp_translate('Name','Nom');
		$label["email"]      = @sp_translate('Email','Courriel');
		$label["postalcode"] = @sp_translate('Postal Code','Code postal');
		$label["message"]    = @sp_translate('Message','Message');
		$template["label"]   = $label;

		echo $this->TWIG->render('section.contactform.twig', $template);
	}

	public function youtube_embed( $atts, $content=NULL ) {
		extract(shortcode_atts(array('width'=>'100%','height'=>'250px',),$atts));
		$video = trim($content);
		$id = get_the_ID();
		$embed .= '<iframe id="ytplayer'.$id.'" type="text/html" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video.'?controls=0&rel=0&showinfo=0&theme=light" frameborder="0" allowfullscreen></iframe>';
		return $embed;
	}

} 
$NPB = new NPB();
global $NPB;
else : exit("Class 'NPB' already exists"); endif;
if( isset( $NPB ) && is_admin( ) )
{
	add_action('admin_notices', array(&$NPB,'my_admin_notice'));
}
if (isset($NPB)) {
	if (is_admin()) {
		/* ADMIN ACTIONS */
		add_action('admin_enqueue_scripts', array(&$NPB, 'wp_enqueue_admin_scripts'));
		add_action('admin_enqueue_scripts', array(&$NPB,'wp_enqueue_admin_styles'));
		add_action('admin_menu', array(&$NPB, 'admin_menu'));
		add_action('admin_head', array(&$NPB, 'admin_head'));
		add_action('admin_head', array(&$NPB, 'metabox_styles'));
		add_action('admin_init', array(&$NPB, 'db_install'));
		add_action('admin_init', array(&$NPB, 'downloads'));
		$types = array('post','page','features','events','speakers');
		add_theme_support('post-thumbnails', $types);
		if (class_exists('MultiPostThumbnails')) {
			foreach($types as $type):
			    new MultiPostThumbnails(
			        array(
			            'label' => 'Featured Image (FR)',
			            'id' => 'featured-image-fr',
			            'post_type' => $type
			        )
			    );
			endforeach;
		}
	}
	/* GLOBAL ACTIONS */
	add_action('init', array(&$NPB, 'create_post_types'));
	add_action('widgets_init', array(&$NPB, 'widgets_and_sidebar'));
	add_action('wp_print_scripts', array(&$NPB, 'wp_enqueue_scripts'));
    add_action('wp_print_styles', array(&$NPB, 'wp_enqueue_styles'));
    add_action('wp_footer', array(&$NPB, 'footer'));
	add_action('wp_dashboard_setup', array(&$NPB, 'reconfigure_dashboard'));
	add_shortcode('button', array(&$NPB, 'button_shortcode'));
	add_shortcode('youtube', array(&$NPB, 'youtube_embed'));
}
require_once('functions-widgets.php');

/* 
 *	FIX TINYMCE size with qTranslate 
 */

function wptiny($initArray){
    $initArray['height'] = '300px';
    return $initArray;
}
add_filter('tiny_mce_before_init', 'wptiny');
function qtranslate_menu_item( $menu_item ) {
   if (stripos($menu_item->url, get_site_url()) !== false){
    $menu_item->url = qtrans_convertURL($menu_item->url);
    }
return $menu_item;
}

// add_filter('wp_setup_nav_menu_item', 'qtranslate_menu_item', 0);
add_action('admin_menu', 'my_remove_menu_elements', 102);

function my_remove_menu_elements()
{
	remove_submenu_page( 'themes.php', 'theme-editor.php' );
}

add_action('admin_menu','wphidenag');
function wphidenag() {
	remove_action( 'admin_notices', 'update_nag', 3 );
}

function my_footer_shh() {
    remove_filter( 'update_footer', 'core_update_footer' ); 
}

add_action( 'admin_menu', 'my_footer_shh' );

?>
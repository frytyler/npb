<?php

class SP_Widget extends WP_Widget
{
	var $LANGS = NULL;
	var $TWIG = NULL;

	public function __construct( )
	{
		$this->LANGS = (sp_french_enabled())? array('en','fr') : array('en');
		if ( defined( "SP_CORE_ACTIVE_FLAG" ) )
		{
			$this->TWIG = sp_new_twig_engine(__DIR__.'/templates');
		}
	}
}

class Code_Widget extends SP_Widget 
{

	public function __construct(  ) 
	{
		parent::__construct( );
		WP_Widget::__construct(
			'codewidget',
			__('Insert your text'),
			array( 'description' => __('Embed your code or snippet'), 'classname' => 'code'),
			array( )
		);
	}

	public function form( $instance ) 
	{
		$template = $field = array( );
		$instance = wp_parse_args( (array) $instance, array('title_en'=>'','title_fr'=>'','text_en'=>'','text_fr'=>''));
		foreach($this->LANGS AS $lang):
			$field[$lang]["title"]["id"]    = $this->get_field_id('title_'.$lang);
			$field[$lang]["title"]["name"]  = $this->get_field_name('title_'.$lang);
			$field[$lang]["title"]["value"] = strip_tags($instance['title_'.$lang]);
			$field[$lang]["text"]["id"]     = $this->get_field_id('text_'.$lang);
			$field[$lang]["text"]["name"]   = $this->get_field_name('text_'.$lang);
			$field[$lang]["text"]["value"]  = trim($instance['text_'.$lang]);
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		echo $this->TWIG->render('widget.form.code.twig',$template);
	}

	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			$i['title_'.$lang] = strip_tags($n['title_'.$lang]);
			$i['text_'.$lang] =  $n['text_'.$lang];
		endforeach;
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		$template = array( );
		$text = apply_filters('widget_text', @sp_translate($instance['text_en'],$instance['text_fr']), $instance );
		$template["before_widget"] = $before_widget;
		$template["before_title"]  = $before_title;
		$template["title"]         = apply_filters('widget_title', @sp_translate(empty($instance['title_en']),empty($instance['title_fr'])) ? '' : @sp_translate($instance['title_en'],$instance['title_fr']), $instance );
		$template["after_title"]   = $after_title;
		ob_start();
		eval('?>'.$text);
		$template["text"] = ob_get_contents();
		ob_end_clean();
		$template["after_widget"] = $after_widget;
		echo $this->TWIG->render('widget.code.twig',$template);
	}
}

class Promo_Widget extends SP_Widget 
{
	public function __construct(  ) 
	{
		parent::__construct( );
		WP_Widget::__construct(
			'promos',
			__('Promotional Graphic / Link'),
			array( 'description' => __('Ad for another site or Product etc.'), 'classname' => 'widget_promos'),
			array( )
		);
	}

	public function form( $instance )
	{
		$template = $field = array( );
		$title = apply_filters('widget_title', empty($instance['title_en']) ? '' : $instance['title_en'], $instance);
		$instance = wp_parse_args((array) $instance, array('title_en'=>'','image_en'=>'', 'link_en' =>'','title_fr'=>'','image_fr'=>'', 'link_fr' =>''));
		foreach($this->LANGS as $lang):
			$field[$lang]["title"]["id"]    = $this->get_field_id('title_'.$lang);
			$field[$lang]["title"]["name"]  = $this->get_field_name('title_'.$lang);
			$field[$lang]["title"]["value"] = strip_tags($instance['title_'.$lang]);
			$field[$lang]["image"]["id"]    = $this->get_field_id('image_'.$lang);
			$field[$lang]["image"]["name"]  = $this->get_field_name('image_'.$lang);
			$field[$lang]["image"]["value"] = strip_tags($instance['image_'.$lang]);
			$field[$lang]["link"]["id"]     = $this->get_field_id('link_'.$lang);
			$field[$lang]["link"]["name"]   = $this->get_field_name('link_'.$lang);
			$field[$lang]["link"]["value"]  = strip_tags($instance['link_'.$lang]);
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		echo $this->TWIG->render('widget.form.promos.twig',$template);
	}
	
	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			$i['title_'.$lang] = strip_tags($n['title_'.$lang]);
			$i['image_'.$lang] = strip_tags($n['image_'.$lang]);
			$i['link_'.$lang] = strip_tags($n['link_'.$lang]);
		endforeach;
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		$template = array( );
		$template["before_widget"] = $before_widget;
		$template["before_title"]  = $before_title;
		$template["title"]         = apply_filters('widget_title', @sp_translate(empty($instance['title_en']),empty($instance['title_fr'])) ? '' : @sp_translate($instance['title_en'],$instance['title_fr']), $instance );
		$template["after_title"]   = $after_title;
		$template["image"]         = @sp_translate($instance['image_en'],$instance['image_fr']);
		$template["link"]          = @sp_translate($instance['link_en'],$instance['link_fr']);
		$template["after_widget"]  = $after_widget;
		echo $this->TWIG->render('widget.promos.twig',$template);
	}
}

class Feature_Widget extends SP_Widget
{
	public function __construct(  ) 
	{
		parent::__construct( );
		WP_Widget::__construct(
			'features',
			__('Feature Details'),
			array( 'description' => __('Display Feature Details in a widget'), 'classname' => 'widget_features'),
			array( )
		);
	}

	public function form( $instance )
	{
		$template = $field = array( );
		$title = apply_filters('widget_title', empty($instance['title_en']) ? '' : $instance['title_en'], $instance);
		$instance = wp_parse_args((array) $instance, array());
		foreach($this->LANGS as $lang):
			
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		echo '<p>There are no settings for this widget.</p><p>This widget pulls in the latest feature from the features tab. It will show the Title, Description and Button</p>';
		// echo $this->TWIG->render('widget.form.promos.twig',$template);
	}
	
	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			// $i['title_'.$lang] = strip_tags($n['title_'.$lang]);
			// save fields
		endforeach;
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		global $NPB;
		$settings = get_option('npb_feature_options');
		$settings["before_widget"] = $before_widget;
		$settings["before_title"]  = $before_title;
		$settings["after_title"]   = $after_title;
		$settings["after_widget"]  = $after_widget;
		$args = array("post_type" => "features", "posts_per_page" => $settings["feature_count"], "orderby" => "date", "order" => "DESC");
		$NPB->get_queried_posts($args, 'widget.features.twig', $settings);
	}
}

class Event_Widget extends SP_Widget
{
	public function __construct(  ) 
	{
		parent::__construct( );
		WP_Widget::__construct(
			'event',
			__('Event Details'),
			array( 'description' => __('Display Event Details in a widget'), 'classname' => 'widget_event'),
			array( )
		);
	}

	public function form( $instance )
	{
		$template = $field = array( );
		$title = apply_filters('widget_title', empty($instance['title_en']) ? '' : $instance['title_en'], $instance);
		$instance = wp_parse_args((array) $instance, array('title_en'=>'','title_fr'=>'','event'=>''));
		foreach($this->LANGS as $lang):
			$field[$lang]["title"]["id"]    = $this->get_field_id('title_'.$lang);
			$field[$lang]["title"]["name"]  = $this->get_field_name('title_'.$lang);
			$field[$lang]["title"]["value"] = strip_tags($instance['title_'.$lang]);
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		
		$template["field"]["event"]["id"]    = $this->get_field_id('event');
		$template["field"]["event"]["name"]  = $this->get_field_name('event');
		$template["field"]["event"]["value"] = strip_tags($instance['event']);

		$events = array( );
		$args = array("post_type" => "events", "posts_per_page" => 5, "orderby" => "date", "order" => "DESC");
		$query = new WP_Query($args);
		$x = 0;
		while ($query->have_posts()) : $query->the_post();
			$events[$x]["id"] = $query->post->ID;
			$events[$x]["title"] = apply_filters('the_title',$query->post->post_title);
			$x++;
		endwhile; wp_reset_postdata();

		$template["events"] = $events;

		echo $this->TWIG->render('widget.form.event.twig',$template);
	}
	
	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			$i['title_'.$lang] = strip_tags($n['title_'.$lang]);
		endforeach;
		$i['event'] = $n['event'];
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		global $NPB;
		$settings["before_widget"] = $before_widget;
		$settings["before_title"]  = $before_title;
		$settings["title"]         = apply_filters('widget_title', @sp_translate(empty($instance['title_en']),empty($instance['title_fr'])) ? '' : @sp_translate($instance['title_en'],$instance['title_fr']), $instance );
		$settings["after_title"]   = $after_title;
		$settings["after_widget"]  = $after_widget;
		$args = array("post_type" => "events", "p" => $instance["event"]);
		$NPB->get_queried_posts($args, 'widget.event.twig', $settings);
	}
}

class UpcomingEvents_Widget extends SP_Widget
{
	public function __construct(  ) 
	{
		parent::__construct( );
		WP_Widget::__construct(
			'upcomingevents',
			__('Upcoming Events'),
			array( 'description' => __('Display Upcoming Events in a widget'), 'classname' => 'widget_upcomingevents'),
			array( )
		);
	}

	public function form( $instance )
	{
		$template = $field = array( );
		$title = apply_filters('widget_title', empty($instance['title_en']) ? '' : $instance['title_en'], $instance);
		$instance = wp_parse_args((array) $instance, array('title_en'=>'','title_fr'=>'','event'=>''));
		foreach($this->LANGS as $lang):
			$field[$lang]["title"]["id"]    = $this->get_field_id('title_'.$lang);
			$field[$lang]["title"]["name"]  = $this->get_field_name('title_'.$lang);
			$field[$lang]["title"]["value"] = strip_tags($instance['title_'.$lang]);
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		
		$template["field"]["eventids"]["id"]    = $this->get_field_id('eventids');
		$template["field"]["eventids"]["name"]  = $this->get_field_name('eventids');
		$template["field"]["eventids"]["value"] = $instance['eventids'];

		$events = array( );
		$args = array("post_type" => "events", "posts_per_page" => 5, "orderby" => "date", "order" => "DESC");
		$query = new WP_Query($args);
		$x = 0;
		while ($query->have_posts()) : $query->the_post();
			$events[$x]["id"] = $query->post->ID;
			$events[$x]["title"] = apply_filters('the_title',$query->post->post_title);
			$events[$x]["is_selected"] = (in_array($query->post->ID, $instance["eventids"]) ? 1 : 0);
			$x++;
		endwhile; wp_reset_postdata();

		$template["events"] = $events;

		echo $this->TWIG->render('widget.form.upcomingevents.twig',$template);
	}
	
	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			$i['title_'.$lang] = strip_tags($n['title_'.$lang]);
		endforeach;
		$i['eventids'] = $n['eventids'];
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		global $NPB;
		$settings["before_widget"] = $before_widget;
		$settings["before_title"]  = $before_title;
		$settings["title"]         = apply_filters('widget_title', @sp_translate(empty($instance['title_en']),empty($instance['title_fr'])) ? '' : @sp_translate($instance['title_en'],$instance['title_fr']), $instance );
		$settings["after_title"]   = $after_title;
		$settings["after_widget"]  = $after_widget;
		$args = array("post_type" => "events", "post__in" => $instance["eventids"]);
		$NPB->get_queried_posts($args, 'widget.upcomingevents.twig', $settings);
	}
}

class NewsLetter_Widget extends SP_Widget
{
	var $NEWS_LETTER_FORM = NULL;
	public function __construct(  ) 
	{
		global $wpdb;
		parent::__construct( );
		WP_Widget::__construct(
			'newsletter',
			__('News Letter Signup'),
			array( 'description' => __('News Letter Signup widget'), 'classname' => 'widget_news_letter'),
			array( )
		);
		$this->NEWS_LETTER_FORM = $wpdb->prefix . "news_letter_form";
	}

	public function form( $instance )
	{
		$template = $field = array( );
		$title = apply_filters('widget_title', empty($instance['title_en']) ? '' : $instance['title_en'], $instance);
		$instance = wp_parse_args((array) $instance, array());
		foreach($this->LANGS as $lang):
			$field[$lang]["title"]["id"]    = $this->get_field_id('title_'.$lang);
			$field[$lang]["title"]["name"]  = $this->get_field_name('title_'.$lang);
			$field[$lang]["title"]["value"] = strip_tags($instance['title_'.$lang]);
			$field[$lang]["name_label"]["id"]    = $this->get_field_id('name_label_'.$lang);
			$field[$lang]["name_label"]["name"]  = $this->get_field_name('name_label_'.$lang);
			$field[$lang]["name_label"]["value"] = strip_tags($instance['name_label_'.$lang]);
			$field[$lang]["email_label"]["id"]    = $this->get_field_id('email_label_'.$lang);
			$field[$lang]["email_label"]["name"]  = $this->get_field_name('email_label_'.$lang);
			$field[$lang]["email_label"]["value"] = strip_tags($instance['email_label_'.$lang]);
			$field[$lang]["postalcode_label"]["id"]    = $this->get_field_id('postalcode_label_'.$lang);
			$field[$lang]["postalcode_label"]["name"]  = $this->get_field_name('postalcode_label_'.$lang);
			$field[$lang]["postalcode_label"]["value"] = strip_tags($instance['postalcode_label_'.$lang]);
			$field[$lang]["button_text"]["id"]    = $this->get_field_id('button_text_'.$lang);
			$field[$lang]["button_text"]["name"]  = $this->get_field_name('button_text_'.$lang);
			$field[$lang]["button_text"]["value"] = strip_tags($instance['button_text_'.$lang]);
		endforeach;
		$template["langs"] = $this->LANGS;
		$template["field"] = $field;
		echo $this->TWIG->render('widget.form.newsletter.twig',$template);
	}
	
	public function update( $n, $o )
	{
		$i = $o;
		foreach($this->LANGS as $lang):
			$i['title_'.$lang] = strip_tags($n['title_'.$lang]);
			$i['name_label_'.$lang] = strip_tags($n['name_label_'.$lang]);
			$i['email_label_'.$lang] = strip_tags($n['email_label_'.$lang]);
			$i['postalcode_label_'.$lang] = strip_tags($n['postalcode_label_'.$lang]);
			$i['button_text_'.$lang] = strip_tags($n['button_text_'.$lang]);
		endforeach;
		return $i;
	}

	public function widget( $args, $instance )
	{
		extract($args);
		global $NPB;
		$template = array( );
		$template["hidden_field_name"] = md5('newsletter_form');

		if ( isset( $_POST[ $template["hidden_field_name"] ] ) ):
			$template["msg"] = $this->process_form($_POST);
			$_POST = array();
		endif;

		$template["before_widget"] = $before_widget;
		$template["before_title"]  = $before_title;
		$template["title"]         = apply_filters('widget_title', @sp_translate(empty($instance['title_en']),empty($instance['title_fr'])) ? '' : @sp_translate($instance['title_en'],$instance['title_fr']), $instance );

		$template["fields"] = array( 'name', 'email', 'postalcode' );
		foreach($template["fields"] as $field):
			$template["field"][$field]["label"] = @sp_translate($instance[$field."_label_en"],$instance[$field."_label_fr"]);
			$template["field"][$field]["id"] = 'newsletter_'.$field;
			$template["field"][$field]["name"] = 'newsletter_'.$field;
			$template["field"][$field]["value"] = $_POST["newsletter_".$field];
		endforeach;

		$template["button_text"] = @sp_translate($instance["button_text_en"],$instance["button_text_fr"]);
		
		$template["after_title"]   = $after_title;
		$template["after_widget"]  = $after_widget;
		echo $this->TWIG->render('widget.newsletter.twig', $template);
	}

	public function process_form($post)
	{
		global $wpdb;
		$return_value = '';
		$name       = @sp_clean_post($post["newsletter_name"]);
		$email      = (is_email($post["newsletter_email"])) ? $post["newsletter_email"] : false;
		$postalcode = @sp_clean_post($post["newsletter_postalcode"]);
		if ($name && $email && $postalcode) {

			$new_contact_form = $wpdb->insert(
				$this->NEWS_LETTER_FORM,
				array(
					'name' => $name,
					'email' => $email,
					'postalcode' => $postalcode
				),
				array( '%s', '%s', '%s' )
			);

			if (1 == $new_contact_form) {
				$return_value = $this->TWIG->render('partial.msg.twig', array('type' => 'success', 'msg' => sp_translate('Thank you!', 'Merci !')));
			}

		} else {
			$return_value = $this->TWIG->render('partial.msg.twig', array('type' => 'error', 'msg' => sp_translate('All fields are required', 'Tous les champs sont obligatoires')));
		}
		return $return_value;
	}
}

function register_all_widgets ( ) { 
	register_widget ( 'Code_Widget' );
	register_widget ( 'Promo_Widget' );
	register_widget ( 'Feature_Widget' );
	register_widget ( 'Event_Widget' );
	register_widget ( 'UpcomingEvents_Widget' );
	register_widget ( 'NewsLetter_Widget' );
}

add_action ('widgets_init', 'register_all_widgets');

?>
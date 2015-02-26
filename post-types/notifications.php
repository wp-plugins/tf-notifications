<?php
if(!class_exists('TF_PT_Notifications')) {
	class TF_PT_Notifications {
		const POST_TYPE = "tf_notification";
		private $_meta = array(
			'tf_featured',
			'tf_lightbox',
			'tf_start_date',
			'tf_end_date',
			'tf_display_date',
			'tf_complete'
		);
		
		/**
		 * The Constructor
		 */ 
		public function __construct() {
			add_filter( 'template_include', array( $this, 'template_loader' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
		} // END public function __construct()
		
		/**
		 * Create the post type
		 */ 
		public function create_post_type() {
			register_post_type(self::POST_TYPE,
				array(
					'labels' => array(
						'name' => "Notifications",
						'singular_name' => __(ucwords(str_replace("_", " ", self::POST_TYPE)))
					),
					'public' => true,
					'has_archive' => true,
					'description' => __(""),
					'exclude_from_search' => false,
					'publicly_queryable' => true,
					'show_ui' => true,
					'show_in_nav_menus' => true,
					'show_in_menu' => true,
					'query_var' => true,
					'rewrite' => array('slug' => 'notifications'),
					'capability_type' => 'post',
					'hierarchical' => false,
					'supports' => array(
						'title',
						'editor',
						'author',
						'thumbnail',
						'excerpt',
						'comments'
					),
					'taxonomies' => array('notification-reason', 'notification-affected')
				)
			);
		}
		
		/**
		 * Create custom taxonomies for custom post type
		 */
		public function create_taxonomies() {
			register_taxonomy(
				self::POST_TYPE.'_reason',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
				self::POST_TYPE,        //post type name
				array(
					'hierarchical' => false,
					'label' => 'Notification Reason',  //Display name
					'query_var' => true,
					'rewrite' => array(
						'slug' => 'notification-reason', // This controls the base slug that will display before each term
						'with_front' => false // Don't display the category base before
					)
				)
			);
			
			register_taxonomy(
				self::POST_TYPE.'_affected',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
				self::POST_TYPE,        //post type name
				array(
					'hierarchical' => false,
					'label' => 'Who is Affected',  //Display name
					'query_var' => true,
					'rewrite' => array(
						'slug' => 'notification-affected', // This controls the base slug that will display before each term
						'with_front' => false // Don't display the category base before
					)
				)
			);
		}
		
		/**
		 * Load a template.
		 *
		 * Handles template usage so that we can use our own templates instead of the themes.
		 *
		 * @access public
		 * @param mixed $template
		 * @return string
		 */
		public function template_loader( $template ) {
			global $post, $tf_notifications;
			$find = array( 'tf-notifications.php' );
			$file = '';
				
			if ( is_single() && get_post_type() == self::POST_TYPE ) {
				$file 	= 'single-notifications.php';
				$find[] = $file;
				$find[] = $tf_notifications->template_url . $file;
		
			} elseif ( is_tax( 'tf_notification_reason' ) || is_tax( 'tf_notification_affected' ) ) {
				$term = get_queried_object();
		
				$file 		= 'taxonomy-' . $term->taxonomy . '.php';
				$find[] 	= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] 	= $tf_notifications->template_url . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
				$find[] 	= $file;
				$find[] 	= $tf_notifications->template_url . $file;
		
			} elseif ( is_post_type_archive( self::POST_TYPE ) ) {
				$file 	= 'archive-notifications.php';
				$find[] = $file;
				$find[] = $tf_notifications->template_url . $file;
		
			}

			if ( $file ) {
				$template = locate_template( $find );
				if ( ! $template ) $template = $tf_notifications->plugin_path() . '/templates/' . $file;
			}
				
			return $template;
		}
		
		/**
		 * Hook into WP's admin_init action hook
		 */
		public function admin_init() {
			// Add metaboxes
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
		} // END public function admin_init()
		
		/**
		 * Hook into WP's add_meta_boxes action hook
		 */
		public function add_meta_boxes() {
			// Add this metabox to every selected post				
			add_meta_box(
				sprintf('wp_plugin_template_%s_section', self::POST_TYPE),
				sprintf('Options', ucwords(str_replace("_", " ", self::POST_TYPE))),
				array(&$this, 'add_meta_box_template'),
				self::POST_TYPE
			);
		} // END public function add_meta_boxes()
		
		/**
		 * Called off of the add meta box
		 *
		 * @param unknown $post
		 */
		public function add_meta_box_template($post) {
			// Render the job order metabox
			include(sprintf("%s/templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
		} // END public function add_inner_meta_boxes($post)
		
		/**
		 * Save the metaboxes for this custom post type
		 */ 
		public function save_post($post_id) {
			global $wpdb;
		// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}
			if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id)) {				;
				foreach($this->_meta as $field_name) {
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
			} else {
				return;
			}

			if ( $post_id == null || empty($_POST) )
				return;
				
			if ( !isset( $_POST['post_type'] ) || $_POST['post_type'] != self::POST_TYPE )
				return;
				
			if ( wp_is_post_revision( $post_id ) )
				$post_id = wp_is_post_revision( $post_id );
				
			global $post;
			if ( empty( $post ) )
				$post = get_post($post_id);
			
			return;
		} // END public function save_post($post_id)
		
	} // END class TF_PT_Notifications 
} // END if(!class_exists('TF_PT_Notifications'))
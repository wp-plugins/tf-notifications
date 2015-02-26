<?php
/*
Plugin Name: TF Notifications
Plugin URI: http://www.timfitt.com/work/wordpress/plugins/tf-notifications
Description: Keep your visitors up to date with notifications across your organisation.
Version: 1.0.0
Author: Tim Fitt
Author URI: http://www.timfitt.com
License: GPL2
*/

/*  Copyright 2013  Tim Fitt  (email : developer@timfitt.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (! class_exists ( 'TF_Notifications' )) {
	class TF_Notifications {
		
		/**
		 *
		 * @var string
		 */
		public $version = '1.0.0';
		
		/**
		 *
		 * @var string
		 */
		public $plugin_url;
		
		/**
		 *
		 * @var string
		 */
		public $plugin_path;
		
		/**
		 *
		 * @var string
		 */
		public $template_url;
		public function __construct() {
			if (! session_id ())
				session_start ();
				
				// Define version constant
			define ( 'TF_NOTIFICATIONS_VERSION', $this->version );
			
			add_action ( 'init', array ( $this, 'init' ) );
			add_action ( 'admin_init', array ( $this, 'admin_init' ) );
			add_action ( 'wp_enqueue_scripts', array ( $this, 'tf_enqueue_scripts' ) );
			add_action ( 'admin_enqueue_scripts', array ( $this, 'tf_enqueue_admin_scripts' ) );
			
			add_shortcode ( 'tf_important_notification', array ( $this, 'notification_shortcode' ) );
			add_shortcode ( 'tf_notification_table', array ( $this, 'notification_table_shortcode' ) );
			
			add_action ( 'wp_footer', array ( $this, 'footer_code' ) );
		}
		function footer_code() {
			$notifications = $this->get_lightbox_notifications ();
			if (! isset ( $_SESSION ['showNotificationPopNot'] ) || (isset ( $_SESSION ['showNotificationPopNot'] ) && $_SESSION ['showNotificationPopNot'] != "noshowpop")) {
				if ($notifications != null) {
					$_SESSION ['showNotificationPopNot'] = "noshowpop";
					?>
					<div style="display: none;">
						<style>
							#tf-notification-pop ul {
								padding: 0;
								margin: 0;
								list-style: none;
							}
							
							#tf-notification-pop h5 {
								margin: 0;
							}
							
							#tf-notification-pop .news-date {
								font-style: italic;
								font-weight: bold;
							}
						</style>
						<div id="tf-notification-pop">
							<h1>Important News</h1>
							<ul>
								<?php
								foreach ( $notifications as $notification ) {
									?>
									<li>
										<h5><a href="<?php echo get_permalink($notification->ID);?>"><?php echo $notification->post_title;?></a></h5>
										<?php
										if (get_post_meta ( $notification->ID, 'tf_start_date', true ))
											echo '<div class="news-date">' . date ( "D j M, Y - g:ia", strtotime ( get_post_meta ( $notification->ID, 'tf_start_date', true ) ) );
										
										if (get_post_meta ( $notification->ID, 'tf_start_date', true ) && get_post_meta ( $notification->ID, 'tf_end_date', true ))
											echo " to " . date ( "D j M, Y - g:ia", strtotime ( get_post_meta ( $notification->ID, 'tf_end_date', true ) ) );
										
										if (get_post_meta ( $notification->ID, 'tf_start_date', true ))
											echo "</div>";
										?>
										<p><?php echo $notification->post_excerpt;?></p>
										<p><a href="<?php echo get_permalink($notification->ID);?>">Read more...</a></p>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
					<script>
						jQuery(document).ready(function($) {
							$.colorbox({inline:true, href:"#tf-notification-pop", width:"50%", height:"50%"});
						});
					</script>
					<?php
				}
			}
		}
		function tf_enqueue_admin_scripts() {
			wp_enqueue_style ( 'tfnotifications_jqthemestyles', plugins_url ( 'css/jquery.ui.theme.css', __FILE__ ) );
			wp_enqueue_style ( 'tfnotifications_styles', plugins_url ( 'css/style.css', __FILE__ ) );
			
			wp_enqueue_script ( 'tfnotifications-jquery-ui-custom', plugins_url ( 'admin/js/jquery-ui-1.10.4.custom.min.js', __FILE__ ), array('jquery'));
			//wp_enqueue_script ( 'tfnotifications-timepicker', plugins_url ( 'admin/js/timepicker.js', __FILE__ ), array('jquery'));
			wp_enqueue_script ( 'tfnotifications-jquery-ui-timepicker', plugins_url ( 'admin/js/jquery-ui-timepicker-addon.js', __FILE__ ), array('jquery'));
			wp_enqueue_script ( 'tfnotifications-jquery-ui-slideraccess', plugins_url ( 'admin/js/jquery-ui-sliderAccess.js', __FILE__ ), array('jquery'));
			
			wp_enqueue_script ( 'tfnotifications_scripts', plugins_url ( 'admin/js/scripts.js', __FILE__ ), array('jquery') );
		}
		function tf_enqueue_scripts() {
			wp_enqueue_style ( 'tfnotifications_jqinfcar_styles', plugins_url ( 'css/jquery.bxslider.css', __FILE__ ) );
			wp_enqueue_style ( 'tfnotifications_colorbox_styles', plugins_url ( 'css/colorbox.css', __FILE__ ) );
			
			wp_enqueue_script ( 'tfnotifications_infcar_scripts', plugins_url ( 'js/jquery.bxslider.js', __FILE__ ), array('jquery') );
			wp_enqueue_script ( 'tfnotifications_colorbox_scripts', plugins_url ( 'js/jquery.colorbox.js', __FILE__ ), array('jquery') );
			wp_enqueue_script ( 'tfnotifications_scripts', plugins_url ( 'js/scripts.js', __FILE__ ), array('jquery') );
		}
		public function init() {
			$this->template_url = apply_filters ( 'tfnotifications_template_url', 'tf-notifications/' );
			$this->register_custom_post_types ();
		}
		public function admin_init() {
			global $TF_PT_Notifications;
			$TF_PT_Notifications->admin_init ();
		}
		
		/**
		 * Regsiter custom post types
		 */
		public function register_custom_post_types() {
			global $TF_PT_Notifications;
				
			require_once(sprintf("%s/post-types/notifications.php", dirname(__FILE__)));
			$TF_PT_Notifications = new TF_PT_Notifications();
			$TF_PT_Notifications->create_post_type();
			$TF_PT_Notifications->create_taxonomies();
		} // END public function register_custom_post_types()
		
		public static function activate() {
			
		}
		
		public static function deactivate() {
			
		}
		
		/**
		 * Get the plugin url.
		 *
		 * @access public
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}
		
		/**
		 * Get the plugin path.
		 *
		 * @access public
		 * @return string
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;
		
			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
		
		public function get_featured_notifications() {
			global $wpdb;
			// Get all notifications marked as featured, limit by "count"
			$notifications = $wpdb->get_results(
					$wpdb->prepare(
							"
							SELECT p.* FROM $wpdb->posts p
							LEFT JOIN $wpdb->postmeta pm1 ON p.ID = pm1.post_id
							LEFT JOIN $wpdb->postmeta pm2 ON pm1.post_id = pm2.post_id
							LEFT JOIN $wpdb->postmeta pm3 ON pm2.post_id = pm3.post_id
							LEFT JOIN $wpdb->postmeta pm4 ON pm3.post_id = pm4.post_id
							WHERE pm1.meta_key = %s
							AND pm1.meta_value = %s
							AND pm2.meta_key = %s
							AND (pm2.meta_value = %s OR UNIX_TIMESTAMP(pm2.meta_value) >= UNIX_TIMESTAMP(%s))
							AND pm3.meta_key = %s
							AND pm3.meta_value != %s
							AND pm4.meta_key = %s
							AND UNIX_TIMESTAMP(pm4.meta_value) <= UNIX_TIMESTAMP(%s)
							AND p.post_type = %s
							ORDER BY p.post_modified DESC
							",
							'tf_featured', '1', 'tf_end_date', '', current_time("mysql"), 'tf_complete', '1', 'tf_display_date', current_time("mysql"), 'tf_notification'
				)
			);
			
			return $notifications;
		}
		
		public function get_lightbox_notifications() {
			global $wpdb;
			// Get all notifications marked as featured, limit by "count"
			$notifications = $wpdb->get_results(
					$wpdb->prepare(
							"
							SELECT p.* FROM $wpdb->posts p
							LEFT JOIN $wpdb->postmeta pm1 ON p.ID = pm1.post_id
							LEFT JOIN $wpdb->postmeta pm2 ON pm1.post_id = pm2.post_id
							LEFT JOIN $wpdb->postmeta pm3 ON pm2.post_id = pm3.post_id
							WHERE pm1.meta_key = %s
							AND pm1.meta_value = %s
							AND pm2.meta_key = %s
							AND (pm2.meta_value = %s OR UNIX_TIMESTAMP(pm2.meta_value) >= UNIX_TIMESTAMP(%s))
							AND pm3.meta_key = %s
							AND pm3.meta_value != %s
							AND p.post_type = %s
							ORDER BY p.post_modified DESC
							",
							'tf_lightbox', '1', 'tf_end_date', '', current_time("mysql"), 'tf_complete', '1', 'tf_notification'
			)
			);
		
			return $notifications;
		}
		
		/*
		 * Create important notification shortcode
		*/
		public function notification_shortcode($atts) {
			
			extract(shortcode_atts(array(
			/*
			 * count => Number of posts to display in cycle
			 * link => 1 to link to the post, 0 to not include a link
			 * trans => Which transition to use between posts: fade / slide
			 * speed => Amount of "wait" time between transitions
			 */
				"count" => -1,
				"link" => 1,
				"trans" => "fade",
				"speed" => 3000
			), $atts));

			$notfications = $this->get_featured_notifications();
			
			$return_string = "";
			if($notfications != null && sizeof($notfications) > 0) {
				$return_string .= '<div class="tf-notification-sc"><ul class="'.$trans.'" data-speed="'.$speed.'">';
				foreach($notfications as $notification) {
					$return_string .= "<li><h5>";

					if($link == 1) {
						$return_string .= '<a href="'.get_permalink($notification->ID).'">'.$notification->post_title.'</a>';
					} else {
						$return_string .= $notification->post_title;
					}
					$return_string .= "</h5>";
					if(get_post_meta($notification->ID, 'tf_start_date', true ))
						$return_string .= '<div class="news-date">'.date("D j M, Y - g:ia", strtotime(get_post_meta($notification->ID, 'tf_start_date', true )));
					
					if(get_post_meta($notification->ID, 'tf_start_date', true ) && get_post_meta($notification->ID, 'tf_end_date', true ))
						$return_string .= " to ".date("D j M, Y - g:ia", strtotime(get_post_meta($notification->ID, 'tf_end_date', true )));
					
					if(get_post_meta($notification->ID, 'tf_start_date', true ))
						$return_string .= "</div>";
					
					$return_string .= "<p>".$notification->post_excerpt."</p>";
					
					if($link == 1)
						$return_string .= '<p><a href="'.get_permalink($notification->ID).'">Read more...</a></p>';
					
					$return_string .= "</li>";
				}
				$return_string .= "</ul></div>";
			}
			
			echo $return_string;
			
		} // END public function notification_shortcode()
		
		public function get_grouped_notifications() {
			global $wpdb;
			// Get all notifications marked as featured, limit by "count"
			$notifications = $wpdb->get_results(
				$wpdb->prepare(
							"
							SELECT p.*, pm1.meta_value as start_date, pm2.meta_value as end_date, pm3.meta_value as is_complete FROM $wpdb->posts p
							LEFT JOIN $wpdb->postmeta pm1 ON p.ID = pm1.post_id
							LEFT JOIN $wpdb->postmeta pm2 ON pm1.post_id = pm2.post_id
							LEFT JOIN $wpdb->postmeta pm3 ON pm2.post_id = pm3.post_id
							WHERE pm1.meta_key = %s
							AND pm2.meta_key = %s
							AND pm3.meta_key = %s
							AND p.post_type = %s
							ORDER BY pm3.meta_value ASC, UNIX_TIMESTAMP(pm1.meta_value) ASC
							",
							'tf_start_date', 'tf_end_date', 'tf_complete', 'tf_notification'
				)
			);
		
			return $notifications;
		}
		
		public function notification_table_shortcode($atts) {
			extract(shortcode_atts(array(
				/*
				 * count => Number of posts to display
				 * link => 1 to link to the post, 0 to not include a link
				 */
				"count" => -1,
				"link" => 1
				), $atts)
			);
			$return_string = "";
			$notifications = $this->get_grouped_notifications();
			if($notifications != null && sizeof($notifications) > 0) {
				$return_string = "<table>";
				$return_string .= "<caption>Current and Recent Notifications</caption>";
				$return_string .= "<tr>";
				$return_string .= "<th>Start</th>";
				$return_string .= "<th>Finish</th>";
				$return_string .= "<th>Information</th>";
				$return_string .= "</tr>";

				$current = 0;
				$resolved = 0;
				$c = 1;
				$d = 1;
				foreach($notifications as $notification) {
					if($current == 0 && $notification->is_complete != 1) { 
						$return_string .= '<tr><td colspan="3">Current</td></tr>';
						$current++;
					}
					if($resolved == 0 && $notification->is_complete == 1) {
						$return_string .= '<tr><td colspan="3">Resolved</td></tr>';
						$resolved++;
					}
					if($count > 0) {
						if($notification->is_complete != 1 && $c <= $count) {
							$return_string .= "<tr>";
							$return_string .= "<td>";
								if($notification->start_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->start_date));
							$return_string .= "</td>";
							$return_string .= "<td>";
								if($notification->end_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->end_date));
							$return_string .= "</td>";
							$return_string .= "<td>".$notification->post_title."</td>";
							$return_string .= "</tr>";

							$c++;
						} else if($notification->is_complete == 1 && $d <= $count) {
							$return_string .= "<tr>";
							$return_string .= "<td>";
								if($notification->start_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->start_date));
							$return_string .= "</td>";
							$return_string .= "<td>";
								if($notification->end_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->end_date));
							$return_string .= "</td>";
							$return_string .= "<td>".$notification->post_title."</td>";
							$return_string .= "</tr>";
							
							$d++;
						}
					} else {
						$return_string .= "<tr>";
						$return_string .= "<td>";
							if($notification->start_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->start_date));
						$return_string .= "</td>";
						$return_string .= "<td>";
							if($notification->end_date != "") $return_string .= date("D j M, Y - g:ia", strtotime($notification->end_date));
						$return_string .= "</td>";
						$return_string .= "<td>".$notification->post_title."</td>";
						$return_string .= "</tr>";
					}
				}
				$return_string .= "</table>";
			}
			
			return $return_string;
		}
		
	}
}

if(class_exists('TF_Notifications')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('TF_Notifications', 'activate'));
	register_deactivation_hook(__FILE__, array('TF_Notifications', 'deactivate'));
	// instantiate the plugin class
	$GLOBALS['tf_notifications'] = new TF_Notifications();
}
?>
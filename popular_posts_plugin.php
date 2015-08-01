<?php
/*
Plugin Name: Most Popular Posts
Plugin URI: 
Description: This widget will show you the most popular posts for the last ( week - Month - year )
Author: Yamen Ali
Version: 1
Author URI: 
*/

//=================================== Create Popular Posts Page =================================//


/* This function Will Create Our Main Page Which Will Contain The Popular Posts */
function create_main_page() {

		// page title
        $title = 'Most Popular Posts';
        // Check if page exists, if not create it
        if ( null == get_page_by_title( $title )) {

			// page info
            $uploader_page = array(
                    'comment_status'        => 'closed',
                    'ping_status'           => 'closed',
                    'post_author'           => 'Yamen Ali',
                    'post_name'             => 'popular_posts',
                    'post_title'            => $title,
                    'post_status'           => 'publish',
                    'post_type'             => 'page'
            );

			// insert page
            $post_id = wp_insert_post( $uploader_page );

			// set page template if everything goes fine
            if ( !$post_id ) {

                    wp_die( 'Error creating template page' );

            } else {

                    update_post_meta( $post_id, '_wp_page_template', 'popular_posts_page_template.php' );
            }
        } // end check if

}

/* Get Our Custom Template For Our Page */
function get_custom_template( $template ) {

		$plugindir = dirname( __FILE__ );

        if ( is_page_template( 'popular_posts_page_template.php' )) {

            $template = $plugindir . '/popular_posts_page_template.php';
        }

        return $template;
}
//=================================== Create Popular Posts Page =================================//



//=================================== Create Popular Posts Widget =================================//
class PopularPostsWidget extends WP_Widget
{
	protected static $footerTrackerCode;
	//========================================================//
	/* Our Widget Constructor */
	function PopularPostsWidget()
	{
		// define some widgets info ( Description , title ) 
		$widget_ops = array('classname' => 'PopularPostsWidget', 'description' => 'Displays most popular posts for the month ' );
		$this->WP_Widget('PopularPostsWidget', 'Most Popular', $widget_ops);
	}
	//========================================================//
	/* Build The Widget Form AS Admin Will Sees It */
	function form($instance)
	{
		// get widget title
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$userId = isset( $instance['userId'] ) ?  $instance['userId'] : '';
		$userPassword = isset( $instance['userPassword'] ) ? $instance['userPassword'] : '';
		$userProfileId=isset( $instance['userProfileId'] ) ? $instance['userProfileId']  : '';
		$trackerCode=isset( $instance['trackerCode'] ) ? $instance['trackerCode']  : '';
		
		// here goes our widget form design
?>
		<p>
			<label>Title : </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"  name="<?php echo $this->get_field_name( 'title' ); ?>" 
				type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label>User Id :<font color="red"> *</font> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'userId' ); ?>"  name="<?php echo $this->get_field_name( 'userId' ); ?>" 
				type="text" value="<?php echo $userId; ?>" />
		</p>
		
		<p>
			<label>User Password :<font color="red"> *</font> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'userPassword' ); ?>"  name="<?php echo $this->get_field_name( 'userPassword' ); ?>" 
				type="text" value="<?php echo $userPassword; ?>" />
		</p>
		
		<p>
			<label>Profile Id : <font color="red"> *</font> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'userProfileId' ); ?>"  name="<?php echo $this->get_field_name( 'userProfileId' ); ?>" 
				type="text" value="<?php echo $userProfileId; ?>" />
		</p>
		
		<p>
			<label>Tracker Code :<font color="red"> *</font> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'trackerCode' ); ?>"  name="<?php echo $this->get_field_name( 'trackerCode' ); ?>" 
				type="text" value="<?php echo $trackerCode; ?>" />
		</p>
<?php
	}
	//========================================================//
	/* Update This Widget info */
	function update($new_instance, $old_instance)
	{
		// update widget form title
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['userId'] = $new_instance['userId'];
		$instance['userPassword'] = $new_instance['userPassword'];
		$instance['userProfileId'] = $new_instance['userProfileId'];
		$instance['trackerCode'] = $new_instance['trackerCode'];
		return $instance;
	}
	//========================================================//
	
	//========================================================//
	/* Build The Widget Main Functionality  */
	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);
		
		// get widget parameters
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Most Popular Posts' );
		$userId = $instance['userId'] ;
		$userPassword = $instance['userPassword'];
		$userProfileId=$instance['userProfileId'];
		$trackerCode=$instance['trackerCode'] ;
		self::$footerTrackerCode="";
		$widgetContent="";
		// check if all required parameters are set
		if(empty($userId) || empty($userPassword) || empty($userProfileId) || empty($trackerCode))
		{
			$widgetContent="<p>Missing Information , Please Check Widget Admin Form To Add Them</p>";
		}
		else
		{
			// just to access the code at the right time 
			self::$footerTrackerCode=$trackerCode;

			// add tracker to footer
			$this->addTrackerId();
			
			// get our popular posts page url
			$page=get_page_by_title( 'Most Popular Posts' );
			$url=get_permalink($page->ID);
			$widgetContent='<a href="'.$url.'">'.$title.'</a>';
			
			// add GA info to post meta data
			update_post_meta($page->ID, 'userId',$userId,false);
			update_post_meta($page->ID, 'userPassword',$userPassword,true);
			update_post_meta($page->ID, 'userProfileId',$userProfileId,true);
		}
		// WIDGET CODE GOES HERE
?>
		<?php echo $before_widget;?>
		<?php echo $widgetContent ?>
		<?php echo $after_widget;?>
<?php
	}
	//========================================================//

	//=================================== Add Google Analytics Code To Page Footer ====================//	
	/* Call This Function To Define An Action To Insert Tracker Id To Every Page Footer */
	function addTrackerId() 
	{
		add_action('wp_footer', array($this,'insertCode'));
	}

	/* Code To Insert Tracker To Page Footer */
	function insertCode() {  
			echo "
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			  ga('create', '".self::$footerTrackerCode."', {'cookieDomain': 'none'});
			  ga('send', 'pageview');
			</script> ";
	}

	//=================================== Add Google Analytics Code To Page Footer ====================//
}
//=================================== Create Popular Posts Widget =================================//

/* Register Our Widget */
add_action( 'widgets_init', create_function('', 'return register_widget("PopularPostsWidget");') );

/* Get Our Template Page */
add_action( 'template_include', 'get_custom_template' );

/* Run (create_main_page) Function When Plugin Is Activated */
register_activation_hook( __FILE__, 'create_main_page' );

?>
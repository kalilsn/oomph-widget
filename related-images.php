<?php 
/**
 * Plugin Name: Related Images Widget
 * Description: Includes a related image in the sidebar with each post
 * Author: Kalil Smith-Nuevelle
 */

class Related_Images_Widget extends WP_Widget {
    public function __construct() {
        $options = array(
            'classname' => 'related_images',
            'description' => 'Includes a related image in the sidebar with each post'
        );
        parent::__construct('related_images_widget', 'Related Images', $options);
    }
}

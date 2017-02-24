<?php 
/**
 * Plugin Name: Related Images Widget
 * Description: Includes a related image in the sidebar with each post
 * Author: Kalil Smith-Nuevelle
 */

class Related_Images_Widget extends WP_Widget {

    /**
     * Set up widget with name/description
     */
    public function __construct() {
        $options = array(
            'classname' => 'related_images',
            'description' => 'Includes a related image in the sidebar with each post'
        );
        parent::__construct('related_images_widget', 'Related Images', $options);
    }

    /**
     * Output related image
     *
     *
     */
    public function widget($args, $instance) {
        $search_term = $this->getPostTopic();
        $api_key = $instance['api_key'];
        $url = $this->getImageURL($api_key);

        echo $args['before_widget'] . "<img src=\"$url\">" . $args['after_widget'];
    }

    /**
     * Output form for setting api key
     *
     */

    public function form($instance) {
        if (isset($instance['api_key'])) {
            $api_key = $instance['api_key'];
        } else {
            $api_key = '';
        }
        ?>
        <p>
            <label for="api_key"></label>
            <input type="text" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>">
        </p>
        <?php
    }

    /**
     * Save api key
     *
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['api_key'] = (!empty($new_instance['api_key'])) ? strip_tags($new_instance['api_key']) : '';
 
        return $instance;
    }

    /**
     * Get the subject of the post and return a string to query the api with
     * 
     * 
     */

    public function getPostTopic() {
        $query_obj = get_queried_object();
        if ($query_obj) {
            $post_id = $query_obj->ID;

        } else {
            return false;
        }
    }

    /**
     * Get an image url from the flickr api
     * 
     * 
     */
    public function getImageURL($api_key) {
        $params = array(
                'api_key'   => $api_key,
                'method'    => 'flickr.photos.search',
                'format'    => 'php_serial',
        );
    }
}

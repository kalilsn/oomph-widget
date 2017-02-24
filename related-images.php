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
     */
    public function widget($args, $instance) {
        $search_term = $this->getPostTopic();
        if (!$search_term) {
            echo "something's wrong with the search_string function";
            return false;
        }
        $api_key = $instance['api_key'];
        $url = $this->getImageURL($api_key);
        if ($url) {
            echo $args['before_widget'] . "<img src=\"$url\">" . $args['after_widget'];
        } else {
            echo "<p>something's wrong with the flickr api call</p>";
        }
        
    }

    /**
     * Output form for setting api key
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
     * @return string|bool 
     */

    public function getPostTopic() {
        $query_obj = get_queried_object();
        if ($query_obj) {
            $post_id = $query_obj->ID;
            $tags = wp_get_post_tags($post_id, array('fields' => 'names'));
            if ($tags) {
                //If post has tags, search by tags
                return join(' ', $tags);
            } else {
                //Otherwise by title
                return get_the_title($post_id);
            }

        } else {
            return false;
        }
    }

    /**
     * Get an image url from the flickr api
     * Mostly stolen from https://www.flickr.com/services/api/response.php.html
     * 
     * @param string $search_string String to search the api for
     * @param string $api_key A flickr api key is required to access the api
     * @return string|bool
     */
    public function getImageURL($search_string, $api_key) {
        $params = array(
                'api_key' => $api_key,
                'method' => 'flickr.photos.search',
                'format' => 'php_serial',
                'text' => $search_string,
                'sort' => 'relevance',
                'safe_search' => 1,
                'content_type' => 1,
                'media' => 1,
                'per_page' => 1,
                'page' => 1
        );
        
        $encoded_params = array();
        foreach ($params as $k => $v){
            $encoded_params[] = urlencode($k).'='.urlencode($v);
        }
        
        $url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
        $rsp = file_get_contents($url);
        $rsp_obj = unserialize($rsp);

        if ($rsp_obj['stat'] === 'ok') {
            $photo = $rsp_obj['photos'][0];
            $id = $photo['id'];
            $farm = $photo['farm'];
            $server = $photo['server'];
            $secret = $photo['secret'];
            $owner = $photo['owner'];
            return 'https://farm' . $farm . '.staticflickr.com/' . $server . '/' . $id . '_' . '$secret' . '_z.jpg';
        } else {
            return false;
        }
    }
}

//Register the widget
add_action( 'widgets_init', function(){
    register_widget('Related_Images_Widget');
});

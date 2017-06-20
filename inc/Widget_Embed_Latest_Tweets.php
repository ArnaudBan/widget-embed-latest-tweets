<?php

/**
 * Class Widget_Embed_Latest_Tweets
 *
 */
class Widget_Embed_Latest_Tweets extends WP_Widget {

    var $defaut = array(
        'title'            => 'Latest Tweets',
        'screen_name'      => '',
        'count'            => 3,
        'maxwidth'         => '',
        'align'            => 'none',
        'hide_thread'      => true,
        'include_rts'      => true,
        'hide_media'       => true,
        'exclude_replies'  => false,
    );

    var $align_possible_value = array('none', 'left', 'right', 'center');

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'welt_last_tweets', // Base ID
            'Widget embed latest Tweets', // Name
            array('description' => __('Show your latest Tweets', 'widget-embed-lastest-tweets'))// Args
        );

        if( defined( 'WPLANG' ) ){
            $this->defaut['lang'] = substr( WPLANG, 0, 2 );
        } else {
            $this->defaut['lang'] = 'en';
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        wp_enqueue_script('welt_script');
        wp_localize_script( 'welt_script', 'weltAjaxurl', admin_url('admin-ajax.php') );


        $instance = wp_parse_args($instance, $this->defaut);

        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];

        if ( !empty( $title ) ){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if( !empty( $instance['screen_name'] ) ){

            $data = ' ';
            if( method_exists('WP_Widget', 'is_preview' ) && $this->is_preview() ){

                foreach ($instance as $key => $value) {

                    if( !empty( $value ) ){
                        $data .= "data-{$key}='$value' ";
                    }
                }

            }

            echo "<div id='welt-{$this->id}' class='welt-tweet-wrapper'$data></div>";

        }

        echo $args['after_widget'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance = wp_parse_args($instance, $this->defaut);

        $instance['title'] = sanitize_text_field($new_instance['title']);

        $instance['screen_name'] = sanitize_text_field($new_instance['screen_name']);

        $count = intval( $new_instance['count'] );
        if( $count != 0 ){
            $instance['count'] = $count ;
        }

        $maxwidth = intval($new_instance['maxwidth']);
        $instance['maxwidth'] = $maxwidth != 0 ? $maxwidth : '';

        if( in_array($new_instance['align'], $this->align_possible_value ) ){
            $instance['align'] = $new_instance['align'];
        }

        $instance['hide_thread'] = isset( $new_instance['hide_thread'] ) && $new_instance['hide_thread'] == 'hide_thread';
        $instance['hide_media'] = isset( $new_instance['hide_media'] ) && $new_instance['hide_media'] == 'hide_media';
        $instance['exclude_replies'] = isset( $new_instance['exclude_replies'] ) && $new_instance['exclude_replies'] == 'exclude_replies';

        $instance['lang'] = sanitize_text_field($new_instance['lang']);

        // When everythings is check and we are not in the customizer, set the transient
        if( ! method_exists('WP_Widget', 'is_preview' ) || ! $this->is_preview() ){

            welt_set_tweet_transient( $this->id, $instance );

        }

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     *
     * @return string Default return is 'noform'.
     */
    public function form($instance) {

        $instance = wp_parse_args($instance, $this->defaut);
        extract($instance);

        $twitter_oauth_var = get_option('welt_twitter_oauth_var');

        // Are all the options there ?
        if( is_array( $twitter_oauth_var ) && count($twitter_oauth_var) == 4 ){

            $this->text_field( 'title', $instance['title'], __('Title') );
            $this->text_field( 'screen_name', $instance['screen_name'], __('Twitter Username', 'widget-embed-lastest-tweets') );

            $this->number_field( 'count', $instance['count'], __('Number of Tweet to display', 'widget-embed-lastest-tweets'), 1, 20 );
            $this->number_field( 'maxwidth', $instance['maxwidth'], __('Width' ), 250, 550, __('Twitter says :This value is constrained to be between 250 and 550 pixels', 'widget-embed-lastest-tweets') );

            $this->checkbox_field( 'hide_thread', $instance['hide_thread'], __('Hide Thread', 'widget-embed-lastest-tweets'), __('Hide the original message in the case that the embedded Tweet is a reply', 'widget-embed-lastest-tweets') );
            $this->checkbox_field( 'hide_media', $instance['hide_media'], __('Hide Media', 'widget-embed-lastest-tweets'), __('Hide the images in the Tweet', 'widget-embed-lastest-tweets') );
            $this->checkbox_field( 'exclude_replies', $instance['exclude_replies'], __('Exclude replies', 'widget-embed-lastest-tweets'), __('They will not show but they will count in the number of tweets', 'widget-embed-lastest-tweets') );

            $this->select_field( 'align', $instance['align'], __('Alignment'), $this->align_possible_value );


            // TODO get all languages and put a select https://api.twitter.com/1.1/help/languages.json
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language', 'widget-embed-lastest-tweets') ?> :</label>
                <input id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" type="text" value="<?php echo $lang; ?>" size="2"/>
                <br />
                <span class="description"><?php _e('Two firsts caractere only. Example : "fr" for french', 'widget-embed-lastest-tweets') ?></span>
            </p>
            <?php

        } else {
            ?>
            <p>
                <?php printf( __('You have to enter your <a href="%s">Twitter connections information first</a>', 'widget-embed-lastest-tweets'), 'options-general.php?page=welt_options_page' ) ?>
            </p>
            <?php
        }
        return 'form';
    }

    private function text_field( $option, $value, $label ){

        ?>
        <p>
            <label for="<?php echo $this->get_field_id($option); ?>"><?php echo $label; ?> :</label>
            <input class="widefat" id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
        </p>
        <?php
    }

    private function checkbox_field( $option, $value, $label, $description = '' ){

        ?>
        <p>
            <label for="<?php echo $this->get_field_id($option); ?>"><?php echo $label ?> :</label>
            <input id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>" type="checkbox" <?php checked( $value ) ?> value="<?php echo esc_attr( $option  ); ?>"/>
            <?php
            if( $description ){
                echo "<br /><span class='description'>{$description}</span>";
            }
            ?>
        </p>
        <?php
    }

    private function select_field( $option, $value, $label, $options, $description = '' ){

        ?>
        <p>
            <label for="<?php echo $this->get_field_id($option); ?>"><?php echo $label ?> :</label>
            <select id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>">
                <?php foreach( $options as $option ) { ?>
                    <option value="<?php echo $option ?>" <?php selected($option, $value, true) ?>><?php echo $option ?></option>
                <?php } ?>
            </select>
            <?php
            if( $description ){
                echo "<br /><span class='description'>{$description}</span>";
            }
            ?>
        </p>
        <?php
    }

    private function number_field( $option, $value, $label, $min, $max, $description = '' ){
        ?>

        <p>
            <label for="<?php echo $this->get_field_id($option); ?>"><?php echo $label; ?> :</label>
            <input id="<?php echo $this->get_field_id($option); ?>" name="<?php echo $this->get_field_name($option); ?>" type="number" step="1" min="<?php echo $min; ?>" max="<?php echo $max; ?>" value="<?php echo esc_attr( $value ); ?>" />
            <?php
            if( $description ){
                echo "<br /><span class='description'>{$description}</span>";
            }
            ?>
        </p>
        <?php
    }
}
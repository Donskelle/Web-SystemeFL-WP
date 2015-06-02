<?php
/**
 * Created by PhpStorm.
 * User: KRa
 * Date: 02.06.2015
 * Time: 10:10
 */
//http://code.tutsplus.com/tutorials/how-to-build-custom-dashboard-widgets--wp-29778
abstract class CustomWidget {

    protected $widgetID = '';
    protected $widgetTitle = '';
    protected $callback_function_name = 'widget_content';


    /**
     *
     */
    public function __construct($id, $title)
    {
        $this->set_widget_meta($id, $title);
        add_action('wp_dashboard_setup', array($this, 'add_dash_board_widget'));
    }
    /**
     * Setzt id und Namen des Widgets.
     * @param $id
     * @param $title
     * @return mixed
     */
    public function set_widget_meta($id, $title){
        $this->widgetID = $id;
        $this->widgetTitle = $title;
    }
    /**
     * @return mixed
     */
    public function add_dash_board_widget(){
        wp_add_dashboard_widget(
            $this->widgetID,
            $this->widgetTitle,
            array($this, $this->callback_function_name)
        );
    }



/**
     * Die Callbackfunktion des Widgets.
     * @return mixed
     */
    abstract public function widget_content();
        /*$user = wp_get_current_user();
        echo "Hello <strong>" . $user->user_login . "</strong>, this is your custom widget. You can, for instance, list all the posts you've published:";

        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'author' => $user->ID
        ) ) );

        if ( $r->have_posts() ) :

        ?><!--

                    --><?php
        /*endif;*/
}
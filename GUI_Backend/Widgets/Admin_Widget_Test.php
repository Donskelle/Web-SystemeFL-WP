<?php
/**
 * Created by PhpStorm.
 * User: KRa
 * Date: 02.06.2015
 * Time: 10:47
 */
require_once('CustomWidget.php');

/**
 * Class Admin_Widget_Test
 *
 * Beispielklasse für die Nutztung der CustomWidget BaseClass.
 */
class Admin_Widget_Test extends CustomWidget {


    public function __construct(){
        parent::__construct('adminWidget', 'My Adming Widget');
    }


    public function widget_content()
    {
        $user = wp_get_current_user();
        echo "Hello <strong>" . $user->user_login . " (".$user->roles[0].")</strong>, this is your custom widget. You can, for instance, list all the posts you've published:";

        $r = new WP_Query( apply_filters( 'widget_posts_args', array(
            'posts_per_page' => 10,
            'post_status' => 'publish',
            'author' => $user->ID
        ) ) );

        if ( $r->have_posts() ) :

        ?>

                            <?php
        endif;
    }
}
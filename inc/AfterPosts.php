<?php
namespace TeComments;

defined( 'ABSPATH' ) || exit;

final class AfterPosts
{
    public static $option_key = 'tecomments_after_posts_enable';

    public static function init(){
        add_action('admin_init', [__CLASS__, 'add_settings']);
        add_filter('the_content', [__CLASS__, 'render_after_content']);
    }

    /**
     * Render after content
     *
     * @param $content
     *
     * @return string
     */
    public static function render_after_content($content){
        if( ! get_option(self::$option_key)){
            return $content;
        }

        if( ! $post = get_post() ){
            return $content;
        }

        if('post' != $post->post_type){
            return $content;
        }
        $content .= do_shortcode('[telegram-comments]');
        return $content;
    }

    /**
     * Add settings
     */
    public static function add_settings(){
        register_setting('u7_telegram_comments_settings', self::$option_key);
        add_settings_field(
            $id = self::$option_key,
            $title = __('Show comments after posts in blog'),
            $callback = function($args){
                printf(
                    '<input type="checkbox" name="%s" value="1" %s/>',
                    $args['key'], checked(1, $args['value'], false)
                );
            },
            $page = 'u7_telegram_comments_settings',
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => self::$option_key,
                'value' => get_option(self::$option_key),
            ]
        );
    }

}

AfterPosts::init();
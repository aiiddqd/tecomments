<?php
namespace TeComments;

defined( 'ABSPATH' ) || exit;

final class SidePanel
{
    public static function init(){
        add_action('admin_init', [__CLASS__, 'add_settings']);
    }

    public static function add_settings(){
        $option_tc_sidepanel_enable = 'tecomments_sidepanel_enable';
        register_setting('u7_telegram_comments_settings', $option_tc_sidepanel_enable);
        add_settings_field(
            $id = $option_tc_sidepanel_enable,
            $title = __('Enable side panel for posts'),
            $callback = function($args){
                printf(
                    '<input type="checkbox" name="%s" value="1" %s/>',
                    $args['key'], checked(1, $args['value'], false)
                );
                printf('<p>%s</p>', __('Option in development'));
            },
            $page = 'u7_telegram_comments_settings',
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => $option_tc_sidepanel_enable,
                'value' => get_option($option_tc_sidepanel_enable),
            ]
        );
    }
}

SidePanel::init();
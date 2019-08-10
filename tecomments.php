<?php
/**
 * Plugin Name: TeComments
 * Plugin URI: https://github.com/uptimizt/telegram-comments-u7
 * Description: Telegram Comments for WordPress by @uptimizt - easy add comments from Telegram to own site by shortcode <code>[telegram-comments]</code>
 * Author: uptimizt
 * Author URI: https://github.com/uptimizt/
 * Version: 1.0
 * Text Domain: tecomments
 * Domain Path: /languages
 * License: MIT
 */

namespace TeComments;

defined( 'ABSPATH' ) || exit;

final class TelegramComments {

    public static $settings_sections_group = 'u7_telegram_comments_settings';
    public static $settings_options_group = 'u7_telegram_comments_settings';
    public static $settings_page_slug = 'telegram-comments-settings-u7';

    public static function init(){

        add_action('plugins_loaded', function (){
            require_once __DIR__ . '/inc/Widget.php';
            require_once __DIR__ . '/inc/SidePanel.php';
        });

        add_action('admin_init', [__CLASS__, 'add_settings']);

        add_action('admin_menu', function(){
            add_options_page(
                $page_title = 'Telegram Comments',
                $menu_title = 'Telegram Comments',
                $capability = 'administrator',
                $menu_slug = self::$settings_page_slug,
                $callback = [__CLASS__, 'render_settings']
            );
        });

        add_shortcode('telegram-comments', [__CLASS__, 'render_shortcode']);

        add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), [__CLASS__, 'add_settings_link_to_plugin_list'] );

        add_action( 'widgets_init', function(){
            register_widget( 'TeComments\Widget' );
        });

    }

    /**
     * add settings link to plugin list
     */
    public static function add_settings_link_to_plugin_list($links){
        $settings_link = sprintf('<a href="admin.php?page=%s">%s</a>', self::$settings_page_slug, __('Settings'));
        $support_link = sprintf('<a href="%s" target="_blank">%s</a>', 'https://github.com/uptimizt/telegram-comments-u7/issues', __('Support'));
        array_unshift($links, $support_link);
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * render shortcode
     */
    public static function render_shortcode($args){
        ob_start();

        $data = [];

        if( ! $data['site_id'] = get_option('tecom_site_id')){
            return '';
        }

        $data['limit'] = get_option('tecom_comments_limit', 5);

        $data['additional_data_attr'] = '';

        if(get_option('tecom_type_page_id') && $post = get_post() ){
            $data['additional_data_attr'] .= sprintf(' data-page-id="%s"', $post->ID);
        }

        $code = sprintf(
            '<script async src="https://comments.app/js/widget.js?2" data-comments-app-website="%s" data-limit="%s" %s></script>',
            $data['site_id'], $data['limit'], $data['additional_data_attr']
        );

        return $code;
    }

    /**
     * render_settings
     */
    public static function render_settings(){

        $url = 'https://comments.app/';
        if($site_id = get_option('tecom_site_id')){
            $url = 'https://comments.app/manage?website=' . $site_id;
        }

        ?>
        <div class="wrap">
            <h1>Telegram Comments</h1>

            <p>All settings we can get from official site <a href="<?= $url ?>" target="_blank"><?= $url ?></a></p>

            <form method="post" action="options.php">
                <?php settings_fields( self::$settings_options_group ); ?>
                <?php do_settings_sections( self::$settings_sections_group ); ?>
                <?php submit_button(); ?>

            </form>
        </div>
        <?php
    }


    /**
     * add settings
     */
    public static function add_settings(){
        add_settings_section(
            $id = 'u7_tc_general_settings',
            $title = 'Основные настройки',
            $callback = '',
            $page = self::$settings_sections_group
        );

        $option_tc_id = 'tecom_site_id';
        register_setting(self::$settings_options_group, $option_tc_id);
        add_settings_field(
            $id = $option_tc_id,
            $title = __('Site ID in Telegram Comments App'),
            $callback = function($args){
                printf(
                    '<input type="text" name="%s" value="%s" />',
                    $args['key'], $args['value']
                );
            },
            $page = self::$settings_sections_group,
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => $option_tc_id,
                'value' => get_option($option_tc_id),
            ]
        );

        $option_tc_limit = 'tecom_comments_limit';
        register_setting(self::$settings_options_group, $option_tc_limit);
        add_settings_field(
            $id = $option_tc_limit,
            $title = __('Limit display comments'),
            $callback = function($args){
                printf(
                    '<input type="number" name="%s" value="%s" />',
                    $args['key'], $args['value']
                );
            },
            $page = self::$settings_sections_group,
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => $option_tc_limit,
                'value' => get_option($option_tc_limit, 5),
            ]
        );

        $option_tc_height = 'tecom_height';
        register_setting(self::$settings_options_group, $option_tc_height);
        add_settings_field(
            $id = $option_tc_height,
            $title = __('Height display comments'),
            $callback = function($args){
                printf(
                    '<input type="number" name="%s" value="%s" />',
                    $args['key'], $args['value']
                );
                printf(
                    '<p><small>%s</small></p>',
                    __('If 0 - the height is auto, otherwise the height in pixels will be set')
                );
            },
            $page = self::$settings_sections_group,
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => $option_tc_height,
                'value' => get_option($option_tc_height, 0),
            ]
        );

        $option_tc_type_page_id = 'tecom_type_page_id';
        register_setting(self::$settings_options_group, $option_tc_type_page_id);
        add_settings_field(
            $id = $option_tc_type_page_id,
            $title = __('Set option if you want set page id as post id'),
            $callback = function($args){
                printf(
                    '<input type="checkbox" name="%s" value="1" %s/>',
                    $args['key'], checked(1, $args['value'], false)
                );

                printf(
                    '<p><small>%s</small></p>',
                    __('If you do not select an option, we may lose data after changing the URL')
                );
            },
            $page = self::$settings_sections_group,
            $section = 'u7_tc_general_settings',
            $args = [
                'key' => $option_tc_type_page_id,
                'value' => get_option($option_tc_type_page_id, 0),
            ]
        );
    }
}

TelegramComments::init();
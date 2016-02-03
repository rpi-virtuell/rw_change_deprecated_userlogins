<?php
/**
 * @TODO  Class RW_Change_Deprecated_Userlogins_Installation
 *
 * Contains some helper code for plugin installation
 *
 * @package   @TODO RW Demo Plugin
 * @author    @TODO Frank Staude
 * @license   GPL-2.0+
 * @link      @TODO https://github.com/rpi-virtuell/plugin-skeleton
 */
class RW_Change_Deprecated_Userlogins_Installation { //@TODO  Klassenname

    /**
     * rewirte user_login
     *
     * @ince    0.0.1
     * @access  private
     * @static
     * @action  rw_sticky_activity_autoload_unregister
     * @return  void
     */

   static function rewrite(){
        global $wpdb;

        if( !get_option('deprecated_logins_rewritten', false) ){

            $sql =  "
				delete from wp_usermeta where meta_key =  'deprecated_login';

				insert into wp_usermeta (user_id,meta_key,meta_value)
				 select
					 ID as user_id,
					 'deprecated_login' as meta_key
					 ,user_login as meta_value
					 FROM wp_users
						WHERE user_login Like '% %'
						OR user_login Like '%\_'
						OR user_login Like '\_%';

				update wp_users set user_login = replace(replace(replace(user_login,'.','-'),' ','-'),'--','-') where user_login Like '% %';
				update wp_users set user_login = replace(user_login,'_','') where user_login Like '%\_' or user_login Like '\_%';
				";

            $wpdb->query( $sql );

            update_option ( 'deprecated_logins_rewritten', 1 );

        }

    }

    /**
     * Check some thinks on plugin activation
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     */
    public static function on_activate() {

        // check WordPress version
        if ( ! version_compare( $GLOBALS[ 'wp_version' ], '4.0', '>=' ) ) {
            deactivate_plugins( RW_Change_Deprecated_Userlogins::$plugin_filename );
            die(
            wp_sprintf(
                '<strong>%s:</strong> ' .
                __( 'This plugin requires WordPress 4.0 or newer to work', RW_Change_Deprecated_Userlogins::get_textdomain() )
                , RW_Change_Deprecated_Userlogins::get_plugin_data( 'Name' )
            )
            );
        }


        // check php version
        if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
            deactivate_plugins( RW_Change_Deprecated_Userlogins::$plugin_filename );
            die(
            wp_sprintf(
                '<strong>%1s:</strong> ' .
                __( 'This plugin requires PHP 5.3 or newer to work. Your current PHP version is %1s, please update.', RW_Change_Deprecated_Userlogins::get_textdomain() )
                , RW_Change_Deprecated_Userlogins::get_plugin_data( 'Name' ), PHP_VERSION
            )
            );
        }

        self::rewrite();

    }

    /**
     * Clean up after deactivation
     *
     * Clean up after deactivation the plugin
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     */
    public static function on_deactivation() {

    }

    /**
     * Clean up after uninstall
     *
     * Clean up after uninstall the plugin.
     * Delete options and other stuff.
     *
     * @since   0.0.1
     * @access  public
     * @static
     * @return  void
     *
     */
    public static function on_uninstall() {



    }
}

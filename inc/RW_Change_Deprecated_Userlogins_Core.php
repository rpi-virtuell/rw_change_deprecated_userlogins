<?php
/**
 *  Class RW_Change_Deprecated_Userlogins_Core
 *
 * Core Functions
 *
 * @package   RW Change Deprecated_Userlogins_Core
 * @author    Joachim Happel
 * @license   GPL-2.0+
 * @link      https://github.com/rpi-virtuell/rw_change_deprecated_userlogins
 */
class RW_Change_Deprecated_Userlogins_Core {



    /**
     * authenticate user against deprecated user_login if login fails and send a message with the new user_login
     *
     * @ince    0.0.1
     * @access  public
     * @static
     * @use_filter  authenticate
     * @return  void
     */

    public static function authenticate($user, $username, $password) {

        if (is_wp_error($user) && !empty($username) && !empty($password)) {

            $deprecated_login = $username;

            global $wpdb;

            $sql =  $wpdb->prepare(
                "
				select user_login from {$wpdb->users_copy} where ID in (
					select user_id from {$wpdb->usermeta_copy} where
						meta_key = 'deprecated_login'
						and meta_value = %s
				)

			",
                $deprecated_login
            );

            $username = $wpdb->get_var( $sql );

            $user = wp_authenticate($username, $password);

            if (!is_wp_error($user)){

                //notify user about changed username

                $message =

                    "Hallo ".$user->display_name.",<br><br>".
                    "Dein bisheriger Benutzername '{$deprecated_login}' musste für reliwerk (das neue rpi-virtuell) " .
                    "aus technischen Gründen geändert werden und heißt nun ".
                    "'$username'. Bitte verwende abjetzt nur diesen geänderten Benutzernamen.<br><br>".
                    "Vielen Dank für dein Verständnis! <br><br>".
                    "Dein rpi-virtuell Technik Team<hr>".
                    "http://about.rpi-virtuell.de";

                wp_mail($user->user_email, sprintf(__('[rpi-virtuell.de login] Dein Benutzername wurde geändert.')), $message);
            }

        }

        return $user;

    }

}

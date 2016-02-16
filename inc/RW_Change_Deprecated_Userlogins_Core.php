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
     * rewirte user_login
     *
     * @ince    0.0.1
     * @access  private
     * @static
     * @action  rw_change_deprecated_userlogins_unregister
     * @return  void
     */

    static $rw_admin_notice ='';

    static function rewrite(){
        global $wpdb;


        if( !get_option('deprecated_logins_rewritten') ){


            /*
            $sql =  "delete from wp_usermeta where meta_key =  'deprecated_login';";
            $success = $wpdb->query( $sql );

            if($success !== false){
                RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= "all deprecated_login entries deleted<br>";
            }
            */

            $sql =  "insert into wp_usermeta (user_id,meta_key,meta_value) select ID, 'deprecated_login', user_login
					 FROM wp_users WHERE user_login Like '% %' OR user_login Like '%\_'	OR user_login Like '\_%'";
            $success = $wpdb->query( $sql );
            if($success !== false){
                RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= 	$success. " deprecated_login entrys in wp_usermeta inserted<br>";
            }

            $sql =  "update wp_users set user_login = replace(replace(replace(user_login,'.','-'),' ','-'),'--','-') where user_login Like '% %';";
            $success = $wpdb->query( $sql );
            if($success !== false){
                RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= 	$success. " user_login entrys with spaces in wp_users where replaced<br>";
            }

            $sql =  "update wp_users set user_login = replace(user_login,'_','') where user_login Like '%\_' or user_login Like '\_%';";
            $success = $wpdb->query( $sql );

            if($success !== false){
                RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= 	$success. " user_login entrys with trailing underscore in wp_users where  replaced<br>";
            }

            update_option ( 'deprecated_logins_rewritten', 1 );

            RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= 	"<hr>Now you can login in with your new and with your old username.<br>";
            RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice .= 	"If you login with your old username you will get a message via E-Mail, that your username ist deprecated.";

            add_action( 'admin_notices', array( 'RW_Change_Deprecated_Userlogins_Core', 'notice') ,99 );



        }

    }

    public static function notice() {
        ?>
        <div class="updated">
            <p><?php echo "<h3>RW Change Deprecated Userlogins</h3><b>Database updated!</b><br>". RW_Change_Deprecated_Userlogins_Core::$rw_admin_notice ; ?></p>
        </div>
        <?php
    }

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
				select user_login from {$wpdb->users} where ID in (
					select user_id from {$wpdb->usermeta} where
						meta_key = 'deprecated_login'
						and meta_value = %s
				)

			",
                $deprecated_login
            );

            $username = $wpdb->get_var( $sql );
			if( !empty($username) ){
				$user = wp_authenticate($username, $password);

    

				if (!is_wp_error($user)){

					//notify user about changed username

					$message =

						"Hallo ".$deprecated_login.",<br><br>".
						"Diese Nachricht erhalten Sie, weil sie sich mit dem Benutzername '{$deprecated_login}' ".
						"im Netzwerk von rpi-virtuell/reliwerk angemeldet haben.".
						"<br>" .
						"Aus technischen Gründen (der Benutzername enthält Punkte, Sonderzeichen oder Leerzeichen) ".
						"musste dieser geändert werden und heißt nun '<b>".$username. "</b>' ".
						"Bitte verwenden Sie zur Anmeldung nur noch den geänderten Benutzernamen.<br><br>".
						"Vielen Dank für dein Verständnis! <br><br>".
						"Dein Technik Team für <a href='http://about.rpi-virtuell.de'>rpi-virtuell</a>";

					if($user->user_email){
						$bool = wp_mail($user->user_email, '[rpi-virtuell.de login] Dein Benutzername wurde geändert.', $message);
					}

				}
			}
        }

        return $user;

    }

}

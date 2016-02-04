<?php
/**
 * @TODO Name des Plugins
 *
 * @package   @TODO RW Demo Plugin
 * @author    @TODO Frank Staude
 * @license   GPL-2.0+
 * @link      @TODO https://github.com/rpi-virtuell/pagetname
 */

/*
 * Plugin Name:       RW Change Deprecated Userlogins
 * Plugin URI:        https://github.com/rpi-virtuell/rw_change_deprecated_userlogins
 * Description:       this plugin rewrites problematic user_login entries and send user a message about the changement
 * Version:           0.0.1
 * Author:            Joachim Happel
 * Author URI:        https://comenius.de
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:       /languages
 * Text Domain:       rw_change_deprecated_userlogins
 * Network:           true
 * GitHub Plugin URI: https://github.com/rpi-virtuell/rw_change_deprecated_userlogins
 * GitHub Branch:     master
 * Requires WP:       4.0
 * Requires PHP:      5.3
 */

// Klassenname
class RW_Change_Deprecated_Userlogins {
    /**
     * Plugin version
     *
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $version = "0.0.1";

    /**
     * Singleton object holder
     *
     * @var     mixed
     * @since   0.0.1
     * @access  private
     */
    static private $instance = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_name = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $textdomain = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_base_name = NULL;

    /**
     * @var     mixed
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_url = NULL;

    /**
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_filename = __FILE__;

    /**
     * @var     string
     * @since   0.0.1
     * @access  public
     */
    static public $plugin_version = '';

    /**
     * Plugin constructor.
     *
     * @since   0.0.1
     * @access  public
     * @uses    plugin_basename
     * @action  rw_change_deprecated_userlogins_init
     */
    public function __construct () {
        // set the textdomain variable
        self::$textdomain = self::get_textdomain();

        // The Plugins Name
        self::$plugin_name = $this->get_plugin_header( 'Name' );

        // The Plugins Basename
        self::$plugin_base_name = plugin_basename( __FILE__ );

        // The Plugins Version
        self::$plugin_version = $this->get_plugin_header( 'Version' );

        // Load the textdomain
        $this->load_plugin_textdomain();

        // Hier Filter und Actions einbinden.
        // alternative Authentifizierung über veraltete userl_logins ermöglichen
        add_action( 'admin_notices',   array( 'RW_Change_Deprecated_Userlogins_Core', 'rewrite') );
        add_filter( 'authenticate',   array( 'RW_Change_Deprecated_Userlogins_Core', 'authenticate'), 30, 3 );

        // do_action( 'rw_change_deprecated_userlogins_init' );
    }

    /**
     * Creates an Instance of this Class
     *
     * @since   0.0.1
     * @access  public
     * @return  Object
     */
    public static function get_instance() {

        if ( NULL === self::$instance )
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Load the localization
     *
     * @since	0.0.1
     * @access	public
     * @uses	load_plugin_textdomain, plugin_basename
     * @filters rw_change_deprecated_userlogins_translationpath path to translations files
     * @return	void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( self::get_textdomain(), false, apply_filters ( 'rw_change_deprecated_userlogins_translationpath', dirname( plugin_basename( __FILE__ )) .  self::get_textdomain_path() ) );
    }

    /**
     * Get a value of the plugin header
     *
     * @since   0.0.1
     * @access	protected
     * @param	string $value
     * @uses	get_plugin_data, ABSPATH
     * @return	string The plugin header value
     */
    protected function get_plugin_header( $value = 'TextDomain' ) {

        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php');
        }

        $plugin_data = get_plugin_data( __FILE__ );
        $plugin_value = $plugin_data[ $value ];

        return $plugin_value;
    }

    /**
     * get the textdomain
     *
     * @since   0.0.1
     * @static
     * @access	public
     * @return	string textdomain
     */
    public static function get_textdomain() {
        if( is_null( self::$textdomain ) )
            self::$textdomain = self::get_plugin_data( 'TextDomain' );

        return self::$textdomain;
    }

    /**
     * get the textdomain path
     *
     * @since   0.0.1
     * @static
     * @access	public
     * @return	string Domain Path
     */
    public static function get_textdomain_path() {
        return self::get_plugin_data( 'DomainPath' );
    }

    /**
     * return plugin comment data
     *
     * @since   0.0.1
     * @uses    get_plugin_data
     * @access  public
     * @param   $value string, default = 'Version'
     *		Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title
     * @return  string
     */
    public static function get_plugin_data( $value = 'Version' ) {

        if ( ! function_exists( 'get_plugin_data' ) )
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        $plugin_data  = get_plugin_data ( __FILE__ );
        $plugin_value = $plugin_data[ $value ];

        return $plugin_value;
    }

}


if ( class_exists( 'RW_Change_Deprecated_Userlogins' ) ) {


    add_action( 'plugins_loaded', array( 'RW_Change_Deprecated_Userlogins', 'get_instance' ) );

    require_once 'inc/RW_Change_Deprecated_Userlogins_Autoloader.php';
    RW_Change_Deprecated_Userlogins_Autoloader::register();

    register_activation_hook( __FILE__, array( 'RW_Change_Deprecated_Userlogins_Installation', 'on_activate' ) );
    register_uninstall_hook(  __FILE__,	array( 'RW_Change_Deprecated_Userlogins_Installation', 'on_uninstall' ) );
    register_deactivation_hook( __FILE__, array( 'RW_Change_Deprecated_Userlogins_Installation', 'on_deactivation' ) );
}

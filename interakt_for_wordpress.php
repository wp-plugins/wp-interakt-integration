<?php // add the admin options page
/*
  Plugin Name: Interakt for WordPress
  Plugin URI: http://interakt.co
  Description: Integrate the <a href="http://interakt.co">Interakt</a> all in one customer engagement platform with your WordPress web app.
  Author: Peeyush Singla
  Author URI: https://twitter.com/peeyush_singla
  Version: 2.1
*/


class PS_Interakt{
    /**
     * Holds the values to be used in the fields callbacks
     */

    public $options;

    /**
     * Start up
     */
    public function __construct()
    {
      $this->options = get_option( 'interakt_plugin_options_name' );
      add_action( 'admin_menu', array( $this, 'interakt_plugin_admin_add_page' ) );
      add_action( 'admin_init', array( $this, 'interakt_plugin_admin_init' ) );
    }

    /**
     * Add options page
     */
    public function interakt_plugin_admin_add_page()
    {
      // This page will be under "Settings"
      add_options_page(
        'Interakt Settings',
        'Interakt Settings',
        'manage_options',
        '__FILE__',
        array( $this, 'interakt_plugin_options_page' )
      );
    }

    /**
     * Options page callback
     */
    public function interakt_plugin_options_page()
    {
      // Set class property
      $this->options = get_option( 'interakt_plugin_options_name' );
      ?>
      <div class="wrap">
        <h2>Configure Interakt App Id</h2>
        <form method="post" action="options.php">
          <?php
            // This prints out all hidden setting fields
            settings_fields( 'interakt_plugin_options_group' );
            do_settings_sections( '__FILE__' );
            submit_button();
          ?>
        </form>
      </div>
      <?php
    }

    /**
     * Register and add settings
     */
    public function interakt_plugin_admin_init()
    {
      register_setting(
        'interakt_plugin_options_group', // Option group
        'interakt_plugin_options_name', // Option name
        array( $this, 'interakt_plugin_options_validate' ) // Sanitize
      );

      add_settings_section(
        'interakt_main_section_id', // ID
        'App Key Setting', // interakt_app_key
        array( $this, 'interakt_main_section_cb' ), // Callback
        '__FILE__' // Page
      );

      add_settings_field(
        'interakt_app_key',
        'Interakt App Id',
        array( $this, 'interakt_app_key_setting' ),
        '__FILE__',
        'interakt_main_section_id'
      );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */

    public function interakt_plugin_options_validate( $input )
    {
      return $input;
    }

    /**
     * Print the Section text
     */
    public function interakt_main_section_cb()
    {
      echo '<h3>Confused! where to get these keys? </h3>';
      echo '<h3><a href="http://docs.interakt.co/integrations/wordpress" target="_blank">Click Here</a> or <a href="mailto:support@interakt.co?Subject=Need help for Interakt Integration with WordPress site" target="_top">Drop us an Email</a><h3>';
    }


    /**
     * Get the settings option array and print one of its values
     */
    public function interakt_app_key_setting()
    {
      printf(
        '<input type="text" id="interakt_app_key" name="interakt_plugin_options_name[interakt_app_key]" size="30" value="%s" />',
        isset( $this->options['interakt_app_key'] ) ? esc_attr( $this->options['interakt_app_key']) : ''
      );
    }

}


//Calling constructor method if user is in admin panel
  if( is_admin() )
    $my_settings_page = new PS_Interakt();



//Calling constructor method if user is in front end.
  add_action('wp_footer', function(){
    $interakt_object = new PS_Interakt();
    $interakt_key = ($interakt_object->options['interakt_app_key']);
    if (!empty($interakt_key)) {
      echo "<script>
        (function() {
        var interakt = document.createElement('script');
        interakt.type = 'text/javascript'; interakt.async = true;
        interakt.src = 'http://cdn.interakt.co/interakt/$interakt_key.js'
        var scrpt = document.getElementsByTagName('script')[0];
        scrpt.parentNode.insertBefore(interakt, scrpt);
        })()
      </script>";
      if ( is_user_logged_in() ) {
        global $current_user;
        get_currentuserinfo();
        $user_name = $current_user->user_login;
        $email = $current_user->user_email;
        $created_at = $current_user->user_registered;
        echo "<script>
          window.mySettings = {
          email: '$email',
          name: '$user_name',
          created_at: '$created_at',
          app_id: '$interakt_key'
          };
        </script>";
      }
    }
  });

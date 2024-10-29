<?php

class AklamatorPrWidget
{


    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function init()
    {

        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public $aklamator_url;
    public $api_data;
    protected $application_id;

    public function __construct()
    {

        $this->aklamator_url = "https://aklamator.com/";
//        $this->aklamator_url = "http://192.168.5.60/aklamator/www/";
        $this->application_id = get_option('aklamatorApplicationID');

        $this->hooks();

    }

    private function hooks(){

        add_filter( 'plugin_row_meta', array($this, 'aklamator_plugin_meta_links'), 10, 2);
        add_filter( "plugin_action_links_".AKLA_PR_PLUGIN_NAME, array($this, 'aklamator_plugin_settings_link') );

        add_action( 'admin_menu', array($this,"adminMenu") );
        add_action( 'admin_init', array($this,"setOptions") );
        add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style_script') );
        add_action( 'after_setup_theme', array($this,'vw_setup_vw_widgets_init_aklamator') );

        if ($this->application_id != "")
            add_filter('the_content', array($this,'bottom_of_every_post'));

        /*
        * Adds featured images from posts to your site's RSS feed output,
        */
        if(get_option('aklamatorFeatured2Feed')){
            add_filter('the_excerpt_rss', array($this,'akla_featured_images_in_rss'), 1000, 1);
            add_filter('the_content_feed', array($this, 'akla_featured_images_in_rss'), 1000, 1);
        }

    }

    function setOptions()
    {

        register_setting('aklamator-options', 'aklamatorApplicationID');
        register_setting('aklamator-options', 'aklamatorPoweredBy');
        register_setting('aklamator-options', 'aklamatorSingleWidgetID');
        register_setting('aklamator-options', 'aklamatorPageWidgetID');
        register_setting('aklamator-options', 'aklamatorSingleWidgetTitle');
        register_setting('aklamator-options', 'aklamatorFeatured2Feed');
        register_setting('aklamator-options', 'aklamatorCategory');

    }

    /*
     * Adds featured images from posts to your site's RSS feed output,
     */
    function akla_featured_images_in_rss($content){
        global $post;
        if (has_post_thumbnail($post->ID)) {
            $featured_images_in_rss_size = 'thumbnail';
            $featured_images_in_rss_css_code = 'display: block; margin-bottom: 5px; clear:both;';
            $content = get_the_post_thumbnail($post->ID, $featured_images_in_rss_size, array('style' => $featured_images_in_rss_css_code)) . $content;
        }
        return $content;
    }

    /*
     * Add setting link on plugin page
     */
    function aklamator_plugin_settings_link($links) {
        $settings_link = '<a href="admin.php?page=aklamator-digital-pr">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /*
     * Activation Hook
     */
    function set_up_options() {
        add_option('aklamatorApplicationID', '');
        add_option('aklamatorPoweredBy', '');
        add_option('aklamatorSingleWidgetID', '');
        add_option('aklamatorPageWidgetID', '');
        add_option('aklamatorSingleWidgetTitle', '');
        add_option('aklamatorFeatured2Feed', 'on');
        add_option('aklamatorWidgets', '');
        add_option('aklamatorCategory', '');
    }

    /*
     * Uninstall Hook
     */
    function aklamator_uninstall() {
        delete_option('aklamatorApplicationID');
        delete_option('aklamatorPoweredBy');
        delete_option('aklamatorSingleWidgetID');
        delete_option('aklamatorPageWidgetID');
        delete_option('aklamatorSingleWidgetTitle');
        delete_option('aklamatorFeatured2Feed');
        delete_option('aklamatorWidgets');
        delete_option('aklamatorCategory');
    }

    /*
     * Add rate and review link in plugin section
     */
    function aklamator_plugin_meta_links($links, $file)
    {
        $plugin = AKLA_PR_PLUGIN_NAME;
        // create link
        if ($file == $plugin) {
            return array_merge(
                $links,
                array('<a href="https://wordpress.org/support/plugin/aklamator-digital-pr/reviews" target=_blank>Please rate and review</a>')
            );
        }
        return $links;
    }

    public function adminMenu()
    {
        add_menu_page('Aklamator Digital PR', 'Aklamator PR', 'manage_options', 'aklamator-digital-pr', array($this, 'createAdminPage'), AKLA_PR_PLUGIN_URL . 'images/aklamator-icon.png');
    }

    

    public function getSignupUrl()
    {
        $user_info =  wp_get_current_user();

        return $this->aklamator_url . 'login/application_id?utm_source=wordpress&utm_medium=wpclassic&e=' . urlencode(get_option('admin_email')) .
        '&pub=' .  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']).
        '&un=' . urlencode($user_info->user_login). '&fn=' . urlencode($user_info->user_firstname) . '&ln=' . urlencode($user_info->user_lastname) .
        '&pl=digital-pr&return_uri=' . admin_url("admin.php?page=aklamator-digital-pr");

    }

    function load_custom_wp_admin_style_script($hook) {

        if ( 'toplevel_page_aklamator-digital-pr' != $hook ) {
            return;
        }

        /*
         * We are calling api only when we at this plugin page, not for all other pages
         */

        if ($this->application_id !== '') {
            $this->api_data = $this->addNewWebsiteApi();

            $this->populate_with_default();

            if($this->api_data->flag){
                update_option('aklamatorWidgets', $this->api_data);
            }
        }

        // Load necessary css files
        wp_enqueue_style('custom-wp-admin', AKLA_PR_PLUGIN_URL . 'assets/css/admin-style.css', false, '1.0.0' );
        wp_enqueue_style('dataTables-plugin', AKLA_PR_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.css', false, '1.10.5', false );

        // Load script files
        wp_enqueue_script('dataTables_plugin', AKLA_PR_PLUGIN_URL . 'assets/dataTables/jquery.dataTables.min.js', array('jquery'), '1.10.5', true );
        wp_register_script('my_custom_akla_script', AKLA_PR_PLUGIN_URL . 'assets/js/main.js', array('jquery'), '1.0', true);

        $data = array(
            'site_url' => $this->aklamator_url
        );
        wp_localize_script('my_custom_akla_script', 'akla_vars', $data);
        wp_enqueue_script('my_custom_akla_script');

    }


    private function populate_with_default(){

        if(isset($this->api_data->data) && $this->api_data->flag){

            if (get_option('aklamatorSingleWidgetID') !== 'none') {

                if (get_option('aklamatorSingleWidgetID') == '') {
                    if ($this->api_data->data[0]) {
                        update_option('aklamatorSingleWidgetID', $this->api_data->data[0]->uniq_name);
                    }
                }
            }

            if (get_option('aklamatorPageWidgetID') !== 'none') {

                if (get_option('aklamatorPageWidgetID') == '') {
                    if ($this->api_data->data[0]) {
                        update_option('aklamatorPageWidgetID', $this->api_data->data[0]->uniq_name);
                    }
                }
            }
        }
    }

    function bottom_of_every_post($content){

        /*  we want to change `the_content` of posts, not pages
            and the text file must exist for this to work */

        if (is_single()){
            $widget_id = get_option('aklamatorSingleWidgetID');
        }elseif (is_page()) {
            $widget_id = get_option('aklamatorPageWidgetID');
        }else{

            /*  if `the_content` belongs to a page or our file is missing
                the result of this filter is no change to `the_content` */

            return $content;
        }

        $return_content = $content;

        if(strlen($widget_id) >=7){
            $title = "";
            if(get_option('aklamatorSingleWidgetTitle') !== ''){
                $title .= "<h2>". get_option('aklamatorSingleWidgetTitle'). "</h2>";
            }
            /*  append the text file contents to the end of `the_content` */

            $return_content.=  $title. $this->show_widget($widget_id);

        }

        return $return_content;



    }

    public function show_widget($widget_id){

        $code  = '<!-- Start Aklamator Widget -->';
        $code .= '<div id="akla'.$widget_id.'"></div>';
        $code .= '<script>(function(d, s, id) ';
        $code .= '{ var js, fjs = d.getElementsByTagName(s)[0];';
        $code .= 'if (d.getElementById(id)) return;';
        $code .= 'js = d.createElement(s); js.id = id;';
        $code .= 'js.src = "'.$this->aklamator_url.'widget/'.$widget_id.'";';
        $code .= 'fjs.parentNode.insertBefore(js, fjs);';
        $code .= '}(document, \'script\', \'aklamator-'.$widget_id.'\'))</script>';
        $code .= '<!-- end -->';
        return $code;

    }

    private function addNewWebsiteApi()
    {

        if (!is_callable('curl_init')) {
            return;
        }


        $service = $this->aklamator_url . "wp-authenticate/user";
        $p['ip'] = $_SERVER['REMOTE_ADDR'];
        $p['domain'] = site_url();
        $p['source'] = "wordpress";
        $p['AklamatorApplicationID'] = get_option('aklamatorApplicationID');

        $aklamatorfeedAppend = "";
        if(get_option('aklamatorCategory') != -1 && get_option('aklamatorCategory') != "")
        {
            $aklamatorfeedAppend = '&cat=' . get_option('aklamatorCategory');
            echo 'proba';
        }
        $p['aklamatorfeedURL'] = site_url() . '?feed=rss2' . $aklamatorfeedAppend;

        
        $client = curl_init();

        curl_setopt($client, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($client, CURLOPT_HEADER, 0);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($client, CURLOPT_URL, $service);

        if (!empty($p)) {
            curl_setopt($client, CURLOPT_POST, count($p));
            curl_setopt($client, CURLOPT_POSTFIELDS, http_build_query($p));
        }

        $data = curl_exec($client);
        if (curl_error($client) != "") {
            $this->curlfailovao = 1;
        } else {
            $this->curlfailovao = 0;
        }

        curl_close($client);

        
        $data = json_decode($data);

        return $data;

    }

    public function createAdminPage()
    {
       require_once AKLA_PR_PLUGIN_DIR."views/admin-page.php";
    }

    function vw_setup_vw_widgets_init_aklamator() {
        add_action( 'widgets_init', array($this, 'vw_widgets_init_aklamator') );
    }

    function vw_widgets_init_aklamator() {
        register_widget( 'Wp_widget_aklamator' );
    }
}

<?php
# kleeja plugin
# 
# version: 1.0
# developer: Mitan Omar

# prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit;
}

# plugin basic information
$kleeja_plugin['Plugins_updater']['information'] = array(
    # the casual name of this plugin, anything can a human being understands
    'plugin_title' => array(
        'en' => 'Plugins updater',
        'ar' => 'محدث الاضافات'
    ),
    # who wrote this plugin?
    'plugin_developer' => 'Mitan Omar',
    # this plugin version
    'plugin_version' => '1.1',
    /*
     * plugin_check_version_link : if your plugin is not hosted in kleeja packages ,
     * you can write a link to get the last version from it .
     * you dont need to write your scource code if you have a paid version from your plugin
     * you can only write : 
                            'plugin_version' => '1.0',
     * and kleeja will read it .
     * if you did not select it , we will check from kleeja github link
     */
    'plugin_check_version_link' => 'https://raw.githubusercontent.com/MitanOmar/plugins_updater/master/init.php',

    # explain what is this plugin, why should i use it?
    'plugin_description' => array(

        'en' => '· Checks the version of the plugins and identifies the plugins that need to be updated and update the plugins',

        'ar' => 'فاحص لاصدار الاضافات و تحديد الاضافات التي تحتاج الى تحديث و تحديثها'
    ),

    # min version of kleeja that's required to run this plugin
    'plugin_kleeja_version_min' => '3.0',
    # max version of kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => "3.9",
    # should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0
);

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['Plugins_updater']['first_run']['ar'] = "
شكراً لاستخدامك هذه الإضافة قم بمراسلتنا بالأخطاء عند ظهورها على الرابط: <br>
https://github.com/awssat/kleeja/issues
<hr>
<br>
<h3>لاحظ:</h3>
<b>الاضافة تقوم بالتحقق من الاضافات الموجودة لديك ( المفعلة و غير المفعلة ) و تحدثها</b>
<br>
<b>تجد الإضافة في صفحة: فحص عن تحديثات-> تحقق من تحديث الاضافات</b>
";

$kleeja_plugin['Plugins_updater']['first_run']['en'] = "
Thanks for using this plugin, to report bugs contact us: 
    <br>
    https://github.com/awssat/kleeja/issues
    <hr>
    <br>
    <h3>Note:</h3>
    <b>The plugin checks your existing plugins (active and inactive) and updating it</b>
    <br>
    <b>You can find the plugin at: Check for updates -> Check Plugins Update</b>
";

# plugin installation function
$kleeja_plugin['Plugins_updater']['install'] = function ($plg_id) {

    global $SQL , $dbprefix;

    $SQL->query("ALTER TABLE `{$dbprefix}stats` ADD `kleeja_github_cash` INT NOT NULL AFTER `lastuser`;");

    // add language varibles
    add_olang(array(
        'CHECK_PLUGIN_UPDATE' => 'Check Plugins Update',
        'ERR_CONN' => 'Error in the connection with github , please try later',
        'U_PLG_OK' => 'you have the last version .',
        'U_PLG_NEW' => 'Error : your version is newer from our version , please contact us .',
        'U_PLG_OLD' => 'Error : you have a old version please update it . ',
        'PLG_GITHUB_ERR' => 'Error : we did not find this plugin in github .',
        'NO_INIT_FILE' => 'Error : we did not find ( init.php ) file in the plugin folder',
        'U_VERSION' => 'your version',
        'AP_VERSION' => 'Approved version',
        'USE_ONLY_REQ' => 'Use this feature only when requested',
        'FL_DEL_SUCCESS' => 'THE FILE IS DELETED SUCCESSFULY',
        'FL_UPDT_SUCCESS' => 'THE FILE IS UPDATED SUCCESSFULY',
        'FIX' => 'fix',
        'PLG_UPDTER_PLG_CACH_MP' => 'Plugin Updater - plugins map cashe',
        'LST_UPDT' => 'Last Update',
        'UPDT' => 'Update',
        'PLG_UPDT_SUCCESS' => 'the plugin had successfuly updated',
        'NOT_KLEEJA_GIT_PLG' => 'the plugin is not hosted in kleeja github',
        'NOT_U_PLG' => 'you dont have a plugin with this name',
    ),
        'en',
        $plg_id);


    add_olang(array(
        'CHECK_PLUGIN_UPDATE' => 'تحقق من تحديث الاضافات',
        'ERR_CONN' => 'حدث خطأ في الاتصال بـ github ، يرجى المحاولة لاحقًا',
        'U_PLG_OK' => 'لديك الإصدار الأخير.',
        'U_PLG_NEW' => 'خطأ: الإصدار الخاص بك أحدث من إصدارنا ، يرجى الاتصال بنا.',
        'U_PLG_OLD' => 'خطأ: لديك نسخة قديمة , يرجى تحديثها.',
        'PLG_GITHUB_ERR' => 'خطأ: لم نعثر على هذا الاضافة في github.',
        'NO_INIT_FILE' => 'خطأ: لم نعثر على ملف (init.php) في مجلد الاضافة',
        'U_VERSION' => 'الاصدار الخاص بك',
        'AP_VERSION' => 'الاصدار المعتمد',
        'FL_DEL_SUCCESS' => 'تم جذف الملف بنجاح',
        'FL_UPDT_SUCCESS' => 'تم إصلاح الملف بنجاح',
        'FIX' => 'إصلاح',
        'PLG_UPDTER_PLG_CACH_MP' => 'محدث الاضافات - الملف المؤقت لخريطة الاضافات',
        'LST_UPDT' => 'آخر تحديث',
        'UPDT' => 'تحديث',
        'USE_ONLY_REQ' => 'استخدم هذه الميزة عندما يتم طلب ذلك منك فقط',
        'PLG_UPDT_SUCCESS' => 'تم تحديث الإضافة بنجاح',
        'NOT_KLEEJA_GIT_PLG' => 'لم يتم العثور على الإضافة ضمن حزمة كليجا',
        'NOT_U_PLG' => 'ليس لديك إضافة بهذا الاسم',
    ),
        'ar',
        $plg_id);


    
};


//plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['Plugins_updater']['update'] = function ($old_version, $new_version) {
    // if(version_compare($old_version, '0.5', '<')){
    // 	//... update to 0.5
    // }
    //
    // if(version_compare($old_version, '0.6', '<')){
    // 	//... update to 0.6
    // }

    //you could use update_config, update_olang
};


# plugin uninstalling, function to be called at uninstalling
$kleeja_plugin['Plugins_updater']['uninstall'] = function ($plg_id) {

    delete_olang(null, null, $plg_id);

    global $SQL , $dbprefix;

    $SQL->query("ALTER TABLE `{$dbprefix}stats` DROP `kleeja_github_cash`;");

};


# plugin functions
$kleeja_plugin['Plugins_updater']['functions'] = array(

    'require_admin_page_end_p_check_update' => function ($args) {

        global $olang;
 // print_r($lang);
        $stylee = 'check_plugin_update';

        $styleePath = dirname(__FILE__);

        $go_menu = $args["go_menu"];

        $current_smt = $args["current_smt"];

        $go_menu["smt"] = array('name'=> $olang["CHECK_PLUGIN_UPDATE"], 'link'=> basename(ADMIN_PATH) . '?cp=p_check_update&amp;smt=check_plugin_update', 'goto'=>'check_plugin_update', 'current'=> $current_smt == 'check_plugin_update');
    ///////////////////////////////



if ($args["_GET"]["smt"] == "check_plugin_update") {


    $check_github_connection = fetch_remote_file("https://raw.githubusercontent.com/awssat/kleeja/master/includes/version.php");

    if ( !$check_github_connection ) {

        $connection_error = true;

        $output = null;

    }else {

        $connection_error = false;

        $hosted_plugin = get_hosted_plugin( "../" . KLEEJA_PLUGINS_FOLDER );

        $plugin_initFile = get_plugin_init_file( $hosted_plugin , "../" . KLEEJA_PLUGINS_FOLDER );
        
        $plugin_init_github_link = plugin_init_github_link( $plugin_initFile );
        
        $check_plugin_version = check_plugin_version( $plugin_init_github_link );

        $output = $check_plugin_version ;

        
    }

    // update plugin ;
}elseif ($args["_GET"]["smt"] == "download_plugin" && isset( $args["_GET"]["plugin_name"] )) {

    $check_github_connection = fetch_remote_file("https://raw.githubusercontent.com/awssat/kleeja/master/includes/version.php");

    if ( $check_github_connection) {
        
        $check_plugin_in_github = fetch_remote_file("https://raw.githubusercontent.com/awssat/kleeja/master/plugins/" .$args["_GET"]["plugin_name"]. "/init.php");

        if ($check_plugin_in_github) // the plugin is hosted in kleeja
        {
            
        $hosted_plugin = get_hosted_plugin( "../" . KLEEJA_PLUGINS_FOLDER );
    
        if (in_array($args["_GET"]["plugin_name"] , $hosted_plugin )) {
    
            $plugins_data = file_get_contents( dirname(__FILE__) . '/pluginsMap.json' );

            // checking plugin map

            $plugins_data = json_decode($plugins_data , true);

            delete_plugin_folder( '../' . KLEEJA_PLUGINS_FOLDER . '/' . $args["_GET"]["plugin_name"]);

            $update_plugin = new down_plugin_zip( $args["_GET"]["plugin_name"] , $plugins_data[$args["_GET"]["plugin_name"]]);

            $update_plugin->done();

            $update_plugin->clean();

            $update_msg = $olang['PLG_UPDT_SUCCESS'];

            $update_err = FALSE;
    
        }else 
        {
    
            $update_msg = $olang['NOT_U_PLG'];
            $update_err = true;
    
        }
     
        }else  // the plugin is not hosted in kleeja , I'm scared to make this option :(
        {
            $update_msg = $olang['NOT_KLEEJA_GIT_PLG'];
            $update_err = true;
        }

    }else 
    {
        $update_msg = $olang['ERR_CONN'];
        $update_err = true;
    }
}
    /////////////////////////////////


    return compact("go_menu" , "stylee" , "styleePath" , "update_msg" , "update_err" , "output" , "connection_error");   



    },

    'begin_admin_page' => function($args){

        global $SQL , $dbprefix;

        $cashe_file = dirname(__FILE__) . '/pluginsMap.json';

        if ( ! file_exists( $cashe_file )) 
        {
            $check_github_connection = fetch_remote_file("https://raw.githubusercontent.com/awssat/kleeja/master/includes/version.php");
            
            if ( $check_github_connection ) 
            {

                $plugin_cashe = make_github_cash();

                $plugin_cashe = json_encode( $plugin_cashe );
    
                file_put_contents( $cashe_file , $plugin_cashe );
    
                $update_query	= array(
                    'UPDATE'	=> "{$dbprefix}stats",
                    'SET'		=> "kleeja_github_cash = " . time() ,
                );
    
                $SQL->build($update_query);
            }


        }else {

            $query	= array(
                'SELECT'	=> 'kleeja_github_cash',
                'FROM'		=> "`{$dbprefix}stats`",
            );

            $cashing_time = $SQL->fetch_array($SQL->build($query))['kleeja_github_cash'];

            if ( ( $cashing_time + 3720 ) < time() ) // 3720 = 1 hour and 2 min .
            {

                unlink($cashe_file);

                $plugin_cashe = make_github_cash();

                $plugin_cashe = json_encode( $plugin_cashe );

                file_put_contents( $cashe_file , $plugin_cashe );
    
                $update_query	= array(
                    'UPDATE'	=> "{$dbprefix}stats",
                    'SET'		=> "kleeja_github_cash = " . time() ,
                );
                
                $SQL->build($update_query);
            }

        }

    } ,


    'require_admin_page_end_r_repair' => function($args){

        global $SQL , $dbprefix ;

        $stylee = 'repair' ;

        $styleePath = dirname(__FILE__) ;

        $update_plugins_cache_link		= basename(ADMIN_PATH) . '?cp=r_repair&amp;case=update_plugins_cache&amp;' . $args['GET_FORM_KEY'];
        $delete_plugins_cache_link		= basename(ADMIN_PATH) . '?cp=r_repair&amp;case=delete_plugins_cache&amp;' . $args['GET_FORM_KEY'];

        $query	= array(
            'SELECT'	=> 'kleeja_github_cash',
            'FROM'		=> "`{$dbprefix}stats`",
        );

        $cashing_time = $SQL->fetch_array($SQL->build($query))['kleeja_github_cash'];

        if ( $cashing_time !== '') 
        {
            $cashing_date = getDate( $cashing_time ) ;

            $last_caching_date = $cashing_date['year'] . '/' . $cashing_date['mon'] . '/' . $cashing_date['mday'] ;
            $last_caching_date .= ' -- ' . $cashing_date['hours'] . ':' . $cashing_date['minutes'] . ':' . $cashing_date['seconds'];
        }


        return compact('stylee' , 'styleePath' , 'update_plugins_cache_link' , 'delete_plugins_cache_link' , 'last_caching_date');
    } ,


    'require_admin_page_begin_r_repair' => function($args){

        global $SQL , $dbprefix , $lang , $olang ;

        if ( isset($args['_GET']['case']) && isset($args['_GET']['formkey']) && $args['_GET']['case'] == 'update_plugins_cache') 
        {
            if(!kleeja_check_form_key_get('REPAIR_FORM_KEY'))
            {

                kleeja_admin_err($lang['INVALID_GET_KEY'], true, '', true, basename(ADMIN_PATH) . '?cp=r_repair', 3);
                exit;

            }
            
            
                $Approved_plugins_map = fetch_remote_file('https://raw.githubusercontent.com/MitanOmar/plugins_updater/master/pluginsMap.json');

                if ( $Approved_plugins_map ) 
                {
                    if ( file_exists( dirname(__FILE__) . '/pluginsmap.json' ) ) 
                    {

                        unlink( dirname(__FILE__) . '/pluginsmap.json' );

                    }

                    if (file_put_contents( dirname(__FILE__) . '/pluginsmap.json' , $Approved_plugins_map) ) 
                    {
                        
                        $update_query	= array(
                            'UPDATE'	=> "{$dbprefix}stats",
                            'SET'		=> "kleeja_github_cash = " . time() ,
                        );
                        
                        $SQL->build($update_query);

                        kleeja_admin_info( $olang['FL_UPDT_SUCCESS'], true, '', true, basename(ADMIN_PATH) . '?cp=r_repair', 3);

                    }
                }
            


        }elseif ( isset($args['_GET']['case']) && isset($args['_GET']['formkey']) && $args['_GET']['case'] == 'delete_plugins_cache') 
        {
            if(!kleeja_check_form_key_get('REPAIR_FORM_KEY'))
            {

                kleeja_admin_err($lang['INVALID_GET_KEY'], true, '', true, basename(ADMIN_PATH) . '?cp=r_repair', 3);
                exit;

            }

            if ( file_exists( dirname(__FILE__) . '/pluginsmap.json' ) ) 
            {

                unlink( dirname(__FILE__) . '/pluginsmap.json' );

            }

            kleeja_admin_info( $olang['FL_DEL_SUCCESS'] , true, '', true, basename(ADMIN_PATH) . '?cp=r_repair', 3);
            
        }
    }
        



);

require_once dirname(__FILE__) . '/functions.php';

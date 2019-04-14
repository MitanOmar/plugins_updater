<?php
# kleeja plugin
# 
# version: 1.0
# developer: Mitan Omar & Kleeja Team

# prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit;
}

# plugin basic information
$kleeja_plugin['plugins_updater']['information'] = array(
    # the casual name of this plugin, anything can a human being understands
    'plugin_title' => array(
        'en' => 'Plugins updater',
        'ar' => 'محدث الاضافات'
    ),
    # who wrote this plugin?
    'plugin_developer' => 'Mitan Omar & Kleeja Team',
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

    # Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '2.4',
    # Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '3.0.2',
    # should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0
);

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['plugins_updater']['first_run']['ar'] = "
شكراً لاستخدامك هذه الإضافة قم بمراسلتنا بالأخطاء عند ظهورها على الرابط: <br>
https://github.com/awssat/kleeja/issues
<hr>
<br>
<h3>لاحظ:</h3>
<b>الاضافة تقوم بالتحقق من الاضافات الموجودة لديك ( المفعلة و غير المفعلة ) و تحدثها</b>
<br>
<b>تجد الإضافة في صفحة: فحص عن تحديثات-> تحقق من تحديث الاضافات</b>
";

$kleeja_plugin['plugins_updater']['first_run']['en'] = "
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
$kleeja_plugin['plugins_updater']['install'] = function ($plg_id) {

    global $SQL , $dbprefix;

    add_config('plgupdt_lastcheck', 0);

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
        'PLG_UPDTER_PLG_CACH_MP' => 'Plugin Updater - plugins map cache',
        'LST_UPDT' => 'Last Update',
        'UPDT' => 'Update',
        'PLG_UPDT_SUCCESS' => 'the plugin had successfuly updated',
        'PLG_UPDT_ERROR' => 'the plugin update failed',
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
        'PLG_UPDT_ERROR' => 'فشل تحديث الإضافة!',
        'NOT_KLEEJA_GIT_PLG' => 'لم يتم العثور على الإضافة ضمن حزمة كليجا',
        'NOT_U_PLG' => 'ليس لديك إضافة بهذا الاسم',
    ),
        'ar',
        $plg_id);


    
};


//plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['plugins_updater']['update'] = function ($old_version, $new_version) {
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
$kleeja_plugin['plugins_updater']['uninstall'] = function ($plg_id) {
    delete_config('plgupdt_lastcheck');
    delete_olang(null, null, $plg_id);
};


# plugin functions
$kleeja_plugin['plugins_updater']['functions'] = array(
    'not_exists_p_check_plugins_update' => function () {
        $include_alternative = dirname(__FILE__) . '/p_check_plugins_update.php';

        return compact('include_alternative');
    },

    'require_admin_page_end_p_check_update' => function ($args) {
        global $olang;
        $go_menu = $args["go_menu"];
        $current_smt = $args["current_smt"];
        $go_menu["check_plugin_update"] = array('name'=> $olang["CHECK_PLUGIN_UPDATE"], 'link'=> basename(ADMIN_PATH) . '?cp=p_check_plugins_update', 'goto'=>'check_plugin_update', 'current'=> $current_smt == 'check_plugin_update');

        return compact("go_menu");
    },

    'begin_admin_page' => function($args) {
        $adm_extensions = $args['adm_extensions'];
        $ext_expt = $args['ext_expt'];
        $adm_extensions[] = 'p_check_plugins_update';
        $ext_expt[] = 'p_check_plugins_update';

        return compact('adm_extensions', 'ext_expt');
    }
);


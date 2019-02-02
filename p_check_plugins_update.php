<?php

// not for directly open
if (!defined('IN_ADMIN'))
{
	exit();
}


require_once dirname(__FILE__).'/functions.php';

$stylee	= "check_plugin_update";
$styleePath = dirname(__FILE__);
$current_smt	= preg_replace('/[^a-z0-9_]/i', '', g('smt', 'str', 'general'));
$update_link = $config['siteurl'] . 'install/update.php?lang=' . $config['language'];


if ($current_smt == "check"):

    $continue = true;

    if(class_exists('ZipArchive'))
    {
        if(($config['plgupdt_lastcheck'] > 0 && time() - intval($config['plgupdt_lastcheck']) > 3600 * 24) 
            || ! file_exists(PATH . 'cache/kleeja-master.zip'))
        {
            if(file_exists(PATH . 'cache/kleeja-master.zip'))
            {
                kleeja_unlink(PATH . 'cache/kleeja-master.zip');
                delete_plugin_folder(PATH . 'cache/kleeja-master');
            }

            fetch_remote_file('https://github.com/awssat/kleeja/archive/master.zip', PATH . 'cache/kleeja-master.zip', 60, false, 10, true);

            if(! file_exists(PATH . 'cache/kleeja-master.zip'))
            {
                header('HTTP/1.1 500 Internal Server Error');
                $adminAjaxContent = $olang['ERR_CONN'];
                $continue = false;
            }

            if($continue):
                $zip = new ZipArchive;
                if ($zip->open(PATH . 'cache/kleeja-master.zip') === true)
                {
                    if(! $zip->extractTo(PATH . 'cache'))
                    {
                        header('HTTP/1.1 500 Internal Server Error');
                        $adminAjaxContent = sprintf($lang['EXTRACT_ZIP_FAILED'], 'cache');
                        $continue = false;
                    }

                    update_config('plgupdt_lastcheck', time());

                    $zip->close();
                }
                else
                {
                    header('HTTP/1.1 500 Internal Server Error');
                    $adminAjaxContent = sprintf($lang['EXTRACT_ZIP_FAILED'], 'cache');
                    $continue = false;
                }
            endif;
        }
    }
    else
    {
        header('HTTP/1.1 500 Internal Server Error');
        $adminAjaxContent = $lang['NO_ZIP_ARCHIVE'];
        $continue = false;
    }


    if($continue):
        if (! file_exists(PATH . 'cache/kleeja-master/index.php')) 
        {
            header('HTTP/1.1 500 Internal Server Error');
            $adminAjaxContent = $olang['ERR_CONN'];
        }
        else
        {
            $current_plugins = get_plugins_list("../" . KLEEJA_PLUGINS_FOLDER);

            $check_result = check_plugin_version($current_plugins);

            $adminAjaxContent = $check_result;
        }
    endif;

//show
elseif ($current_smt == "general"):

    //nothing ... 

// update plugin
elseif($current_smt == "update" && ig('plugin')):

    $plugin = g('plugin');

    if(file_exists(PATH . 'cache/kleeja-master/plugins/' . $plugin . '/init.php'))
    {
        if (file_exists(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '/init.php')) 
        {
            rename(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin, PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin.'_copy');

            if(file_exists(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '/init.php')) 
            {
                delete_plugin_folder(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin);
            }

            copy_plugin_folder(
                PATH . 'cache/kleeja-master/plugins/' . $plugin,
                PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin
            );

            if(file_exists(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '/init.php'))
            {
                delete_plugin_folder(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '_copy');
                $adminAjaxContent = htmlspecialchars($plugin) . ': ' . $olang['PLG_UPDT_SUCCESS'];
            }
            else if(file_exists(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '_copy/init.php'))
            {
                rename(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '_copy', PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin);
                header('HTTP/1.1 500 Internal Server Error');
                $adminAjaxContent = htmlspecialchars($plugin) . ': '. $olang['PLG_UPDT_ERROR'];
            }
        }
        else
        {
            header('HTTP/1.1 500 Internal Server Error');
            $adminAjaxContent = htmlspecialchars($plugin) . ': ' . $olang['NOT_U_PLG'];
        }
    }
    // the plugin is not hosted in kleeja , I'm scared to make this option :(
    else
    {
        header('HTTP/1.1 500 Internal Server Error');
        $adminAjaxContent = htmlspecialchars($plugin) . ': ' . $olang['NOT_KLEEJA_GIT_PLG'];
    }

endif;
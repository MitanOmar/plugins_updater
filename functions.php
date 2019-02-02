<?php

if (! function_exists("get_plugins_list")) {
    function get_plugins_list($plugin_dir = "./plugins/")
    {
        return array_filter(scandir($plugin_dir), function($d) {
            return ! in_array($d, ['.DS_Store', '.', '..', 'index.html']);
        });
   }
}

if (! function_exists("check_plugin_version")) {
    function check_plugin_version($current_plugins = array())
    {
        global $olang;

        $plugin_result = [];

        foreach ($current_plugins as $plugin)
        {
            $plugin_result['pluginName'] = $plugin;
    
            if (file_exists(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '/init.php'))
            {
                $current_content = file_get_contents(PATH . KLEEJA_PLUGINS_FOLDER . '/' . $plugin . '/init.php');

                preg_match("/'plugin_check_version_link'\s{0,4}=>\s{0,4}\'([^\']+)\'\,/" , $current_content , $update_link);

                $online_content = '';

                if (count($update_link) > 1)
                {
                    $online_content = fetch_remote_file($update_link[1] , false , 30);
                }
                else if(file_exists(PATH . 'cache/kleeja-master/plugins/' . $plugin . '/init.php'))
                {
                    $online_content = file_get_contents(PATH . 'cache/kleeja-master/plugins/' . $plugin . '/init.php');
                    $plugin_result['from_kleeja_github'] = true;
                }
  
                if (! empty($online_content))
                {
                    preg_match("/'plugin_version'\s{0,4}=>\s{0,4}\'([^\']+)\'\,/" , $online_content , $online_version);
                    preg_match("/'plugin_version'\s{0,4}=>\s{0,4}\'([^\']+)\'\,/" , $current_content , $local_version);
        
                    $online_version = trim(htmlspecialchars($online_version[1]));
                    $local_version = trim(htmlspecialchars($local_version[1]));
        
                    if (version_compare(strtolower($local_version), strtolower($online_version), '<'))
                    {
                        $report	= $olang['U_PLG_OLD'];
                        $error = "warning";
                        $plugin_result["local_version"] = $local_version;
                        $plugin_result["online_version"] = $online_version;
                        $plugin_result["update_link"] = $plugin_result['from_kleeja_github'] ? basename(ADMIN_PATH) . '?cp=p_check_update&amp;smt=download_plugin&amp;plugin_name=' . $plugin : '' ;
                    }
                    else if (version_compare(strtolower($local_version), strtolower($online_version), '='))
                    {
                        $report	= $olang['U_PLG_OK'];
                        $error = "success";
                        $plugin_result["local_version"] = $local_version ;
                        $plugin_result["online_version"] = $online_version ;
                    }
                    else if (version_compare(strtolower($local_version), strtolower($github_version), '>'))
                    {
                        $report	= $olang['U_PLG_NEW'];
                        $error = "warning";
                        $plugin_result["local_version"] = $local_version;
                        $plugin_result["online_version"] = $online_version;
                        $plugin_result["update_link"] = $plugin_result['from_kleeja_github'] ? basename(ADMIN_PATH) . '?cp=p_check_update&amp;smt=download_plugin&amp;plugin_name=' . $plugin["pluginName"] : '' ;
                    }
                }
                else
                {
                    $report	= $olang['PLG_GITHUB_ERR'];
                    $error = "danger";
                }
            }
            else
            {
                $report = $olang['NO_INIT_FILE'];
                $error = "danger";
            }

            $plugin_result["report"] = $report;
            $plugin_result["error"] = $error;

            $return[] = $plugin_result;
        }

        return $return;
    }
}

if (! function_exists('delete_plugin_folder')) {
    function delete_plugin_folder($dir)
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) 
        {
            if ($file->isLink()) 
            {
                unlink($file->getPathname());
            }
            else if ($file->isDir()) 
            {
                rmdir($file->getPathname());
            }
            else
            {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }
}


if (! function_exists('copy_plugin_folder')) {
    function copy_plugin_folder($src, $dst)
    {
        if (file_exists($dst)) 
        {
            delete_plugin_folder($dst);
        }

        if (is_dir($src)) 
        {
            mkdir($dst);
            $files = scandir($src);
            foreach ($files as $file) 
            {
                if ($file != '.' && $file != '..') 
                {
                    copy_plugin_folder("$src/$file", "$dst/$file");
                }
            }
        }
        elseif (file_exists($src)) 
        {
            copy($src, $dst);
        }
    }
}
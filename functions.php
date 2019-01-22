<?php

if ( ! function_exists("get_hosted_plugin")) {

    function get_hosted_plugin($plugin_dir = "./plugins/"){

        $direction = scandir($plugin_dir);

    if (count($direction) > 2) {

        foreach ($direction as  $files) {

            if ($files !== "." && $files !== ".." && $files !== "index.html") {

                $return_file[] = $files;

            }

        }

    }

    return $return_file;

   }

}




if ( ! function_exists("get_plugin_init_file")) {

    function get_plugin_init_file($locatedPlugin = array() , $pluginDir = "./plugins" ){

        foreach ($locatedPlugin as $pluginName) {

            if (file_exists( $pluginDir ."/" . $pluginName ."/" . "init.php")) {

                $return_init_dir[] = array( "pluginName" => $pluginName ,"init" => $pluginName ."/" . "init.php");

            }else {

                $return_init_dir[] = array( "pluginName" => $pluginName ,"init" => "Not Found");

            }

        }

        return $return_init_dir;
    }

}

if ( ! function_exists("plugin_init_github_link")) {

    function plugin_init_github_link( $hosted_plugin = array()){
        
        $github_link = "https://raw.githubusercontent.com/awssat/kleeja/master/";

        foreach ($hosted_plugin as $plugin_info) {

            if ( isset($plugin_info["init"])) {
                
                if ( $plugin_info["init"] !== "Not Found") {

                    $plugin_info["github"] = $github_link . "plugins/" . $plugin_info["init"];

                }

            }

            $return[] = $plugin_info;

        }

        return $return;
    }
}


if (!function_exists("check_plugin_version")) {

    function check_plugin_version( $plugins_info = array()){

        global $olang;
        
        foreach ($plugins_info as $plugin) {
            
            if ($plugin["init"] !== "Not Found") {

                $local_content = file_get_contents("../" . KLEEJA_PLUGINS_FOLDER . "/" . $plugin["init"]);

                preg_match("/'plugin_check_version_link' => {1,4}\'([^\']+)\'\,/" , $local_content , $update_link);
                
                if (count($update_link) > 1) {

                    $online_content = fetch_remote_file($update_link[1] , false , 30);

                }
                else {
                    $online_content = fetch_remote_file($plugin["github"] , false , 30);
                    $plugin['from_kleeja_github'] = true;
                }
                
                
                if ($online_content) {

                    preg_match("/'plugin_version' => {1,4}\'([^\']+)\'\,/" , $online_content , $online_version);
                    preg_match("/'plugin_version' => {1,4}\'([^\']+)\'\,/" , $local_content , $local_version);
        
                    $online_version = trim(htmlspecialchars($online_version[1]));
                    $local_version = trim(htmlspecialchars($local_version[1]));
        
                    if (version_compare(strtolower($local_version), strtolower($online_version), '<'))
                    {
                        $report	= $olang['U_PLG_OLD'];
                        $error = "warning";
                        $plugin["local_version"] = $local_version ;
                        $plugin["online_version"] = $online_version ;
                        $plugin["update_link"] = $plugin['from_kleeja_github'] ? basename(ADMIN_PATH) . '?cp=p_check_update&amp;smt=download_plugin&amp;plugin_name=' . $plugin["pluginName"] : '' ;
                    }
                    else if (version_compare(strtolower($local_version), strtolower($online_version), '='))
                    {
                        $report	= $olang['U_PLG_OK'];
                        $error = "success";
                        $plugin["local_version"] = $local_version ;
                        $plugin["online_version"] = $online_version ;
                    }
                    else if (version_compare(strtolower($local_version), strtolower($github_version), '>'))
                    {
                        $report	= $olang['U_PLG_NEW'];
                        $error = "warning";
                        $plugin["local_version"] = $local_version ;
                        $plugin["online_version"] = $online_version ;
                        $plugin["update_link"] = $plugin['from_kleeja_github'] ? basename(ADMIN_PATH) . '?cp=p_check_update&amp;smt=download_plugin&amp;plugin_name=' . $plugin["pluginName"] : '' ;
                    }
        
                    
    
                }else {
                    $report	= $olang['PLG_GITHUB_ERR'];
                    $error = "danger";
                }
    
            }else {
                $report = $olang['NO_INIT_FILE'];
                $error = "danger";
            }

            $plugin["report"] = $report;
            $plugin["error"] = $error;

            $return[] = $plugin;
        }
        
        return $return;
    }
}


// new functions

if ( ! function_exists('plugins_store_link')) {

    function plugins_store_link($store_name = '')
    {

        $avilable_store = array(

             0 => array( 'name' => 'kleeja' ,'link' => 'https://api.github.com/repositories/116738112/contents/plugins') ,

           );

        /**
        * your example :
        * $avilable_store[] = array( 'name' => 'your_store_name' ,'link' => 'https://your_store_link.path') ,
        */

        // add plugin hook here .

        if ( $store_name !== '') {

           foreach ($avilable_store as $store_info) {

               if ($store_info['name'] == $store_name) {

                   return $store_info['link'];

               }
           }            
        }

        return $avilable_store;
    }
}





if ( !function_exists('get_online_plugins')) {

    function get_online_plugins($url = '')
    {

        if ($url == '') {

           $url = fetch_remote_file( plugins_store_link('kleeja'));

        }

        // convert json data to an array;
        $plugins = json_decode($url , true);

        $return_plugin = array();

        foreach ($plugins as $plugin_info) {
            
           if ($plugin_info['type'] == 'dir') 
           {

               $return_plugin[] = $plugin_info['name'];
               
           }
        }

        return $return_plugin;
    }
}



if ( ! function_exists('online_plugin_data') ):
   
   function online_plugin_data($folder_name = '', $path = '' , $store_info = array() )
   {
      

       if ( $folder_name == '') {

           return ;

       }

       if ( count($store_info) == 0)
       {

           $store_info = array('name' => 'kleeja' , 'link' =>  plugins_store_link('kleeja') );

       }

       $plugin_foler_content = fetch_remote_file(  $path =='' ? $store_info['link']  . '/' . $folder_name : str_replace('plugins' ,'' , $store_info['link']) . '/' . $path   );

               // convert json data to an array;
               $content = json_decode( $plugin_foler_content , true);

               $return_content = array();
       
               foreach ($content as $content_info) {
                    
                   if ($content_info['type'] == 'dir') 
                   {

                       $return_content[$content_info['name']] = online_plugin_data($content_info['name'] , $content_info['path'] , $store_info );
                       
                   }else 
                   {

                       $return_content[$content_info['name']] = $content_info;
                   }

                   
                }
       
                return $return_content;

   }

endif;   

/**
* it was not able for me to create folder in folder by mkdir() function ,
* so i made this function , and it will make evrything automaticly .
* it was working with all plugins except ( pdf_viewer ) .
* when i was trying to make this dir /plugins/pdf_viewer/v/images/
* the function was making all direction except /images/ folder .
* thatswhy i canceled this function from the plugin and i made ( down_plugin_zip ) class .
*/
if (!function_exists('create_multi_dir')) 
{
   function create_multi_dir( $dir)
   {
       $check = explode('/', $dir);
       $final_path = '';

       foreach ($check as $folder) {
           
           if ($folder !== '' && $folder !== '/') 
           {
               if (!is_dir($folder)) 
               {
                   if ($final_path === '') 
                   {
                       $final_path = $folder . '/';

                       mkdir($final_path , 0777 , true);
                   }else
                   {
                       $final_path = $final_path . '/' . $folder ;

                       if (!is_dir($final_path)) 
                       {
                           mkdir($final_path , 0777 , true);
                       }
                   }
               }else
               {
                   if ($final_path === '') 
                   {
                       $final_path = $folder . '/';
                   }else
                   {
                       $final_path = $final_path . '/' . $folder ;
                   }
               }
           }
       }// return $final_path;
   }
}




if ( !class_exists('down_plugin_zip')) 
{
   class down_plugin_zip
   {
       private $plugin_name;
       private $zip;
       private $error = array();

       public function __construct($plg_name = '', $plg_data = array())
       {
           if ($plg_name == '' || (!is_array($plg_data) && count($plg_data) == 0)) 
           {
               return;
           }
           $this->plugin_name = $plg_name ;

           $this->zip = new ZipArchive() ;

           if ($this->zip->open($this->plugin_name . '.zip', ZipArchive::CREATE)!==TRUE) {

               $this->error[] = "cannot open " . $this->plugin_name . '.zip';

           }

           $this->addFiles($plg_data);
       }

       public function getError()
       {
           return $this->error;
       }

       public function done()
       {
           
           $this->zip->close();
           
           if ($this->zip->open($this->plugin_name . '.zip') !== true) {

               $this->error[] = 'unable to open zip file after creating it';

           }

           $this->zip->extractTo('../');

           $this->zip->close();

       }

       public function clean()
       {
           if (file_exists($this->plugin_name . '.zip')) {

               unlink($this->plugin_name . '.zip');

           }else 
           {

               $this->error[] = 'we did not find the file in cleare function';

           }
       }


       public function addFiles($data = array())
       {
           foreach ($data as $file_info) 
           {

               if ( isset($file_info['type']) && $file_info['type'] == 'file') 
               {

                   $this->zip->addFromString($file_info['path'], fetch_remote_file($file_info['download_url']));

               }else 
               {

                   $this->addFiles($file_info);
               }
           }
       }

   }
   
}



if ( ! function_exists('make_github_cash')) {
   function make_github_cash()
   {
       $plugins = get_online_plugins();

       $return = array();

       foreach ($plugins as $pluginName) {

           $return[$pluginName] = online_plugin_data($pluginName);
       
       }

       return $return;
   }
}



if (! function_exists('delete_plugin_folder')) {

    // https://paulund.co.uk/php-delete-directory-and-files-in-directory

    function delete_plugin_folder($dirname) {

        if (is_dir($dirname))

          $dir_handle = opendir($dirname);

    if (!$dir_handle)

         return false;

    while($file = readdir($dir_handle)) {

          if ($file != "." && $file != "..") {

               if (!is_dir($dirname."/".$file))

                    unlink($dirname."/".$file);

               else

               delete_plugin_folder($dirname.'/'.$file);

          }
    }

    closedir($dir_handle);

    rmdir($dirname);

    return true;

    }

}



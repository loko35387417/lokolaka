<?php
// Avoiding the tkn libraries at all costs ... unless we have to. 
// require_once(dirname(__FILE__).'/tkn/CUsmAutoLoader.php');
session_save_path(dirname(dirname(__FILE__)) .'/sessions');

defined('DB_NAME') or define('DB_NAME', 'rapport_new');
defined('SITE_NAME') or define('SITE_NAME', 'Rapport');
defined('CLASS_PATH') or define('CLASS_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes');

function autoload($class)
{
    $classPath = CLASS_PATH;
    $result = loadClass($classPath, $class);
}

function loadClass($classPath, $class)
{
    if(is_dir($classPath)) {
        if ($dh = opendir($classPath)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($classPath . DIRECTORY_SEPARATOR . $file)) {
                        call_user_func(__FUNCTION__, $classPath . DIRECTORY_SEPARATOR . $file, $class);
                    } else {
                        
                         // origin a.php, A.php
                        $file = $classPath . DIRECTORY_SEPARATOR . $class . '.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        };
                        
                        $file = $classPath . DIRECTORY_SEPARATOR . $class . '.cls.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        };
                        
                        // $a = new A() but the class declear is class a {...}
                        $file = $classPath . DIRECTORY_SEPARATOR .strtoupper(substr($class, 0,1)) . substr($class, 1) . '.cls.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        }
                        
                        $file = $classPath . DIRECTORY_SEPARATOR .strtoupper(substr($class, 0,1)) . substr($class, 1) . '.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        }
                        
                        // $a = new A() but the class declear is class a {...}
                        $file = $classPath . DIRECTORY_SEPARATOR .strtolower(substr($class, 0,1)) . substr($class, 1) . '.cls.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        }
                        
                        $file = $classPath . DIRECTORY_SEPARATOR .strtolower(substr($class, 0,1)) . substr($class, 1) . '.php';
                        if (file_exists($file)) {
                            require_once $file;
                            return;
                        }
                        
                    }
                }
            }
            closedir($dh);
        }
    }
} 
spl_autoload_register('autoload');

//debug function
function w($str = null)
{
    echo'<pre>';
    var_dump($str);
    echo'</pre>';
}

function we($str = null)
{
    echo'<pre>';
    var_dump($str);
    echo'</pre>';
    exit;
}
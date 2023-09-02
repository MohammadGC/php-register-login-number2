<?php

use Auth\Auth;


//session start
session_start();





//config
define('BASE_PATH', __DIR__); //address base project
define('CURRENT_DOMAIN', currentDomain() . '/project'); //address now domain
define('DISPLAY_ERROR', true); //show error message => true / not show error message => false
define('DB_HOST', 'localhost'); //databse settings
define('DB_NAME', 'project'); //databse settings
define('DB_USERNAME', 'root'); //databse settings
define('DB_PASSWORD', ''); //databse settings
//phpMailer
define('MAIL_HOST', 'smtp.gmail.com'); //for host
define('SMTP_AUTH', true); //for authentication
define('MAIL_USERNAME', 'enterEmailAddress');
define('MAIL_PASSWORD', 'enter password email');
define('MAIL_PORT', 587); //for gmail port 587
define('SENDER_MAIL', 'enterEmailAddress'); //for show name sender email
define('SENDER_NAME', 'Mohammad Reza'); //Name Of The Sender

require_once 'database/DataBase.php';
require_once 'database/CreateDB.php';
require_once 'activities/Admin/Admin.php';
require_once 'activities/Admin/Category.php';
// $db = new database\Database();
// $db = new database\CreateDB();
// $db->run();

// auth
require_once 'activities/Auth/Auth.php';

//spl_autoload_register : PHP itself has this.
spl_autoload_register(function ($className) { //DIRECTORY_SEPARATOR : It can detect the operating system and when you want to slash or backslash, it will put it by itself
        //All pakeges are in to folder lib.
        $path = BASE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR; //
        // $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);//for (MAC OS)
        include $path . $className . '.php';
});
// $auth = new Auth();
// $auth->sendMail("emailAddress", "subject", "<p>body email</p>");//test phpMailer






// uri('admin/category', 'Category', 'index');
// uri('admin/category/store', 'Category', 'store', 'POST');
function uri($reservedUrl, $class, $method, $requestMethod = 'GET') //roting system helper
{

        //current url array
        $currentUrl = explode('?', currentUrl())[0];
        $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
        $currentUrl = trim($currentUrl, '/');
        $currentUrlArray = explode('/', $currentUrl);
        $currentUrlArray = array_filter($currentUrlArray); //delete empty index array

        //reserved Url array
        $reservedUrl = trim($reservedUrl, '/');
        $reservedUrlArray = explode('/', $reservedUrl);
        $reservedUrlArray = array_filter($reservedUrlArray);

        if (sizeof($currentUrlArray) != sizeof($reservedUrlArray) || methodField() != $requestMethod) {
                return false;
        }

        $parameters = [];
        for ($key = 0; $key < sizeof($currentUrlArray); $key++) {
                if ($reservedUrlArray[$key][0] == "{" && $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] == "}") {
                        array_push($parameters, $currentUrlArray[$key]);
                } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {
                        return false;
                }
        }

        if (methodField() == 'POST') {
                $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
                $parameters = array_merge([$request], $parameters);
        }

        $object = new $class;
        call_user_func_array(array($object, $method), $parameters); //Don't know what our class 
        exit();
}
// admin/category/edit/{id} reserved url
// admin/category/delete/{id} reserved url
// admin/category/edit/5 current url 
// admin/category/edit/5 current url 
// uri('admin/category', 'Category', 'index');


//helpers

function protocol()
{
        return  stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://'; // get position protocol 
}


function currentDomain() // get current domain
{
        return protocol() . $_SERVER['HTTP_HOST'];
}


function asset($src) //give src from function asset and macke address for We. like css file
{

        $domain = trim(CURRENT_DOMAIN, '/ ');
        $src = $domain . '/' . trim($src, '/');
        return $src;
}

function url($url) // create a tag. address site url
{

        $domain = trim(CURRENT_DOMAIN, '/ ');
        $url = $domain . '/' . trim($url, '/');
        return $url;
}

function currentUrl() //show currentUrl User opened AND For System rooting
{
        return currentDomain() . $_SERVER['REQUEST_URI'];
}

function methodField() //Specified currentMethod? post method or get method
{
        return $_SERVER['REQUEST_METHOD'];
}

function displayError($displayError) //show error message
{

        if ($displayError) { //show error message and or not show error message, change setting => php.ini
                ini_set('display_errors', 1); //Automatically open php.ini and set:display_errors: 1
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL); //E_ALL:shwo all error
        } else {
                ini_set('display_errors', 0);
                ini_set('display_startup_errors', 0);
                error_reporting(0);
        }
}

displayError(DISPLAY_ERROR); //DISPLAY_ERROR is The constant we defined above


global $flashMessage;
if (isset($_SESSION['flash_message'])) { //Display a message when the page is refreshed.session =>Preventing the message from being deleted when the page is refreshed
        $flashMessage = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
}


function flash($name, $value = null) //Messages that occur in different requests.
//this flash Dual Purpose.setter and geter
{
        if ($value === null) {
                global $flashMessage;
                $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
                return $message;
        } else {
                $_SESSION['flash_message'][$name] = $value;
        }
}
// flash('login_error', "Login encountered an error");
// flash('cart_success', "The product has been successfully added to your shopping cart");
// echo flash('login_error');
// echo flash('cart_success');


function dd($var)
{
        echo '<pre>';
        var_dump($var);
        exit;
}





// category system rooting

uri('admin/category', 'Admin\Category', 'index');
uri('admin/category/create', 'Admin\Category', 'create');
uri('admin/category/store', 'Admin\Category', 'store', 'POST');
uri('admin/category/edit/{id}', 'Admin\Category', 'edit');
uri('admin/category/update/{id}', 'Admin\Category', 'update', 'POST');
uri('admin/category/delete/{id}', 'Admin\Category', 'delete');


//Auth 
uri('register', 'Auth\Auth', 'register');
uri('register/store', 'Auth\Auth', 'registerStore', 'POST');
uri('activation/{verify_token}', 'Auth\Auth', 'activation');
uri('login', 'Auth\Auth', 'login');
uri('check-login', 'Auth\Auth', 'checkLogin', 'POST');
uri('logout', 'Auth\Auth', 'logout');
uri('forgot', 'Auth\Auth', 'forgot');
uri('forgot/request', 'Auth\Auth', 'forgotRequest', "POST");
uri('reset-password-form/{forgot_token}', 'Auth\Auth', 'resetPasswordView');
uri('reset-password/{forgot_token}', 'Auth\Auth', 'resetPassword', 'POST');


echo '404 - page not found';

<?php

namespace Auth;

use database\Database;
use LDAP\Result;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Auth
{
    protected function redirect($url)
    {

        header("Location: " . trim(CURRENT_DOMAIN, "/ ") . "/" . trim($url, "/ "));
        exit;
    }
    protected function redirectBack()
    {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    private function hash($password)
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        return $hashPassword;
    }

    private function random()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }


    public function activationMessage($username, $verifyToken)
    { //for body email
        $message = "
        <h1>activation account</h1>
            <p>" . $username . " maby for activation accuot click here</p>
        <div><a href=" . url('activation/' . $verifyToken) . ">activation</a></div>";
        return $message;
    }




    private function sendMail($emailAddress, $subject, $body)
    { //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
            $mail->CharSet = "UTF-8"; //I added myself
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = MAIL_HOST; //Set the SMTP server to send through
            $mail->SMTPAuth = SMTP_AUTH; //Enable SMTP authentication
            $mail->Username = MAIL_USERNAME; //SMTP username
            $mail->Password = MAIL_PASSWORD; //SMTP password
            $mail->SMTPSecure = "tls"; //for gamil
            $mail->Port = MAIL_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(SENDER_MAIL, SENDER_NAME);
            $mail->addAddress($emailAddress);



            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $result = $mail->send();
            echo 'Message has been sent';
            return $result;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    public function register()
    {
        require_once(BASE_PATH . "/template/auth/register.php");
    }
    public function registerStore($request)
    {


        if (empty($request["email"]) || empty($request["username"]) || empty($request["password"])) {
            flash("register_error", "requierd all filed");
            $this->redirectBack();
        } else if (strlen($request["password"]) < 8) {
            flash("register_error", "Password shuld not be less than 8 characters");
            $this->redirectBack();
            //FILTER_VALIDATE_EMAIL is khode php
        } else if (!filter_var($request["email"], FILTER_VALIDATE_EMAIL)) {
            flash("register_error", "please enter a valid email address");
            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select("SELECT * FROM `users` WHERE `email` = ?", [$request["email"]])->fetch();

            if ($user != null) {
                flash("register_error", "The user already exist in the system.");
                $this->redirectBack();
            } else {

                $randomToken = $this->random(); //for verifytoken
                $activationMessage = $this->activationMessage($request["username"], $randomToken);
                $result = $this->sendMail($request["email"], "activation account", $activationMessage);

                if ($result) {

                    $request["verify_token"] = $randomToken;
                    $request["password"] = $this->hash($request["password"]);
                    $db->insert("users", array_keys($request), $request);
                    $this->redirect("login");
                } else {
                    flash("register_error", "There was an error sendigning the email. Please try again");
                    $this->redirectBack();
                }
            }
        }
    }


    public function activation($verifyToken)
    {
        $db = new Database();
        $user = $db->select("SELECT * FROM `users` WHERE `verify_token` = ? AND  `is_active` = 0 ;", [$verifyToken])->fetch();

        if ($user == null) {
            $this->redirect("login");
        } else {
            $result = $db->update("users", $user["id"], ["is_active"], [1]);
            $this->redirect("login");
        }
    }


    public function login()
    {
        require_once(BASE_PATH . "/template/auth/login.php");
    }
    public function checkLogin($request)
    {

        if (empty($request["email"]) ||  empty($request["password"])) {
            flash("login_error", "requierd all filed");
            $this->redirectBack();
        } else {


            $db = new DataBase();
            $user = $db->select("SELECT * FROM `users` WHERE `email` = ? ;", [$request["email"]])->fetch();



            if ($user != null) {
                if (password_verify($request["password"], $user["password"]) && $user["is_active"] == 1) {
                    $_SESSION["user"] = $user["id"];
                    $this->redirect("admin");
                } else {
                    flash("login_error", "Password or email failde");
                    $this->redirectBack();
                }
            } else {
                flash("login_error", "Ther is no such user");
                $this->redirectBack();
            }
        }
    }

    public function checkAdmin()
    {
        if (isset($_SESSION["user"])) {
            $db = new Database();
            $user = $db->select("SELECT * FROM `users` WHERE `id` = ?;", [$_SESSION["user"]])->fetch();

            if ($user != null) {
                if ($user["permission"] != "admin") {
                    $this->redirect("home");
                }
            } else {
                $this->redirect("home");
            }
        } else {
            $this->redirect("home");
        }
    }



    public function logout()
    {

        if (isset($_SESSION["user"])) {
            unset($_SESSION["user"]);
            session_destroy();
        }

        $this->redirect("home");
    }

    public function forgot()
    {
        require_once(BASE_PATH . "/template/auth/forgot.php");
    }

    public function forgotMessage($username, $verifyToken)
    { //for body email
        $message = "
        <h1>Forgot Password Account</h1>
            <p>" . $username . " maby for reset password accout click here</p>
        <div><a href=" . url('reset-password-form/' . $verifyToken) . ">Reset Password</a></div>";
        return $message;
    }

    public function forgotRequest($request)
    {
        if (empty($request["email"])) {
            flash("forgot_error", "requierd email");
            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select("SELECT * FROM `users` WHERE `email` = ? ;", [$request["email"]])->fetch();



            if ($user == null) {
                flash("forgot_error", "Not Found user");
                $this->redirectBack();
            } else {

                $randomToken = $this->random(); //for verifytoken
                $forgotMessage = $this->forgotMessage($user["username"], $randomToken);
                $result = $this->sendMail($request["email"], "reset password account", $forgotMessage);

                if ($result) {
                    date_default_timezone_set("Asia/Tehran");
                    $db->update("users", $user["id"], ["forgot_token", "forgot_token_expire"], [$randomToken, date("Y-m-d H:i:s", strtotime("+ 15 minutes"))]);
                    $this->redirect("login");
                } else {
                    flash("forgot_error", "Sorry,Sending email failed");
                    $this->redirectBack();
                }
            }
        }
    }

    public function resetPasswordView($forgot_token)
    {
        require_once(BASE_PATH . "/template/auth/reset-password.php");
    }
    public function resetPassword($request, $forgot_token)
    {
        if (!isset($request["password"]) || strlen($request["password"]) < 8) {
            flash("reset_error", "Sorry,shuld not be less than 8 characters");
            $this->redirectBack();
        } else {
            $db = new DataBase();
            $user = $db->select("SELECT * FROM `users` WHERE `forgot_token` = ?", [$forgot_token])->fetch();

            if ($user == null) {
                flash("reset_error", "Sorry,user not found!");
                $this->redirectBack();
            } else {
                date_default_timezone_set("Asia/Tehran");
                if ($user["forgot_token_expire"] < date("Y-m-d H:i:s")) {
                    flash("reset_error", "Sorry,Password recovery time has expired");
                    $this->redirectBack();
                }
                if ($user) {
                    $user = $db->update("users", $user["id"], ["password"], [$this->hash($request["password"])]);

                    $this->redirect("login");
                } else {
                    flash("reset_error", "Sorry,user not found!");
                    $this->redirectBack();
                }
            }
        }
    }
}

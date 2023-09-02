<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login form</title>
    <?php

    require_once(BASE_PATH . "/template/auth/layouts/head-tag.php");
    ?>
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="POST" action="<?= url("check-login") ?>">
        <h3>login Here</h3>
        <?php

        $message = flash("login_error");
        if (!empty($message)) {
        ?>

            <div class="mb-2 alert alert-danger"> <small class="form-text text-danger"><?= $message ?></small> </div>

        <?php } ?>





        <label for="text">Email</label>
        <input type="text" placeholder="email" name="email" />

        <label for="password">Password</label>
        <input type="password" placeholder="Password" name="password" />


        <div class="forgotPassword p-t-12">
            <span class="txt1">
                Forgot
            </span>
            <a class="txt2" href="#">
                Username / Password?
            </a>
        </div>

        <button>Login</button>
        <div class="pt-styel text-center p-t-136">
            <a class="txt2" href="<?= url('register') ?>">
                Create your Account
                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
            </a>
        </div>
        <div class="social">
            <div class="go"><i class="fab fa-google"></i> Google</div>
            <div class="fb"><i class="fab fa-facebook"></i> Facebook</div>
        </div>
    </form>
</body>


</html>
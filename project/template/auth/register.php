<!DOCTYPE html>
<html lang="en">

<head>
    <title>register form</title>
    <?php
    require_once(BASE_PATH . "/template/auth/layouts/head-tag.php");
    ?>
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="POST" action="<?= url("register/store") ?>">
        <h3>register Here</h3>
        <?php

        $message = flash("register_error");
        if (!empty($message)) {
        ?>

            <div class="mb-2 alert alert-danger"> <small class="form-text text-danger"><?= $message ?></small> </div>

        <?php } ?>



        <label for="username">Username</label>
        <input type="text" placeholder="username" name="username" />

        <label for="text">Email</label>
        <input type="text" placeholder="email" name="email" />

        <label for="password">Password</label>
        <input type="password" placeholder="Password" name="password" />




        <button>Register</button>
        <div class="pt-styel text-center p-t-136">
            <a class="txt2" href="<?= url('login') ?>">
                login your Account
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
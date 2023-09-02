<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot form</title>
    <?php

    require_once(BASE_PATH . "/template/auth/layouts/head-tag.php");
    ?>
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="POST" action="<?= url("forgot/request") ?>">
        <h3>Forgot password Here</h3>
        <?php

        $message = flash("forgot");
        if (!empty($message)) {
        ?>

            <div class="mb-2 alert alert-danger"> <small class="form-text text-danger"><?= $message ?></small> </div>

        <?php } ?>


        <label for="text">Email</label>
        <input type="text" placeholder="email" name="email" />


        <button>Send</button>
        <div class="pt-styel text-center p-t-136">
            <a class="txt2" href="<?= url('register') ?>">
                Create your Account
                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
            </a>
        </div>

    </form>
</body>


</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password form</title>
    <?php

    require_once(BASE_PATH . "/template/auth/layouts/head-tag.php");
    ?>
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="POST" action="<?= url("reset-password/" . $forgot_token) ?>">
        <h3>Forgot password Here</h3>
        <?php

        $message = flash("reset_error");
        if (!empty($message)) {
        ?>

            <div class="mb-2 alert alert-danger"> <small class="form-text text-danger"><?= $message ?></small> </div>

        <?php } ?>


        <label for="text">Password</label>
        <input type="Password" placeholder="password" name="password" />


        <button>Send</button>


    </form>
</body>


</html>
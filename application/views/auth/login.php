<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Login'; ?></title>
</head>
<body>
    <h1>Login</h1>

    <?php if (validation_errors()): ?>
        <div style="color: red;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= html_escape($error_message); ?>
        </div>
    <?php endif; ?>

    <?= form_open('auth/login'); ?>

        <p>
            <label for="email">University Email</label><br>
            <input type="email" name="email" id="email" value="<?= set_value('email'); ?>">
        </p>

        <p>
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password">
        </p>

        <p>
            <button type="submit">Login</button>
        </p>

    <?= form_close(); ?>
    <p><a href="<?= site_url('auth/forgot_password'); ?>">Forgot password?</a></p>
</body>
</html>
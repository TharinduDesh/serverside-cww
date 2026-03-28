<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Forgot Password'; ?></title>
</head>
<body>
    <h1>Forgot Password</h1>

    <?php if (validation_errors()): ?>
        <div style="color: red;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div style="color: green; margin-bottom: 15px;">
            <?= html_escape($success_message); ?>
        </div>
    <?php endif; ?>

    <?= form_open('auth/forgot_password'); ?>
        <p>
            <label for="email">University Email</label><br>
            <input type="email" name="email" id="email" value="<?= set_value('email'); ?>">
        </p>

        <p>
            <button type="submit">Send Reset Link</button>
        </p>
    <?= form_close(); ?>

    <p><a href="<?= site_url('auth/login'); ?>">Back to login</a></p>
</body>
</html>
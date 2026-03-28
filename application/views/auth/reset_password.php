<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Reset Password'; ?></title>
</head>
<body>
    <h1>Reset Password</h1>

    <?php if (validation_errors()): ?>
        <div style="color: red;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open(current_url() . '?token=' . urlencode($token)); ?>
        <p>
            <label for="password">New Password</label><br>
            <input type="password" name="password" id="password">
        </p>

        <p>
            <label for="confirm_password">Confirm New Password</label><br>
            <input type="password" name="confirm_password" id="confirm_password">
        </p>

        <p>
            <button type="submit">Reset Password</button>
        </p>
    <?= form_close(); ?>
</body>
</html>
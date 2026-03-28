<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Register'; ?></title>
</head>
<body>
    <h1>Alumni Registration</h1>

    <?php if (validation_errors()): ?>
        <div style="color: red;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div style="color: green; margin-bottom: 15px;">
            <?= $success_message; ?>
        </div>
    <?php endif; ?>

    <?= form_open('auth/register'); ?>

        <p>
            <label for="first_name">First Name</label><br>
            <input type="text" name="first_name" id="first_name" value="<?= set_value('first_name'); ?>">
        </p>

        <p>
            <label for="last_name">Last Name</label><br>
            <input type="text" name="last_name" id="last_name" value="<?= set_value('last_name'); ?>">
        </p>

        <p>
            <label for="email">University Email</label><br>
            <input type="email" name="email" id="email" value="<?= set_value('email'); ?>">
        </p>

        <p>
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password">
        </p>

        <p>
            <label for="confirm_password">Confirm Password</label><br>
            <input type="password" name="confirm_password" id="confirm_password">
        </p>

        <p>
            <button type="submit">Register</button>
        </p>

    <?= form_close(); ?>
</body>
</html>
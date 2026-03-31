<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Edit Licence'; ?></title>
</head>
<body>
    <h1>Edit Licence</h1>

    <p><a href="<?= site_url('profile'); ?>">Back to Profile</a></p>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open('profile/edit_licence/' . $licence->id); ?>

        <p>
            <label for="licence_name">Licence Name</label><br>
            <input type="text" name="licence_name" id="licence_name" value="<?= set_value('licence_name', $licence->licence_name); ?>">
        </p>

        <p>
            <label for="issuing_body">Issuing Body</label><br>
            <input type="text" name="issuing_body" id="issuing_body" value="<?= set_value('issuing_body', $licence->issuing_body); ?>">
        </p>

        <p>
            <label for="licence_url">Licence Awarding Body URL</label><br>
            <input type="url" name="licence_url" id="licence_url" value="<?= set_value('licence_url', $licence->licence_url); ?>">
        </p>

        <p>
            <label for="completion_date">Completion Date</label><br>
            <input type="date" name="completion_date" id="completion_date" value="<?= set_value('completion_date', $licence->completion_date); ?>">
        </p>

        <p>
            <button type="submit">Update Licence</button>
        </p>

    <?= form_close(); ?>
</body>
</html>
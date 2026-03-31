<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Edit Certification'; ?></title>
</head>
<body>
    <h1>Edit Certification</h1>

    <p><a href="<?= site_url('profile'); ?>">Back to Profile</a></p>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open('profile/edit_certification/' . $certification->id); ?>

        <p>
            <label for="certification_name">Certification Name</label><br>
            <input type="text" name="certification_name" id="certification_name" value="<?= set_value('certification_name', $certification->certification_name); ?>">
        </p>

        <p>
            <label for="issuing_organization">Issuing Organization</label><br>
            <input type="text" name="issuing_organization" id="issuing_organization" value="<?= set_value('issuing_organization', $certification->issuing_organization); ?>">
        </p>

        <p>
            <label for="certification_url">Course / Certification Page URL</label><br>
            <input type="url" name="certification_url" id="certification_url" value="<?= set_value('certification_url', $certification->certification_url); ?>">
        </p>

        <p>
            <label for="completion_date">Completion Date</label><br>
            <input type="date" name="completion_date" id="completion_date" value="<?= set_value('completion_date', $certification->completion_date); ?>">
        </p>

        <p>
            <button type="submit">Update Certification</button>
        </p>

    <?= form_close(); ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Edit Degree'; ?></title>
</head>

<body>
    <h1>Edit Degree</h1>

    <p><a href="<?= site_url('profile'); ?>">Back to Profile</a></p>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open('profile/edit_degree/' . $degree->id); ?>

    <p>
        <label for="degree_name">Degree Name</label><br>
        <input type="text" name="degree_name" id="degree_name"
            value="<?= set_value('degree_name', $degree->degree_name); ?>">
    </p>

    <p>
        <label for="programme">Programme / Study Area</label><br>
        <input type="text" name="programme" id="programme"
            value="<?= set_value('programme', isset($degree->programme) ? $degree->programme : ''); ?>"
            placeholder="Example: Computer Science">
    </p>

    <p>
        <label for="institution_name">Institution Name</label><br>
        <input type="text" name="institution_name" id="institution_name"
            value="<?= set_value('institution_name', $degree->institution_name); ?>">
    </p>

    <p>
        <label for="degree_url">Official Degree Page URL</label><br>
        <input type="url" name="degree_url" id="degree_url"
            value="<?= set_value('degree_url', $degree->degree_url); ?>">
    </p>

    <p>
        <label for="completion_date">Completion Date</label><br>
        <input type="date" name="completion_date" id="completion_date"
            value="<?= set_value('completion_date', $degree->completion_date); ?>">
    </p>

    <p>
        <button type="submit">Update Degree</button>
    </p>

    <?= form_close(); ?>
</body>

</html>
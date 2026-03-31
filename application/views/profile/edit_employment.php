<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Edit Employment'; ?></title>
</head>
<body>
    <h1>Edit Employment</h1>

    <p><a href="<?= site_url('profile'); ?>">Back to Profile</a></p>

    <?php if (!empty($employment_error)): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= $employment_error; ?>
        </div>
    <?php endif; ?>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open('profile/edit_employment/' . $employment->id); ?>

        <p>
            <label for="company_name">Company Name</label><br>
            <input type="text" name="company_name" id="company_name" value="<?= set_value('company_name', $employment->company_name); ?>">
        </p>

        <p>
            <label for="job_title">Job Title</label><br>
            <input type="text" name="job_title" id="job_title" value="<?= set_value('job_title', $employment->job_title); ?>">
        </p>

        <p>
            <label for="start_date">Start Date</label><br>
            <input type="date" name="start_date" id="start_date" value="<?= set_value('start_date', $employment->start_date); ?>">
        </p>

        <p>
            <label for="end_date">End Date</label><br>
            <input type="date" name="end_date" id="end_date" value="<?= set_value('end_date', $employment->end_date); ?>">
        </p>

        <p>
            <label>
                <input type="checkbox" name="is_current" value="1" <?= set_checkbox('is_current', '1', (int)$employment->is_current === 1); ?>>
                This is my current job
            </label>
        </p>

        <p>
            <label for="description">Description</label><br>
            <textarea name="description" id="description" rows="4" cols="60"><?= set_value('description', $employment->description); ?></textarea>
        </p>

        <p>
            <button type="submit">Update Employment</button>
        </p>

    <?= form_close(); ?>
</body>
</html>
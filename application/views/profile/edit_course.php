<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Edit Professional Course'; ?></title>
</head>
<body>
    <h1>Edit Professional Course</h1>

    <p><a href="<?= site_url('profile'); ?>">Back to Profile</a></p>

    <?php if (validation_errors()): ?>
        <div style="color: red; margin-bottom: 15px;">
            <?= validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?= form_open('profile/edit_course/' . $course->id); ?>

        <p>
            <label for="course_name">Course Name</label><br>
            <input type="text" name="course_name" id="course_name" value="<?= set_value('course_name', $course->course_name); ?>">
        </p>

        <p>
            <label for="provider_name">Provider Name</label><br>
            <input type="text" name="provider_name" id="provider_name" value="<?= set_value('provider_name', $course->provider_name); ?>">
        </p>

        <p>
            <label for="course_url">Course Page URL</label><br>
            <input type="url" name="course_url" id="course_url" value="<?= set_value('course_url', $course->course_url); ?>">
        </p>

        <p>
            <label for="completion_date">Completion Date</label><br>
            <input type="date" name="completion_date" id="completion_date" value="<?= set_value('completion_date', $course->completion_date); ?>">
        </p>

        <p>
            <button type="submit">Update Course</button>
        </p>

    <?= form_close(); ?>
</body>
</html>
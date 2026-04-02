<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Featured Alumnus Today'; ?></title>
</head>
<body>
    <h1>Featured Alumnus Today</h1>

    <p><strong>Date:</strong> <?= html_escape($feature_date); ?></p>

    <?php if ($featured): ?>
        <p>
            <strong>Name:</strong>
            <?= html_escape($featured->first_name . ' ' . $featured->last_name); ?>
        </p>
        <p><strong>Feature Date:</strong> <?= html_escape($featured->feature_date); ?></p>
    <?php else: ?>
        <p>No featured alumnus has been selected for today yet.</p>
    <?php endif; ?>
</body>
</html>
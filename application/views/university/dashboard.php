<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?= html_escape($title); ?>
    </title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #111827;
            color: #ffffff;
            padding: 24px 18px;
        }

        .sidebar h2 {
            font-size: 20px;
            margin: 0 0 24px;
        }

        .sidebar a {
            display: block;
            color: #d1d5db;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .main {
            flex: 1;
            padding: 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 26px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }

        .topbar .user-note {
            color: #6b7280;
            font-size: 14px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .card {
            background: #ffffff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #6b7280;
        }

        .card .number {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .section-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 20px;
        }

        .panel {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .panel h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .insight {
            border-left: 5px solid #2563eb;
            padding: 12px 14px;
            background: #eff6ff;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            background: #e5e7eb;
        }

        .badge-critical {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-significant {
            background: #ffedd5;
            color: #9a3412;
        }

        .badge-emerging {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
        }

        .quick-links a {
            display: inline-block;
            margin: 8px 8px 0 0;
            padding: 10px 14px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 8px;
            text-decoration: none;
        }

        @media (max-width: 1000px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: auto;
            }

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .section-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="layout">
        <aside class="sidebar">
            <h2>University Analytics</h2>
            <a class="active" href="<?= site_url('university/dashboard'); ?>">Dashboard</a>
            <a href="<?= site_url('university/graphs'); ?>">Graphs & Trends</a>
            <a href="<?= site_url('university/alumni'); ?>">View Alumni</a>
            <a href="<?= site_url('university/reports'); ?>">Reports</a>
            <a href="<?= site_url('developer'); ?>">API Keys</a>
            <a href="<?= site_url('profile'); ?>">Back to Profile</a>
            <a href="<?= site_url('logout'); ?>">Logout</a>
        </aside>

        <main class="main">
            <div class="topbar">
                <div>
                    <h1>
                        <?= html_escape($title); ?>
                    </h1>
                    <p class="user-note">Real-time alumni intelligence for curriculum and strategic planning.</p>
                </div>
            </div>

            <div class="cards">
                <div class="card">
                    <h3>Total Active Alumni</h3>
                    <div class="number">
                        <?= (int) $summary['total_alumni']; ?>
                    </div>
                </div>

                <div class="card">
                    <h3>Programmes</h3>
                    <div class="number">
                        <?= (int) $summary['total_programmes']; ?>
                    </div>
                </div>

                <div class="card">
                    <h3>Industry Sectors</h3>
                    <div class="number">
                        <?= (int) $summary['total_industry_sectors']; ?>
                    </div>
                </div>

                <div class="card">
                    <h3>Certifications</h3>
                    <div class="number">
                        <?= (int) $summary['total_certifications']; ?>
                    </div>
                </div>

                <div class="card">
                    <h3>Professional Courses</h3>
                    <div class="number">
                        <?= (int) $summary['total_professional_courses']; ?>
                    </div>
                </div>
            </div>

            <div class="section-grid">
                <section class="panel">
                    <h2>Curriculum Skills Gap Signals</h2>

                    <?php if (!empty($skills_gap)): ?>
                        <?php foreach ($skills_gap as $gap): ?>
                            <?php
                            $level = strtolower($gap['level']);
                            $badgeClass = 'badge-low';

                            if (strpos($level, 'critical') !== false) {
                                $badgeClass = 'badge-critical';
                            } elseif (strpos($level, 'significant') !== false) {
                                $badgeClass = 'badge-significant';
                            } elseif (strpos($level, 'emerging') !== false) {
                                $badgeClass = 'badge-emerging';
                            }
                            ?>
                            <div class="insight">
                                <strong>
                                    <?= html_escape($gap['label']); ?>
                                </strong><br>
                                <?= (int) $gap['total']; ?> alumni records mention this skill/course.
                                <br>
                                <span class="badge <?= $badgeClass; ?>">
                                    <?= html_escape($gap['level']); ?> -
                                    <?= html_escape($gap['percentage']); ?>%
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No certification or professional course data available yet.</p>
                    <?php endif; ?>
                </section>

                <section class="panel">
                    <h2>Quick Actions</h2>
                    <p>
                        Use the analytics dashboard to identify programme trends, graduate employment
                        sectors, skills gaps, popular job titles, top employers, and alumni locations.
                    </p>

                    <div class="quick-links">
                        <a href="<?= site_url('university/graphs'); ?>">View Graphs</a>
                        <a href="<?= site_url('university/alumni'); ?>">Filter Alumni</a>
                        <a href="<?= site_url('university/reports'); ?>">Generate Report</a>
                        <a href="<?= site_url('developer'); ?>">Manage API Keys</a>
                    </div>
                </section>
            </div>
        </main>
    </div>

</body>

</html>
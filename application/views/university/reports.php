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
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }

        .topbar p {
            color: #6b7280;
            margin-top: 8px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-success {
            background: #16a34a;
            color: #ffffff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
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

        .panel {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
        }

        .panel h2 {
            margin-top: 0;
            font-size: 20px;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .meta-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
        }

        .meta-box strong {
            display: block;
            margin-bottom: 6px;
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
            margin-top: 6px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        th,
        td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        .note {
            background: #fef9c3;
            border-left: 5px solid #ca8a04;
            border-radius: 8px;
            padding: 14px;
            color: #713f12;
        }

        @media (max-width: 1000px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: auto;
            }

            .cards,
            .report-meta {
                grid-template-columns: repeat(2, 1fr);
            }

            .topbar {
                display: block;
            }

            .actions {
                margin-top: 14px;
            }
        }

        @media print {
            body {
                background: #ffffff;
            }

            .sidebar,
            .actions,
            .no-print {
                display: none !important;
            }

            .layout {
                display: block;
            }

            .main {
                padding: 0;
            }

            .card,
            .panel {
                box-shadow: none;
                border: 1px solid #dddddd;
                break-inside: avoid;
            }

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

    <div class="layout">
        <aside class="sidebar">
            <h2>University Analytics</h2>
            <a href="<?= site_url('university/dashboard'); ?>">Dashboard</a>
            <a href="<?= site_url('university/graphs'); ?>">Graphs & Trends</a>
            <a href="<?= site_url('university/alumni'); ?>">View Alumni</a>
            <a class="active" href="<?= site_url('university/reports'); ?>">Reports</a>
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
                    <p>
                        Generate a simple university intelligence report using current alumni
                        profile, education, employment, certification, and course data.
                    </p>
                </div>

                <div class="actions no-print">
                    <a class="btn btn-success" href="<?= site_url('export/analytics-summary-csv'); ?>">
                        Export Summary CSV
                    </a>
                    <button class="btn btn-primary" onclick="window.print();">
                        Print / Save as PDF
                    </button>
                    <a class="btn btn-secondary" href="<?= site_url('university/graphs'); ?>">
                        Back to Graphs
                    </a>
                </div>
            </div>

            <section class="panel">
                <h2>Report Information</h2>

                <div class="report-meta">
                    <div class="meta-box">
                        <strong>Report Type</strong>
                        University Analytics & Intelligence Summary
                    </div>

                    <div class="meta-box">
                        <strong>Generated Date</strong>
                        <?= date('Y-m-d H:i:s'); ?>
                    </div>

                    <div class="meta-box">
                        <strong>Data Source</strong>
                        Alumni profiles, degrees, employment history, certifications, and professional courses
                    </div>
                </div>

                <div class="note">
                    This report supports curriculum planning by identifying alumni outcome trends,
                    employment sectors, post-graduation development activity, and possible skills gaps.
                </div>
            </section>

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

            <section class="panel">
                <h2>Skills Gap Intelligence</h2>

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
                            <?= (int) $gap['total']; ?> alumni records mention this certification or professional course.
                            This represents
                            <?= html_escape($gap['percentage']); ?>% of active alumni records.
                            <br>
                            <span class="badge <?= $badgeClass; ?>">
                                <?= html_escape($gap['level']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No certification or professional course data is available yet.</p>
                <?php endif; ?>
            </section>

            <section class="panel">
                <h2>Summary Table</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Current Value</th>
                            <th>Interpretation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Active Alumni</td>
                            <td>
                                <?= (int) $summary['total_alumni']; ?>
                            </td>
                            <td>Represents verified and active alumni records available for analysis.</td>
                        </tr>
                        <tr>
                            <td>Total Programmes</td>
                            <td>
                                <?= (int) $summary['total_programmes']; ?>
                            </td>
                            <td>Shows how many different programme areas are represented in the alumni dataset.</td>
                        </tr>
                        <tr>
                            <td>Total Industry Sectors</td>
                            <td>
                                <?= (int) $summary['total_industry_sectors']; ?>
                            </td>
                            <td>Shows the range of employment sectors entered by alumni.</td>
                        </tr>
                        <tr>
                            <td>Total Certifications</td>
                            <td>
                                <?= (int) $summary['total_certifications']; ?>
                            </td>
                            <td>Indicates post-graduation certification activity.</td>
                        </tr>
                        <tr>
                            <td>Total Professional Courses</td>
                            <td>
                                <?= (int) $summary['total_professional_courses']; ?>
                            </td>
                            <td>Indicates professional development activity after graduation.</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>

</html>
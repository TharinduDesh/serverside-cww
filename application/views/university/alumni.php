<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= html_escape($title); ?></title>

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

        .topbar h1 {
            margin: 0;
            font-size: 28px;
        }

        .topbar p {
            color: #6b7280;
            margin-top: 8px;
            margin-bottom: 24px;
        }

        .filter-panel {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            margin-bottom: 24px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 16px;
            align-items: end;
        }

        label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .actions {
            margin-top: 16px;
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

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        .btn-success {
            background: #16a34a;
            color: #ffffff;
        }

        .table-panel {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .table-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .result-count {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 6px 12px;
            font-weight: bold;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th,
        td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: #374151;
        }

        tr:hover {
            background: #f9fafb;
        }

        .empty {
            text-align: center;
            padding: 28px;
            color: #6b7280;
        }

        .small-muted {
            color: #6b7280;
            font-size: 13px;
        }

        @media (max-width: 1100px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: auto;
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .main {
                padding: 18px;
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
        <a class="active" href="<?= site_url('university/alumni'); ?>">View Alumni</a>
        <a href="<?= site_url('university/reports'); ?>">Reports</a>
        <a href="<?= site_url('developer'); ?>">API Keys</a>
        <a href="<?= site_url('profile'); ?>">Back to Profile</a>
        <a href="<?= site_url('logout'); ?>">Logout</a>
    </aside>

    <main class="main">
        <div class="topbar">
            <h1><?= html_escape($title); ?></h1>
            <p>
                Filter alumni records by programme, graduation year, industry sector,
                or keyword search. This supports the university analytics requirement
                for exploring graduate outcomes.
            </p>
        </div>

        <section class="filter-panel">
            <form method="get" action="<?= site_url('university/alumni'); ?>">
                <div class="filter-grid">
                    <div>
                        <label for="search">Search</label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="<?= html_escape($filters['search'] ?? ''); ?>"
                            placeholder="Name, email, company, job title"
                        >
                    </div>

                    <div>
                        <label for="programme">Programme</label>
                        <select name="programme" id="programme">
                            <option value="">All Programmes</option>
                            <?php if (!empty($filter_options['programmes'])): ?>
                                <?php foreach ($filter_options['programmes'] as $programme): ?>
                                    <?php $programmeValue = $programme['programme']; ?>
                                    <option
                                        value="<?= html_escape($programmeValue); ?>"
                                        <?= (($filters['programme'] ?? '') === $programmeValue) ? 'selected' : ''; ?>
                                    >
                                        <?= html_escape($programmeValue); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="graduation_year">Graduation Year</label>
                        <select name="graduation_year" id="graduation_year">
                            <option value="">All Years</option>
                            <?php if (!empty($filter_options['graduation_years'])): ?>
                                <?php foreach ($filter_options['graduation_years'] as $year): ?>
                                    <?php $yearValue = $year['graduation_year']; ?>
                                    <option
                                        value="<?= html_escape($yearValue); ?>"
                                        <?= (($filters['graduation_year'] ?? '') == $yearValue) ? 'selected' : ''; ?>
                                    >
                                        <?= html_escape($yearValue); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="industry_sector">Industry Sector</label>
                        <select name="industry_sector" id="industry_sector">
                            <option value="">All Sectors</option>
                            <?php if (!empty($filter_options['industry_sectors'])): ?>
                                <?php foreach ($filter_options['industry_sectors'] as $sector): ?>
                                    <?php $sectorValue = $sector['industry_sector']; ?>
                                    <option
                                        value="<?= html_escape($sectorValue); ?>"
                                        <?= (($filters['industry_sector'] ?? '') === $sectorValue) ? 'selected' : ''; ?>
                                    >
                                        <?= html_escape($sectorValue); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?= site_url('university/alumni'); ?>" class="btn btn-secondary">Clear Filters</a>

                    <a
                        href="<?= site_url('export/alumni-csv') . '?' . http_build_query($filters); ?>"
                        class="btn btn-success"
                    >
                        Export Filtered CSV
                    </a>
                </div>
            </form>
        </section>

        <section class="table-panel">
            <div class="table-header">
                <h2>Alumni Results</h2>
                <span class="result-count"><?= count($alumni); ?> record(s)</span>
            </div>

            <?php if (!empty($alumni)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Alumni Name</th>
                            <th>Email</th>
                            <th>Headline</th>
                            <th>Degree</th>
                            <th>Programme</th>
                            <th>Graduation Date</th>
                            <th>Company</th>
                            <th>Job Title</th>
                            <th>Industry Sector</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumni as $person): ?>
                            <tr>
                                <td>
                                    <strong><?= html_escape($person['full_name'] ?? '-'); ?></strong>
                                </td>
                                <td><?= html_escape($person['university_email'] ?? '-'); ?></td>
                                <td><?= !empty($person['headline']) ? html_escape($person['headline']) : '<span class="small-muted">Not provided</span>'; ?></td>
                                <td><?= !empty($person['degree_name']) ? html_escape($person['degree_name']) : '-'; ?></td>
                                <td><?= !empty($person['programme']) ? html_escape($person['programme']) : '-'; ?></td>
                                <td><?= !empty($person['completion_date']) ? html_escape($person['completion_date']) : '-'; ?></td>
                                <td><?= !empty($person['company_name']) ? html_escape($person['company_name']) : '-'; ?></td>
                                <td><?= !empty($person['job_title']) ? html_escape($person['job_title']) : '-'; ?></td>
                                <td><?= !empty($person['industry_sector']) ? html_escape($person['industry_sector']) : '-'; ?></td>
                                <td><?= !empty($person['location']) ? html_escape($person['location']) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">
                    No alumni records matched the selected filters.
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
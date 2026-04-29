<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        <?= html_escape($title); ?>
    </title>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .topbar p {
            color: #6b7280;
            margin-top: 8px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(320px, 1fr));
            gap: 22px;
        }

        .chart-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            min-height: 380px;
        }

        .chart-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .chart-card p {
            margin-top: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        .loading {
            padding: 12px;
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-box {
            display: none;
            padding: 12px;
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #991b1b;
        }

        .download-btn {
            margin-top: 12px;
            padding: 8px 12px;
            border: none;
            background: #2563eb;
            color: #ffffff;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
        }

        .download-btn:hover {
            background: #1d4ed8;
        }

        @media (max-width: 1000px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: auto;
            }

            .chart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="layout">
        <aside class="sidebar">
            <h2>University Analytics</h2>
            <a href="<?= site_url('university/dashboard'); ?>">Dashboard</a>
            <a class="active" href="<?= site_url('university/graphs'); ?>">Graphs & Trends</a>
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
                    <p>
                        Charts are generated from alumni profile, education, employment,
                        certification, and course data stored in the database.
                    </p>
                </div>
            </div>

            <div class="loading" id="loadingBox">
                Loading analytics charts from the database...
            </div>

            <div class="error-box" id="errorBox">
                Unable to load one or more charts. Please check the analytics API and API key.
            </div>

            <div class="chart-grid">
                <div class="chart-card">
                    <h3>Alumni by Programme</h3>
                    <p>Shows which academic programmes have the highest alumni representation.</p>
                    <div class="chart-wrapper">
                        <canvas id="programmeChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('programmeChart', 'alumni_by_programme')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Alumni by Graduation Year</h3>
                    <p>Shows graduate distribution over different completion years.</p>
                    <div class="chart-wrapper">
                        <canvas id="graduationYearChart"></canvas>
                    </div>
                    <button class="download-btn"
                        onclick="downloadChart('graduationYearChart', 'alumni_by_graduation_year')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Employment by Industry Sector</h3>
                    <p>Shows the sectors where alumni are currently or previously employed.</p>
                    <div class="chart-wrapper">
                        <canvas id="industrySectorChart"></canvas>
                    </div>
                    <button class="download-btn"
                        onclick="downloadChart('industrySectorChart', 'employment_by_industry_sector')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Most Common Job Titles</h3>
                    <p>Highlights frequent graduate job roles across alumni records.</p>
                    <div class="chart-wrapper">
                        <canvas id="jobTitleChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('jobTitleChart', 'common_job_titles')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Top Certifications</h3>
                    <p>Shows popular post-graduation certifications completed by alumni.</p>
                    <div class="chart-wrapper">
                        <canvas id="certificationChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('certificationChart', 'top_certifications')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Professional Course Trends</h3>
                    <p>Shows the most common professional courses completed after graduation.</p>
                    <div class="chart-wrapper">
                        <canvas id="courseChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('courseChart', 'professional_courses')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Top Employers</h3>
                    <p>Shows companies that appear most often in alumni employment records.</p>
                    <div class="chart-wrapper">
                        <canvas id="employerChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('employerChart', 'top_employers')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Geographic Distribution</h3>
                    <p>Shows alumni employment locations based on profile employment data.</p>
                    <div class="chart-wrapper">
                        <canvas id="geographyChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('geographyChart', 'geographic_distribution')">
                        Download Chart
                    </button>
                </div>

                <div class="chart-card">
                    <h3>Skills Gap Severity</h3>
                    <p>Uses certifications and professional courses as signals of post-graduation skill demand.</p>
                    <div class="chart-wrapper">
                        <canvas id="skillsGapChart"></canvas>
                    </div>
                    <button class="download-btn" onclick="downloadChart('skillsGapChart', 'skills_gap_severity')">
                        Download Chart
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        /*
         * Paste a valid University Analytics Dashboard API key below.
         * The key must have this scope:
         * read:alumni,read:analytics
         */
        const ANALYTICS_API_KEY = '7b8c85d83c6e8cb1de3704658f8c162674395ec4fdfa09f3cd00973e50bd2da9';

        const API_BASE = '<?= rtrim(site_url('api/analytics'), '/'); ?>';

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 900
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    enabled: true
                }
            }
        };

        const axisOptions = {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        };

        async function fetchAnalytics(endpoint) {
            const cleanEndpoint = endpoint.trim();

            const response = await fetch(`${API_BASE}/${cleanEndpoint}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${ANALYTICS_API_KEY}`,
                    'Accept': 'application/json'
                }
            });

            const responseText = await response.text();

            if (!response.ok) {
                console.error('Failed endpoint:', cleanEndpoint);
                console.error('Status:', response.status);
                console.error('Response:', responseText);
                throw new Error(`API request failed: ${cleanEndpoint}`);
            }

            let result;

            try {
                result = JSON.parse(responseText);
            } catch (error) {
                console.error('Invalid JSON from endpoint:', cleanEndpoint);
                console.error('Response:', responseText);
                throw new Error(`Invalid JSON response from ${cleanEndpoint}`);
            }

            if (result.status !== 'success') {
                console.error('API returned error:', cleanEndpoint, result);
                throw new Error(result.message || `Invalid response from ${cleanEndpoint}`);
            }

            return result.data || [];
        }

        function labels(data) {
            return data.map(item => item.label || 'Not Specified');
        }

        function totals(data) {
            return data.map(item => Number(item.total || 0));
        }

        function percentages(data) {
            return data.map(item => Number(item.percentage || 0));
        }

        function createChart(canvasId, type, data, label, options = commonOptions) {
            const ctx = document.getElementById(canvasId);

            return new Chart(ctx, {
                type: type,
                data: {
                    labels: labels(data),
                    datasets: [{
                        label: label,
                        data: totals(data),
                        borderWidth: 2
                    }]
                },
                options: options
            });
        }

        function createHorizontalBarChart(canvasId, data, label) {
            const ctx = document.getElementById(canvasId);

            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels(data),
                    datasets: [{
                        label: label,
                        data: totals(data),
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function createSkillsGapChart(canvasId, data) {
            const ctx = document.getElementById(canvasId);

            return new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels(data),
                    datasets: [{
                        label: 'Skills Gap Signal (%)',
                        data: percentages(data),
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        r: {
                            beginAtZero: true,
                            suggestedMax: 100
                        }
                    }
                }
            });
        }

        function downloadChart(canvasId, fileName) {
            const canvas = document.getElementById(canvasId);
            const image = canvas.toDataURL('image/png');

            const link = document.createElement('a');
            link.href = image;
            link.download = `${fileName}.png`;
            link.click();
        }

        async function loadCharts() {
            const loadingBox = document.getElementById('loadingBox');
            const errorBox = document.getElementById('errorBox');

            try {
                const [
                    programmeData,
                    graduationYearData,
                    industrySectorData,
                    jobTitleData,
                    certificationData,
                    courseData,
                    employerData,
                    geographyData,
                    skillsGapData
                ] = await Promise.all([
                    fetchAnalytics('programmes'),
                    fetchAnalytics('graduation-years'),
                    fetchAnalytics('industry-sectors'),
                    fetchAnalytics('job-titles'),
                    fetchAnalytics('certifications'),
                    fetchAnalytics('courses'),
                    fetchAnalytics('top-employers'),
                    fetchAnalytics('geography'),
                    fetchAnalytics('skills-gap')
                ]);

                createChart('programmeChart', 'bar', programmeData, 'Alumni Count', axisOptions);
                createChart('graduationYearChart', 'line', graduationYearData, 'Alumni Count', axisOptions);
                createChart('industrySectorChart', 'pie', industrySectorData, 'Employment Records', commonOptions);
                createHorizontalBarChart('jobTitleChart', jobTitleData, 'Job Title Count');
                createChart('certificationChart', 'doughnut', certificationData, 'Certification Count', commonOptions);
                createChart('courseChart', 'bar', courseData, 'Course Count', axisOptions);
                createChart('employerChart', 'bar', employerData, 'Employer Count', axisOptions);
                createChart('geographyChart', 'doughnut', geographyData, 'Location Count', commonOptions);
                createSkillsGapChart('skillsGapChart', skillsGapData);

                loadingBox.style.display = 'none';
            } catch (error) {
                console.error(error);
                loadingBox.style.display = 'none';
                errorBox.style.display = 'block';
                errorBox.innerHTML = 'Unable to load charts. Failed reason: ' + error.message;
            }
        }

        loadCharts();
    </script>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? html_escape($title) : 'API Documentation' ?></title>

    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/swagger-ui/swagger-ui.css'); ?>">

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background: #fafafa;
            font-family: Arial, sans-serif;
        }

        .docs-header {
            padding: 20px 24px;
            background: #1f2937;
            color: #fff;
        }

        .docs-header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
        }

        .docs-header p {
            margin: 4px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .docs-wrapper {
            padding: 0;
        }

        #swagger-ui {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="docs-header">
        <h1>Alumni Influencer API Documentation</h1>
        <p>This documentation describes the public developer API for retrieving the featured alumnus of the day.</p>
        <p><strong>Authentication:</strong> Use an API key generated from the Developer Dashboard as a Bearer token in
            the <code>Authorization</code> header.</p>
    </div>

    <div class="docs-wrapper">
        <div id="swagger-ui"></div>
    </div>

    <script src="<?= base_url('assets/swagger-ui/swagger-ui-bundle.js'); ?>"></script>
    <script src="<?= base_url('assets/swagger-ui/swagger-ui-standalone-preset.js'); ?>"></script>

    <script>
        window.onload = function () {
            window.ui = SwaggerUIBundle({
                url: "<?= isset($spec_url) ? $spec_url : site_url('api-spec.json') ?>",
                dom_id: '#swagger-ui',
                deepLinking: true,
                docExpansion: "list",
                displayRequestDuration: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>

</html>
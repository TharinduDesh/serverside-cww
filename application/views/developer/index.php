<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? html_escape($title) : 'Developer API Keys'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            background: #f8f9fa;
            color: #222;
        }

        h1,
        h2 {
            margin-top: 0;
        }

        .section {
            background: #fff;
            border: 1px solid #dcdcdc;
            padding: 16px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .success-message {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .error-message {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .generated-key-box {
            background: #f4f4f4;
            border: 1px solid #ccc;
            padding: 10px;
            word-break: break-all;
            margin-top: 10px;
            font-family: monospace;
        }

        .help-text {
            font-size: 13px;
            color: #555;
            margin-top: 6px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            max-width: 420px;
            padding: 8px;
            margin-top: 6px;
            box-sizing: border-box;
        }

        button {
            padding: 8px 14px;
            cursor: pointer;
        }

        .inline-form {
            display: inline;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
            background: #fff;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        table th {
            background: #f1f1f1;
        }

        .muted {
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Developer API Keys</h1>

    <p><a href="<?= site_url('dashboard'); ?>">Back to Dashboard</a></p>

    <?php if ($this->session->flashdata('success_message')): ?>
        <div class="success-message">
            <?= html_escape($this->session->flashdata('success_message')); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error_message')): ?>
        <div class="error-message">
            <?= $this->session->flashdata('error_message'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('generated_api_key')): ?>
        <div class="section">
            <h2>New API Key</h2>
            <p>Copy this key now. For security reasons, it will not be shown again.</p>
            <div class="generated-key-box">
                <?= html_escape($this->session->flashdata('generated_api_key')); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Generate API Key</h2>

        <?= form_open('developer/generate-key'); ?>
        <p>
            <label for="key_name">Key Name</label><br>
            <input type="text" name="key_name" id="key_name" maxlength="100" required>
        <div class="help-text">Example: Mobile AR Client, Demo Client, Testing Key</div>
        </p>

        <p>
            <label for="scope">Client Permission Scope</label><br>
            <select name="scope" id="scope" required>
                <option value="read:alumni,read:analytics">University Analytics Dashboard - read:alumni, read:analytics
                </option>
                <option value="read:alumni_of_day">Mobile AR App - read:alumni_of_day</option>
                <option value="read:alumni">Alumni Data Only - read:alumni</option>
                <option value="read:analytics">Analytics Data Only - read:analytics</option>
                <option value="read:donations">Donation Data Only - read:donations</option>
                <option value="full">Full Access - testing/admin only</option>
            </select>

        <div class="help-text">
            Select the smallest permission needed for the client. For the University Analytics
            Dashboard should use <strong>read:alumni,read:analytics</strong>, while the Mobile AR App
            should use <strong>read:alumni_of_day</strong>.
        </div>
        </p>

        <p>
            <button type="submit">Generate Key</button>
        </p>
        <?= form_close(); ?>
    </div>

    <div class="section">
        <h2>Your API Keys</h2>

        <?php if (!empty($api_keys)): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Prefix</th>
                    <th>Scope</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Expires At</th>
                    <th>Last Used</th>
                    <th>Revoked At</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($api_keys as $api_key): ?>
                    <tr>
                        <td><?= html_escape($api_key->key_name); ?></td>
                        <td><?= !empty($api_key->key_prefix) ? html_escape($api_key->key_prefix) : '-'; ?></td>
                        <td><?= !empty($api_key->scope) ? html_escape($api_key->scope) : 'read'; ?></td>
                        <td><?= (int) $api_key->is_active === 1 ? 'Active' : 'Revoked'; ?></td>
                        <td><?= html_escape($api_key->created_at); ?></td>
                        <td><?= !empty($api_key->expires_at) ? html_escape($api_key->expires_at) : 'Never'; ?></td>
                        <td><?= !empty($api_key->last_used_at) ? html_escape($api_key->last_used_at) : '-'; ?></td>
                        <td><?= !empty($api_key->revoked_at) ? html_escape($api_key->revoked_at) : '-'; ?></td>
                        <td>
                            <?php if ((int) $api_key->is_active === 1): ?>
                                <?= form_open('developer/revoke-key/' . $api_key->id, ['class' => 'inline-form', 'onsubmit' => "return confirm('Are you sure you want to revoke this API key?');"]); ?>
                                <button type="submit">Revoke</button>
                                <?= form_close(); ?>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No API keys created yet.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>API Usage Logs</h2>

        <?php if (!empty($usage_logs)): ?>
            <table>
                <tr>
                    <th>Key Name</th>
                    <th>Key Prefix</th>
                    <th>Scope</th>
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>Status Code</th>
                    <th>IP Address</th>
                    <th>Accessed At</th>
                </tr>
                <?php foreach ($usage_logs as $log): ?>
                    <tr>
                        <td><?= html_escape($log->key_name); ?></td>
                        <td><?= !empty($log->key_prefix) ? html_escape($log->key_prefix) : '-'; ?></td>
                        <td><?= !empty($log->scope) ? html_escape($log->scope) : '-'; ?></td>
                        <td><?= html_escape($log->endpoint); ?></td>
                        <td><?= html_escape($log->method); ?></td>
                        <td><?= isset($log->status_code) && $log->status_code !== null ? (int) $log->status_code : '-'; ?></td>
                        <td><?= !empty($log->ip_address) ? html_escape($log->ip_address) : '-'; ?></td>
                        <td><?= html_escape($log->accessed_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No API usage logs yet.</p>
        <?php endif; ?>
    </div>
</body>

</html>
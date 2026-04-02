<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Developer API Keys'; ?></title>
    <style>
        .section {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .generated-key-box {
            background: #f4f4f4;
            border: 1px solid #ccc;
            padding: 10px;
            word-break: break-all;
            margin-top: 10px;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Developer API Keys</h1>

    <p><a href="<?= site_url('auth/dashboard'); ?>">Back to Dashboard</a></p>

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

        <?= form_open('developer/generate_key'); ?>
            <p>
                <label for="key_name">Key Name</label><br>
                <input type="text" name="key_name" id="key_name" maxlength="100">
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
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Last Used</th>
                    <th>Revoked At</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($api_keys as $api_key): ?>
                    <tr>
                        <td><?= html_escape($api_key->key_name); ?></td>
                        <td><?= (int)$api_key->is_active === 1 ? 'Active' : 'Revoked'; ?></td>
                        <td><?= html_escape($api_key->created_at); ?></td>
                        <td><?= !empty($api_key->last_used_at) ? html_escape($api_key->last_used_at) : '-'; ?></td>
                        <td><?= !empty($api_key->revoked_at) ? html_escape($api_key->revoked_at) : '-'; ?></td>
                        <td>
                            <?php if ((int)$api_key->is_active === 1): ?>
                                <a href="<?= site_url('developer/revoke_key/' . $api_key->id); ?>" onclick="return confirm('Are you sure you want to revoke this API key?');">Revoke</a>
                            <?php else: ?>
                                -
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
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>IP Address</th>
                    <th>Accessed At</th>
                </tr>
                <?php foreach ($usage_logs as $log): ?>
                    <tr>
                        <td><?= html_escape($log->key_name); ?></td>
                        <td><?= html_escape($log->endpoint); ?></td>
                        <td><?= html_escape($log->method); ?></td>
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
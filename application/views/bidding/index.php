<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? html_escape($title) : 'Blind Bidding'; ?></title>
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

        table {
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h1>Blind Bidding</h1>

    <p><a href="<?= site_url('auth/dashboard'); ?>">Back to Dashboard</a></p>

    <p><a href="<?= site_url('bidding/featured_today'); ?>">View Featured Alumnus Today</a></p>

    <?php if ($this->session->flashdata('success_message')): ?>
        <div class="success-message">
            <?= $this->session->flashdata('success_message'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error_message')): ?>
        <div class="error-message">
            <?= $this->session->flashdata('error_message'); ?>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Your Bidding Summary</h2>
        <p><strong>Target feature date:</strong> <?= html_escape($feature_date); ?></p>
        <p><strong>Monthly featured wins:</strong> <?= (int)$monthly_wins; ?></p>
        <p><strong>Remaining monthly slots:</strong> <?= (int)$remaining_slots; ?></p>

        <?php if ($current_bid): ?>
            <p><strong>Your current bid:</strong> £<?= number_format((float)$current_bid->bid_amount, 2); ?></p>
            <p><strong>Current status:</strong> <?= html_escape(ucfirst($current_bid->status)); ?></p>
        <?php else: ?>
            <p>You have not placed a bid for this date yet.</p>
        <?php endif; ?>

        <p>
            Highest bid amount is hidden to preserve blind bidding.
        </p>
    </div>

    <div class="section">
        <h2>Place or Update Bid</h2>

        <?= form_open('bidding/place_bid'); ?>

            <p>
                <label for="feature_date">Feature Date</label><br>
                <input type="date" name="feature_date" id="feature_date" value="<?= html_escape($feature_date); ?>">
            </p>

            <p>
                <label for="bid_amount">Bid Amount (£)</label><br>
                <input type="number" step="0.01" min="0.01" name="bid_amount" id="bid_amount">
            </p>

            <p>
                <button type="submit">
                    <?= $current_bid ? 'Increase Bid' : 'Place Bid'; ?>
                </button>
            </p>

        <?= form_close(); ?>

        <p>
            You cannot see the highest bid. After saving, you will only see whether you are currently winning or outbid.
        </p>
    </div>

    <!-- Admin section for test purpose -->

    <div class="section">
        <h2>Test Winner Selection</h2>

        <p>
            This is for local testing/demo only. It will finalize the winner for the selected feature date.
        </p>

        <?= form_open('bidding/run_winner_selection_post'); ?>
            <input type="hidden" name="feature_date" value="<?= html_escape($feature_date); ?>">

            <p>
                <button
                    type="submit"
                    onclick="return confirm('Run winner selection for <?= html_escape($feature_date); ?>? This will finalize the result for that date.');"
                >
                    Run Winner Selection for <?= html_escape($feature_date); ?>
                </button>
            </p>
        <?= form_close(); ?>
    </div>

    <!-- End admin section -->

    <div class="section">
        <h2>Your Bid History</h2>

        <?php if (!empty($bid_history)): ?>
            <table>
                <tr>
                    <th>Feature Date</th>
                    <th>Your Bid</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                </tr>
                <?php foreach ($bid_history as $bid): ?>
                    <tr>
                        <td><?= html_escape($bid->feature_date); ?></td>
                        <td>£<?= number_format((float)$bid->bid_amount, 2); ?></td>
                        <td><?= html_escape(ucfirst($bid->status)); ?></td>
                        <td><?= html_escape($bid->updated_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have not placed any bids yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
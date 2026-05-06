<div class="card">
    <div class="alert alert-info">
        <strong>Ship Status (Last deploy): </strong> <?= $data->deployDate ?>
    </div>
    <h2>⚓️ Admin Command Center</h2>
    <form method="POST">
        <?= csrfInput($baseDTO->csrf_token) ?>
        <label for="TOKEN">Security Token:</label>
        <input type="password" name="TOKEN" id="TOKEN" autocomplete="one-time-code" required placeholder="••••••••">

        <div class="mt-3">
            <h4>⚙️ System Actions:</h4>
            <button type="submit" name="action" value="test_redis" style="background: #06b6d4;">⚡ Test Redis Connection</button>
            <button type="submit" name="action" value="init_db">Run Database Init</button>
            <button type="submit" name="action" value="clear_all">Reset App</button>
            <button type="submit" name="action" value="reset_logs" style="background: #ef4444;">🔥 Reset All Logs</button>
        </div>
        <div class="mt-4">
            <h4>⚡ Direct SQL Terminal:</h4>
            <textarea name="sql_query" rows="5" style="width:100%; font-family: monospace; background: #000; color: #0f0; padding: 10px;" placeholder="SELECT * FROM users;"></textarea>
            <button type="submit" name="action" value="sql_db" style="background: #d97706; margin-top: 10px;">Execute SQL Text</button>
        </div>

        <div class="mt-4">
            <h4>📄 View Logs:</h4>
            <button type="submit" name="action" value="log_info">Info Log</button>
            <button type="submit" name="action" value="log_error">Error Log</button>
            <button type="submit" name="action" value="log_sql">SQL Log</button>
            <button type="submit" name="action" value="log_warning">Warning Log</button>
        </div>
    </form>

    <?php if ($data->errorMessage): ?>
        <div class="status-msg <?= $data->success ? 'success' : 'error' ?>">
            <?= e($data->errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($data->logContent): ?>
        <pre><?= e($data->logContent) ?></pre>
    <?php endif; ?>
</div>
<?php if (!empty($data->queryResults)): ?>
    <div class="mt-4" style="overflow-x: auto;">
        <h4>📊 Query Results:</h4>
        <table class="admin-table">
            <thead>
                <tr>
                    <?php foreach (array_keys($data->queryResults[0]) as $column): ?>
                        <th><?= e($column) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data->queryResults as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= e((string)$value) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
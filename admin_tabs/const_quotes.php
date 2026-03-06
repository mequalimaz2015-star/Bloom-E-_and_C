<?php
$quotes = $pdo->query("SELECT * FROM construction_quotes ORDER BY created_at DESC")->fetchAll();
?>

<div class="card" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; overflow: hidden;">
    <div class="card-header"
        style="background: #fff; border-bottom: 1px solid #f1f5f9; padding: 25px; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to right, #ffffff, #f8fafc); flex-wrap: wrap; gap: 10px;">
        <span class="card-title"
            style="margin: 0; display: flex; align-items: center; gap: 12px; font-weight: 700; color: #1e293b;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #f59e0b;"></i> Client Quotes & Project Requests
        </span>
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div id="cquotes_bulk_actions" style="display: none; align-items: center; gap: 10px;">
                <span id="cquotes_selected_count" style="font-size: 13px; font-weight: 700; color: #2563eb;">0
                    selected</span>
                <form method="POST" style="display: flex; gap: 5px;">
                    <input type="hidden" name="cquotes_bulk_ids" id="cquotes_bulk_ids_input">
                    <button type="submit" name="bulk_delete_const_quotes" class="btn"
                        onclick="return confirm('Are you sure you want to delete the selected quote requests?')"
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; padding: 7px 16px; font-size: 12px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(239,68,68,0.3);">
                        <i class="fa-solid fa-trash-can"></i> Delete Selected
                    </button>
                </form>
            </div>
            <div
                style="font-size: 13px; color: #64748b; font-weight: 600; background: #fff; padding: 6px 15px; border-radius: 20px; border: 1px solid #e2e8f0;">
                Total Requests:
                <?= count($quotes) ?>
            </div>
        </div>
    </div>
    <div style="padding: 25px;">
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 15px; border-bottom: 2px solid #f1f5f9; width: 40px; text-align: center;">
                            <input type="checkbox" id="select_all_cquotes" onchange="toggleSelectAllCQuotes(this)"
                                style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                        </th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Client Details</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Project & Budget</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: left; font-weight: 600; color: #64748b; font-size: 13px;">
                            Status</th>
                        <th
                            style="padding: 15px; border-bottom: 2px solid #f1f5f9; text-align: right; font-weight: 600; color: #64748b; font-size: 13px;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($quotes)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 50px; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                    <i class="fa-solid fa-envelope-open-text" style="font-size: 40px; color: #e2e8f0;"></i>
                                    <span>No quote requests received yet.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($quotes as $q): ?>
                            <tr style="transition: 0.3s; cursor: pointer;"
                                onclick="viewQuote(<?= htmlspecialchars(json_encode($q)) ?>)">
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: center;"
                                    onclick="event.stopPropagation()">
                                    <input type="checkbox" class="cquotes-checkbox" value="<?= $q['id'] ?>"
                                        onchange="updateCQuotesBulkUI()"
                                        style="width: 16px; height: 16px; cursor: pointer; accent-color: #2563eb;">
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                    <div style="font-weight: 700; color: #1e293b;">
                                        <?= htmlspecialchars($q['client_name']) ?>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        <?= htmlspecialchars($q['email']) ?>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        <?= htmlspecialchars($q['phone']) ?>
                                    </div>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                    <div style="font-weight: 600; color: #475569;">
                                        <?= htmlspecialchars($q['project_type']) ?>
                                    </div>
                                    <div
                                        style="font-size: 11px; display: inline-block; background: #fff7ed; color: #c2410c; padding: 2px 8px; border-radius: 4px; border: 1px solid #ffedd5; margin-top: 5px;">
                                        <?= htmlspecialchars($q['budget']) ?> Budget
                                    </div>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9;">
                                    <?php
                                    $q_colors = [
                                        'Pending' => ['bg' => '#fef9c3', 'text' => '#a16207'],
                                        'Contacted' => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
                                        'Quoted' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                                        'Rejected' => ['bg' => '#fee2e2', 'text' => '#b91c1c']
                                    ];
                                    $qcol = $q_colors[$q['status']] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                                    ?>
                                    <span
                                        style="background: <?= $qcol['bg'] ?>; color: <?= $qcol['text'] ?>; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                        <?= $q['status'] ?>
                                    </span>
                                    <div style="font-size: 10px; color: #94a3b8; margin-top: 5px;">
                                        <?= date('M d, Y', strtotime($q['created_at'])) ?>
                                    </div>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #f1f5f9; text-align: right;"
                                    onclick="event.stopPropagation()">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <button class="btn-icon" onclick="viewQuote(<?= htmlspecialchars(json_encode($q)) ?>)"
                                            style="background: #f1f5f9; color: #475569; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i
                                                class="fa-solid fa-eye"></i></button>
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="id" value="<?= $q['id'] ?>">
                                            <select name="status" onchange="this.form.submit()"
                                                style="font-size: 11px; padding: 5px; border-radius: 6px; border: 1px solid #e2e8f0; outline: none; background: #fff;">
                                                <option value="" disabled selected>Update Status</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Contacted">Contacted</option>
                                                <option value="Quoted">Quoted</option>
                                                <option value="Rejected">Rejected</option>
                                            </select>
                                            <input type="hidden" name="update_const_quote" value="1">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleSelectAllCQuotes(source) {
        document.querySelectorAll('.cquotes-checkbox').forEach(cb => cb.checked = source.checked);
        updateCQuotesBulkUI();
    }
    function updateCQuotesBulkUI() {
        const checked = document.querySelectorAll('.cquotes-checkbox:checked');
        const all = document.querySelectorAll('.cquotes-checkbox');
        const bulk = document.getElementById('cquotes_bulk_actions');
        const count = document.getElementById('cquotes_selected_count');
        const ids = document.getElementById('cquotes_bulk_ids_input');
        const selAll = document.getElementById('select_all_cquotes');
        if (checked.length > 0) {
            bulk.style.display = 'flex';
            count.innerText = checked.length + ' selected';
            ids.value = Array.from(checked).map(cb => cb.value).join(',');
        } else { bulk.style.display = 'none'; }
        if (all.length > 0 && checked.length === all.length) { selAll.checked = true; selAll.indeterminate = false; }
        else if (checked.length > 0) { selAll.checked = false; selAll.indeterminate = true; }
        else { selAll.checked = false; selAll.indeterminate = false; }
    }
</script>

<!-- View Quote Modal -->
<div id="viewQuoteModal" class="modal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
    <div
        style="background:#fff; width:90%; max-width:550px; margin: 60px auto; border-radius:24px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
        <div
            style="background: #f59e0b; padding: 25px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin:0; font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-message"></i> Quote Details
            </h3>
            <span onclick="closeQuoteModal()" style="cursor:pointer; font-size:24px;">&times;</span>
        </div>
        <div style="padding: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing: 1px;">Client
                        Name</label>
                    <div id="viewClientName" style="font-weight: 700; color: #1e293b; font-size: 16px;"></div>
                </div>
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing: 1px;">Sent
                        On</label>
                    <div id="viewSentDate" style="color: #475569; font-weight: 600;"></div>
                </div>
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing: 1px;">Current
                        Status</label>
                    <div id="viewStatusBadge"></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing: 1px;">Email
                        Address</label>
                    <div id="viewEmail" style="color: #475569; font-weight: 600;"></div>
                </div>
                <div>
                    <label
                        style="display:block; font-size:11px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing: 1px;">Phone
                        Number</label>
                    <div id="viewPhone" style="color: #475569; font-weight: 600;"></div>
                </div>
            </div>

            <div
                style="background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #f1f5f9; margin-bottom: 25px;">
                <div style="display: flex; gap: 40px; margin-bottom: 15px;">
                    <div>
                        <label
                            style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:3px; text-transform:uppercase;">Project
                            Type</label>
                        <div id="viewProjectType" style="font-weight: 700; color: #1e293b;"></div>
                    </div>
                    <div>
                        <label
                            style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:3px; text-transform:uppercase;">Budget
                            Level</label>
                        <div id="viewBudget" style="font-weight: 700; color: #c2410c;"></div>
                    </div>
                </div>
                <label
                    style="display:block; font-size:10px; font-weight:700; color:#94a3b8; margin-bottom:5px; text-transform:uppercase;">CLIENT
                    MESSAGE</label>
                <div id="viewMessage"
                    style="color: #475569; line-height: 1.6; font-size: 14px; white-space: pre-wrap; margin-bottom: 20px;">
                </div>

                <form method="POST">
                    <input type="hidden" name="update_const_quote" value="1">
                    <input type="hidden" name="id" id="replyQuoteId">
                    <label
                        style="display:block; font-size:10px; font-weight:700; color:#0f172a; margin-bottom:5px; text-transform:uppercase;">YOUR
                        REPLY / ACTION NOTES</label>
                    <textarea name="admin_reply" id="viewAdminReply" rows="4"
                        style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:12px; font-size:13px; margin-bottom:15px; outline:none; transition:0.3s;"
                        placeholder="Type your response or internal notes here..."></textarea>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="status" id="viewStatusSelect" required
                            style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; font-size: 13px; font-weight: 600;">
                            <option value="Pending">Mark as Pending</option>
                            <option value="Contacted">Mark as Contacted</option>
                            <option value="Quoted">Mark as Quoted</option>
                            <option value="Rejected">Mark as Rejected</option>
                        </select>
                        <button type="submit"
                            style="background:#f59e0b; color:#fff; border:none; padding:12px 25px; border-radius:12px; font-weight:700; cursor:pointer;">Update
                            & Save</button>
                    </div>
                </form>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button onclick="closeQuoteModal()"
                    style="padding:12px 35px; border-radius:12px; border:none; background:#1e293b; color:#fff; font-weight:700; cursor:pointer;">Close
                    Overview</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewQuote(q) {
        document.getElementById('viewQuoteModal').style.display = 'block';
        document.getElementById('viewClientName').innerText = q.client_name;
        document.getElementById('viewSentDate').innerText = new Date(q.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
        document.getElementById('viewEmail').innerText = q.email;
        document.getElementById('viewPhone').innerText = q.phone;
        document.getElementById('viewProjectType').innerText = q.project_type;
        document.getElementById('viewBudget').innerText = q.budget;
        document.getElementById('viewMessage').innerText = q.message;
        document.getElementById('viewAdminReply').value = q.admin_reply || '';
        document.getElementById('replyQuoteId').value = q.id;
        document.getElementById('viewStatusSelect').value = q.status;

        let colors = {
            'Pending': { bg: '#fef9c3', text: '#a16207' },
            'Contacted': { bg: '#e0f2fe', text: '#0369a1' },
            'Quoted': { bg: '#dcfce7', text: '#15803d' },
            'Rejected': { bg: '#fee2e2', text: '#b91c1c' }
        };
        let c = colors[q.status] || { bg: '#f1f5f9', text: '#475569' };
        document.getElementById('viewStatusBadge').innerHTML = `<span style="background:${c.bg}; color:${c.text}; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700;">${q.status}</span>`;
    }

    function closeQuoteModal() {
        document.getElementById('viewQuoteModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('viewQuoteModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
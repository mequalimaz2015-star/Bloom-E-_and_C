<div style="margin-bottom: 20px; text-align: right;">
    <button onclick="document.getElementById('addJobModal').style.display='flex';" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Post New Job
    </button>
</div>
<div class="modal-overlay" id="addJobModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title">Post New Job</span>
            <button type="button" onclick="document.getElementById('addJobModal').style.display='none';" class="btn"
                style="background:none; border:none; color:#888; font-size:22px;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="add_job" value="1">
            <div class="form-row" style="margin-top:15px;">
                <input type="text" name="title" placeholder="Job Title" required>
                <input type="text" name="category" placeholder="Category (e.g. Kitchen, Management)" required>
            </div>
            <div class="form-row">
                <select name="type" required>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Contract">Contract</option>
                </select>
                <input type="text" name="location" placeholder="Location" value="Addis Ababa" required>
            </div>
            <div class="form-row" style="margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="font-size:12px; color:#64748b; margin-bottom:5px; display:block;">Closing Date &
                        Time</label>
                    <input type="datetime-local" name="closing_date" required>
                </div>
            </div>
            <textarea name="description" placeholder="Job Description" rows="4" required
                style="margin-bottom:15px;"></textarea>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn" onclick="document.getElementById('addJobModal').style.display='none';"
                    style="background:#f8f9fa; color:#333; border:1px solid #ddd;">Cancel</button>
                <button type="submit" class="btn btn-primary">Post Job</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header"><span class="card-title">My Job Listings</span></div>
    <table>
        <tr>
            <th>Job Title</th>
            <th>Category</th>
            <th>Type</th>
            <th>Closing At</th>
            <th>Time Left</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        $jobs_list = $pdo->query("SELECT * FROM jobs ORDER BY id DESC")->fetchAll();
        foreach ($jobs_list as $job): ?>
            <tr>
                <td><strong>
                        <?= htmlspecialchars($job['title']) ?>
                    </strong><br><small>
                        <?= htmlspecialchars($job['location']) ?>
                    </small>
                </td>
                <td>
                    <?= htmlspecialchars($job['category']) ?>
                </td>
                <td><span class="badge" style="background:#e9ecef; color:#495057; border:1px solid #ddd;">
                        <?= $job['type'] ?>
                    </span>
                </td>
                <td>
                    <span
                        style="font-size: 13px; color: <?= (strtotime($job['closing_date']) < time()) ? '#ef4444' : '#64748b' ?>;">
                        <i class="fa-solid fa-clock"></i> <?= date("M d, Y H:i", strtotime($job['closing_date'])) ?>
                    </span>
                </td>
                <td>
                    <?php
                    $time_left = strtotime($job['closing_date']) - time();
                    if ($time_left < 0): ?>
                        <span class="badge" style="background:#fee2e2; color:#b91c1c;">Expired</span>
                    <?php else:
                        $days = floor($time_left / (60 * 60 * 24));
                        $hours = floor(($time_left % (60 * 60 * 24)) / (60 * 60));
                        $mins = floor(($time_left % (60 * 60)) / 60);
                        $urgent = ($days < 10);
                        ?>
                        <span style="font-size: 13px; font-weight: 600; color: <?= $urgent ? '#ef4444' : '#10b981' ?>;">
                            <?= $days ?>d <?= $hours ?>h <?= $mins ?>m
                            <?php if ($urgent): ?><br><small style="text-transform:uppercase; font-size:9px;">Hurry! Closing
                                    soon</small><?php endif; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td><span class="badge <?= strtolower($job['status']) ?>">
                        <?= $job['status'] ?>
                    </span></td>
                <td>
                    <div class="action-flex" style="gap: 12px;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <button class="btn-icon btn-view" title="View Job"
                                onclick="viewJob(<?= htmlspecialchars(json_encode($job)) ?>)">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <span
                                style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">View</span>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <button class="btn-icon btn-edit" title="Edit Job"
                                onclick="editJob(<?= htmlspecialchars(json_encode($job)) ?>)">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <span
                                style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Edit</span>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <button type="button" class="btn-icon btn-delete" title="Delete"
                                onclick="modernDelete('delete_job', '<?= $job['id'] ?>', '<?= htmlspecialchars($job['title'], ENT_QUOTES) ?>', 'Job Listing')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <span
                                style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Delete</span>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Edit Job Modal -->
<div class="modal-overlay" id="editJobModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header">
            <span class="card-title">Edit Job Listing</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="update_job" value="1">
            <input type="hidden" name="id" id="edit_job_id">
            <div class="form-row">
                <input type="text" name="title" id="edit_job_title" required>
                <input type="text" name="category" id="edit_job_category" required>
            </div>
            <div class="form-row">
                <select name="type" id="edit_job_type" required>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Contract">Contract</option>
                </select>
                <input type="text" name="location" id="edit_job_location" required>
            </div>
            <div class="form-row" style="margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="font-size:12px; color:#64748b; margin-bottom:5px; display:block;">Closing Date &
                        Time</label>
                    <input type="datetime-local" name="closing_date" id="edit_job_closing" required>
                </div>
            </div>
            <textarea name="description" id="edit_job_desc" rows="4" style="width:100%; margin-bottom:15px;"></textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;">Update Job</button>
        </form>
    </div>
</div>

<!-- View Job Modal -->
<div class="modal-overlay" id="viewJobModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header">
            <span class="card-title">Job Vacancy Info</span>
            <button onclick="closeModals()" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <div class="form-row">
            <div><label style="font-size:12px; color:#888;">Job Title</label>
                <p id="view_job_title" style="font-weight:700;"></p>
            </div>
            <div><label style="font-size:12px; color:#888;">Category</label>
                <p id="view_job_category"></p>
            </div>
        </div>
        <div class="form-row">
            <div><label style="font-size:12px; color:#888;">Type</label>
                <p id="view_job_type"></p>
            </div>
            <div><label style="font-size:12px; color:#888;">Location</label>
                <p id="view_job_location"></p>
            </div>
            <div><label style="font-size:12px; color:#888;">Closing Date</label>
                <p id="view_job_closing" style="font-weight:700; color:#ef4444;"></p>
            </div>
        </div>
        <div style="margin-top:10px;">
            <label style="font-size:12px; color:#888;">Description</label>
            <p id="view_job_desc" style="font-size:14px; color:#555; line-height:1.5; white-space: pre-line;">
            </p>
        </div>
        <button onclick="closeModals()" class="btn btn-primary" style="width:100%; margin-top:20px;">Close
            View</button>
    </div>
</div>
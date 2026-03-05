<div style="margin-bottom: 20px; text-align: right;">
    <button onclick="document.getElementById('addPayModal').style.display='flex';" class="btn btn-primary"
        id="addPayBtn"><i class="fa-solid fa-plus"></i> Generate Salary Slip</button>
</div>
<div class="modal-overlay" id="addPayModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header" style="border-bottom:none; margin-bottom:5px; padding-bottom:0;">
            <span class="card-title" style="font-size: 20px;">Generate Salary Slip</span>
            <button type="button" onclick="document.getElementById('addPayModal').style.display='none';" class="btn"
                style="background:none; border:none; color:#888; font-size:22px; cursor:pointer; padding:0;"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST">
            <input type="hidden" name="add_payroll" value="1">
            <div class="form-row" style="margin-top: 15px;">
                <select name="employee_id" required>
                    <option value="" disabled selected>Select Employee</option>
                    <?php
                    $active_staff_payroll = $pdo->query("SELECT id, name, salary FROM employees WHERE status='Active'")->fetchAll();
                    foreach ($active_staff_payroll as $emp):
                        ?>
                        <option value="<?= $emp['id'] ?>" data-salary="<?= $emp['salary'] ?>">
                            <?= htmlspecialchars($emp['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <script>
                    document.querySelector('select[name="employee_id"]').addEventListener('change', function () {
                        const salary = this.options[this.selectedIndex].getAttribute('data-salary');
                        document.querySelector('input[name="base_salary"]').value = salary || 0;
                    });
                </script>
                <input type="month" name="salary_month" value="<?= date('Y-m') ?>" required>
            </div>
            <div class="form-row">
                <input type="number" step="0.01" name="base_salary" placeholder="Base Salary ($)" required>
                <input type="number" step="0.01" name="bonus" placeholder="Bonus ($)" value="0">
            </div>
            <div class="form-row">
                <input type="number" step="0.01" name="deductions" placeholder="Deductions ($)" value="0">
                <select name="status" required>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="card" style="margin-top: 25px; border: 1px dashed #cbd5e1; background: #f8fafc;">
                <div style="padding: 15px; font-size: 13px; color: #64748b;">
                    <i class="fa-solid fa-circle-info"></i> <strong>Note:</strong> Automated calculations will include:
                    <ul style="margin-left: 20px; margin-top: 5px;">
                        <li>Attendance (Present vs Absent)</li>
                        <li>Overtime (Rate x 1.5)</li>
                        <li>Late Penalties (50 ETB per late)</li>
                        <li>Salary Advances tracked this month</li>
                    </ul>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn" onclick="document.getElementById('addPayModal').style.display='none';"
                    style="background: #f8f9fa; color: #333; border: 1px solid #ddd; border-radius: 20px; padding: 10px 20px;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 10px 20px;">Generate
                    & Save Payroll</button>
            </div>
        </form>
    </div>
</div>

<div style="margin-bottom: 20px; text-align: left;">
    <button onclick="document.getElementById('addAdvanceModal').style.display='flex';" class="btn"
        style="background: #f59e0b; color: #fff; border-radius: 20px; padding: 10px 20px;"><i
            class="fa-solid fa-hand-holding-dollar"></i> Record Salary Advance</button>
</div>

<div class="modal-overlay" id="addAdvanceModal" style="display: none;">
    <div class="modal-content">
        <div class="card-header">
            <span class="card-title">Salary Advance Request</span>
            <button onclick="document.getElementById('addAdvanceModal').style.display='none';" class="btn"
                style="background:none; border:none; font-size:20px;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="add_salary_advance" value="1">
            <div class="form-row" style="margin-top: 15px;">
                <select name="employee_id" required>
                    <option value="" disabled selected>Select Employee</option>
                    <?php foreach ($active_staff_payroll as $emp): ?>
                        <option value="<?= $emp['id'] ?>">
                            <?= htmlspecialchars($emp['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="date" name="advance_date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-row">
                <input type="number" step="0.01" name="amount" placeholder="Advance Amount (ETB)" required>
                <input type="text" name="reason" placeholder="Reason for advance">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 20px;">Record
                Advance</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Payroll History</span></div>
    <table>
        <tr>
            <th>Month</th>
            <th>Employee</th>
            <th>Calculations</th>
            <th>Net Salary</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $payroll = $pdo->query("SELECT p.*, e.name FROM payroll p JOIN employees e ON p.employee_id = e.id ORDER BY p.id DESC")->fetchAll();
        foreach ($payroll as $pay):
            ?>
            <tr>
                <td>
                    <?= $pay['salary_month'] ?>
                </td>
                <td><strong>
                        <?= htmlspecialchars($pay['name']) ?>
                    </strong></td>
                <td style="font-size: 11px; color: #64748b;">
                    Base:
                    <?= number_format($pay['base_salary'], 2) ?> ETB<br>
                    OT: +
                    <?= number_format($pay['overtime_amount'], 2) ?> (
                    <?= $pay['total_overtime_hours'] ?>h)<br>
                    Adv: -
                    <?= number_format($pay['advance_deduction'], 2) ?> ETB<br>
                    Deduct/Late: -
                    <?= number_format($pay['deductions'], 2) ?> ETB
                </td>
                <td><strong>
                        <?= number_format($pay['net_salary'], 2) ?> ETB
                    </strong></td>
                <td><span class="badge <?= $pay['status'] == 'Paid' ? 'confirmed' : 'pending' ?>">
                        <?= $pay['status'] ?>
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $pay['id'] ?>">
                                <input type="hidden" name="update_payroll_status" value="1">
                                <select name="status" onchange="this.form.submit()"
                                    style="padding:4px; font-size:11px; border-radius: 6px; border: 1px solid #ddd; width: 85px;">
                                    <option value="Unpaid" <?= $pay['status'] == 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                    <option value="Paid" <?= $pay['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                </select>
                            </form>
                            <span
                                style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Status</span>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <button class="btn"
                                style="width: 38px; height: 38px; background: #3b82f6; color: #fff; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                onclick="viewPayslip(<?= htmlspecialchars(json_encode($pay)) ?>)">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 16px;"></i>
                            </button>
                            <span style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">View
                                Slip</span>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<!-- Payslip Modal -->
<div class="modal-overlay" id="payslipModal" style="display: none;">
    <div class="payslip-modal" id="payslipContent">
        <div class="no-print" style="position: absolute; right: 20px; top: 20px;">
            <button onclick="closeModals()"
                style="background:none; border:none; font-size:24px; color:#94a3b8; cursor:pointer;">&times;</button>
        </div>
        <div class="payslip-header">
            <div style="color: #dfb180; font-weight: 900; font-size: 28px; margin-bottom: 5px;">BLOOM AFRICA
            </div>
            <div class="payslip-title">Salary Pay Slip</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 5px;">Restaurant & Hotel Management System
            </div>
        </div>

        <div class="payslip-info-grid">
            <div>
                <div class="info-label">Employee Name</div>
                <div class="info-value" id="slip_emp_name">---</div>
            </div>
            <div>
                <div class="info-label">Email Address</div>
                <div class="info-value" id="slip_emp_email">---</div>
            </div>
            <div>
                <div class="info-label">Payment Month</div>
                <div class="info-value" id="slip_month">---</div>
            </div>
            <div>
                <div class="info-label">Status</div>
                <div class="info-value" id="slip_status">---</div>
            </div>
        </div>

        <table class="earnings-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (ETB)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td style="text-align: right;" id="slip_base">0.00</td>
                </tr>
                <tr>
                    <td>Bonus & Incentives</td>
                    <td style="text-align: right; color: #059669;" id="slip_bonus">+0.00</td>
                </tr>
                <tr>
                    <td>Deductions / Tax</td>
                    <td style="text-align: right; color: #dc2626;" id="slip_deduct">-0.00</td>
                </tr>
                <tr>
                    <td>Overtime (1.5x Rate)</td>
                    <td style="text-align: right; color: #059669;" id="slip_ot">+0.00</td>
                </tr>
                <tr>
                    <td>Salary Advance Deduction</td>
                    <td style="text-align: right; color: #dc2626;" id="slip_advance">-0.00</td>
                </tr>
                <tr class="total-row">
                    <td style="border-bottom-left-radius: 8px;">NET SALARY</td>
                    <td style="text-align: right; border-bottom-right-radius: 8px;" id="slip_net">0.00</td>
                </tr>
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; margin-top: 40px; font-size: 12px;">
            <div style="text-align: center;">
                <div style="width: 150px; border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
                <div>Employee Signature</div>
            </div>
            <div style="text-align: center;">
                <div style="width: 150px; border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
                <div>Authorized By</div>
            </div>
        </div>

        <div style="margin-top: 30px;" class="no-print">
            <button onclick="window.print()" class="btn btn-primary"
                style="width:100%; border-radius:12px; padding: 15px; font-weight: 800;"><i
                    class="fa-solid fa-print"></i> Print Official Receipt</button>
        </div>
    </div>
</div>
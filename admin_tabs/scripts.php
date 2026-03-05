<script>
    // Chart Initialization
    <?php if (($active_tab ?? 'dashboard') == 'dashboard'): ?>
        document.addEventListener("DOMContentLoaded", function () {
            // Services Pie Chart
            const ctxPie = document.getElementById('servicesPieChart').getContext('2d');
            const dataLabels = <?= $chart_labels ?? '[]' ?>;
            const dataCounts = <?= $chart_data ?? '[]' ?>;
            const finalLabels = dataLabels.length > 0 ? dataLabels : ['Main Course', 'Starter', 'Dessert', 'Beverages'];
            const finalData = dataCounts.length > 0 ? dataCounts : [45, 25, 15, 15];

            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: finalLabels,
                    datasets: [{
                        label: 'Items',
                        data: finalData,
                        backgroundColor: ['#dfb180', '#8e6c46', '#c8935c', '#2b211a', '#594c40', '#fff5eb'],
                        borderWidth: 0, hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 20, font: { family: "'Inter', sans-serif", size: 12 } } } },
                    cutout: '70%'
                }
            });

            // Weekly Performance Line Chart
            const ctxLine = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: <?= $perf_labels_json ?? '[]' ?>,
                    datasets: [{
                        label: 'Engagement Performance (%)',
                        data: <?= $perf_data_json ?? '[]' ?>,
                        borderColor: '#dfb180',
                        backgroundColor: 'rgba(223, 177, 128, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#dfb180',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: function (value) { return value + "%" } }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    <?php endif; ?>

    // Live Clock Logic
    function updateClock() {
        const now = new Date();
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.innerHTML = '<i class="fa-regular fa-clock"></i> ' + now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
    }
    setInterval(updateClock, 1000);
    document.addEventListener("DOMContentLoaded", updateClock);

    function toggleLogout() { document.getElementById('logoutDropdown').classList.toggle('show'); }
    function closeModals() {
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.style.display = 'none';
            const content = modal.querySelector('.modal-content');
            if (content) { content.style.transform = 'none'; content.style.left = 'auto'; content.style.top = 'auto'; }
        });
    }

    // Draggable Logic
    document.querySelectorAll('.modal-content').forEach(modalContent => {
        const header = modalContent.querySelector('.card-header');
        if (!header) return;
        let isDragging = false;
        let currentX, currentY, initialX, initialY, xOffset = 0, yOffset = 0;
        header.addEventListener('mousedown', dragStart);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', dragEnd);
        function dragStart(e) { if (e.target.closest('button')) return; initialX = e.clientX - xOffset; initialY = e.clientY - yOffset; isDragging = true; }
        function drag(e) { if (isDragging) { e.preventDefault(); currentX = e.clientX - initialX; currentY = e.clientY - initialY; xOffset = currentX; yOffset = currentY; modalContent.style.transform = `translate(${currentX}px, ${currentY}px)`; } }
        function dragEnd() { isDragging = false; }
    });

    function editMenu(item) {
        document.getElementById('edit_menu_id').value = item.id;
        document.getElementById('edit_menu_name').value = item.name;
        document.getElementById('edit_menu_category').value = item.category;
        document.getElementById('edit_menu_price').value = item.price;
        document.getElementById('edit_menu_image').value = item.image_url;
        document.getElementById('edit_menu_desc').value = item.description;

        const preview = document.getElementById('edit_menu_preview');
        const placeholder = document.getElementById('edit_menu_placeholder');
        if (item.image_url) {
            preview.src = item.image_url;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
        }

        document.getElementById('editMenuModal').style.display = 'flex';
    }

    function viewMenu(item) {
        document.getElementById('view_menu_name').innerText = item.name;
        document.getElementById('view_menu_category').innerText = item.category;
        document.getElementById('view_menu_price').innerText = parseFloat(item.price).toLocaleString() + ' ETB';
        document.getElementById('view_menu_desc').innerText = item.description || 'No description available.';
        const img = document.getElementById('view_menu_img');
        if (item.image_url) { img.src = item.image_url; img.style.display = 'block'; } else { img.style.display = 'none'; }
        document.getElementById('viewMenuModal').style.display = 'flex';
    }

    function editEmp(emp) {
        document.getElementById('edit_emp_id').value = emp.id;
        document.getElementById('edit_emp_title').value = emp.title || '';
        document.getElementById('edit_emp_first_name').value = emp.first_name || '';
        document.getElementById('edit_emp_middle_name').value = emp.middle_name || '';
        document.getElementById('edit_emp_last_name').value = emp.last_name || '';
        document.getElementById('edit_emp_role').value = emp.role;
        document.getElementById('edit_emp_email').value = emp.email || '';
        document.getElementById('edit_emp_phone').value = emp.phone || '';
        document.getElementById('edit_emp_salary').value = emp.salary;
        document.getElementById('edit_emp_salary_type').value = emp.salary_type || 'Monthly';
        document.getElementById('edit_emp_date').value = emp.join_date;
        document.getElementById('edit_emp_dob').value = emp.date_of_birth || '';
        document.getElementById('edit_emp_gender').value = emp.gender || 'Male';
        document.getElementById('edit_emp_address').value = emp.address || '';
        document.getElementById('edit_emp_emer_name').value = emp.emergency_contact_name || '';
        document.getElementById('edit_emp_emer_phone').value = emp.emergency_contact_phone || '';
        document.getElementById('edit_emp_bio').value = emp.bio || '';

        const preview = document.getElementById('edit_emp_photo_preview');
        if (emp.photo) {
            preview.src = emp.photo;
        } else {
            preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&background=dfb180&color=fff&size=200`;
        }

        document.getElementById('editEmpModal').style.display = 'flex';
    }

    function viewEmp(emp) {
        document.getElementById('view_emp_title').innerText = emp.title || '';
        document.getElementById('view_emp_name').innerText = emp.name;
        document.getElementById('view_emp_role').innerText = emp.role;
        document.getElementById('view_emp_email').innerText = emp.email || 'N/A';
        document.getElementById('view_emp_phone').innerText = emp.phone;
        document.getElementById('view_emp_salary').innerText = parseFloat(emp.salary).toLocaleString() + ' ETB';
        document.getElementById('view_emp_date').innerText = emp.join_date;
        document.getElementById('view_emp_gender').innerText = emp.gender || 'N/A';
        document.getElementById('view_emp_dob').innerText = emp.date_of_birth || 'N/A';
        document.getElementById('view_emp_address').innerText = emp.address || 'Address Not Provided';
        document.getElementById('view_emp_emer_name').innerText = emp.emergency_contact_name || 'N/A';
        document.getElementById('view_emp_emer_phone').innerText = emp.emergency_contact_phone || 'N/A';
        document.getElementById('view_emp_id_display').innerText = emp.id_number || ('BA-' + String(emp.id).padStart(3, '0'));

        const photoImg = document.getElementById('view_emp_photo');
        if (emp.photo) {
            photoImg.src = emp.photo;
        } else {
            photoImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&background=dfb180&color=fff&size=200`;
        }

        document.getElementById('viewEmpModal').style.display = 'flex';
    }

    function editJob(job) {
        document.getElementById('edit_job_id').value = job.id;
        document.getElementById('edit_job_title').value = job.title;
        document.getElementById('edit_job_category').value = job.category;
        document.getElementById('edit_job_type').value = job.type;
        document.getElementById('edit_job_location').value = job.location;
        document.getElementById('edit_job_desc').value = job.description;
        document.getElementById('edit_job_closing').value = job.closing_date;
        document.getElementById('editJobModal').style.display = 'flex';
    }

    function viewJob(job) {
        document.getElementById('view_job_title').innerText = job.title;
        document.getElementById('view_job_category').innerText = job.category;
        document.getElementById('view_job_type').innerText = job.type;
        document.getElementById('view_job_location').innerText = job.location;
        document.getElementById('view_job_closing').innerText = job.closing_date ? new Date(job.closing_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        document.getElementById('view_job_desc').innerText = job.description;
        document.getElementById('viewJobModal').style.display = 'flex';
    }

    function showIDCard(emp) {
        document.getElementById('id_card_name').innerText = (emp.title ? emp.title + ' ' : '') + emp.name;
        document.getElementById('id_card_role').innerText = emp.role;
        document.getElementById('id_card_no').innerText = ': ' + (emp.id_number || ('BA-' + String(emp.id).padStart(3, '0')));
        document.getElementById('id_card_dob').innerText = ': ' + (emp.date_of_birth || 'N/A');
        document.getElementById('id_card_email').innerText = ': ' + (emp.email || 'N/A');
        document.getElementById('id_card_phone').innerText = ': ' + (emp.phone || 'N/A');

        const photoImg = document.getElementById('id_card_img');
        if (emp.photo) { photoImg.src = emp.photo; }
        else { photoImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name)}&background=0054a6&color=fff&size=200`; }

        // Update QR code with employee data
        const qrData = `BloomID|${emp.id_number}|${emp.name}`;
        document.getElementById('id_card_qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrData)}`;

        document.getElementById('idCardModal').style.display = 'flex';
    }

    function generateIDFromForm() {
        const title = document.getElementById('edit_emp_title').value;
        const first = document.getElementById('edit_emp_first_name').value;
        const middle = document.getElementById('edit_emp_middle_name').value;
        const last = document.getElementById('edit_emp_last_name').value;
        const name = `${first} ${middle} ${last}`.trim();

        const emp = {
            id: document.getElementById('edit_emp_id').value,
            id_number: 'BA-' + String(document.getElementById('edit_emp_id').value).padStart(3, '0'), // Estimate ID for preview
            title: title,
            name: name,
            role: document.getElementById('edit_emp_role').value,
            email: document.getElementById('edit_emp_email').value,
            phone: document.getElementById('edit_emp_phone').value,
            date_of_birth: document.getElementById('edit_emp_dob').value,
            photo: document.getElementById('edit_emp_photo_preview').src.includes('http') ? document.getElementById('edit_emp_photo_preview').src : null
        };
        showIDCard(emp);
    }

    function previewImage(input, previewId = 'photo_preview') {
        const preview = document.getElementById(previewId);
        const placeholderId = previewId.replace('Preview', 'Placeholder');
        const placeholder = document.getElementById(placeholderId) || document.getElementById('photo_placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMenuImage(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function confirmDelete(id, name) {
        modernDelete('delete_employee', id, name, 'Employee Record');
    }

    function modernDelete(formAction, id, name, typeLabel = "item") {
        const reason = prompt(`Are you sure you want to delete ${typeLabel}: "${name}"?\n\nThis will move it to the Recycle Bin. Please specify a reason:`, "No longer needed");

        if (reason !== null) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="${formAction}" value="1">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="deletion_reason" value="${reason}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function viewPayslip(p) {
        document.getElementById('slip_emp_name').innerText = p.name;
        document.getElementById('slip_emp_email').innerText = p.email || 'N/A';
        document.getElementById('slip_month').innerText = p.salary_month;
        document.getElementById('slip_status').innerText = p.status;
        document.getElementById('slip_base').innerText = parseFloat(p.base_salary).toLocaleString() + ' ETB';
        document.getElementById('slip_bonus').innerText = '+' + parseFloat(p.bonus).toLocaleString() + ' ETB';
        document.getElementById('slip_deduct').innerText = '-' + parseFloat(p.deductions).toLocaleString() + ' ETB';
        document.getElementById('slip_net').innerText = parseFloat(p.net_salary).toLocaleString() + ' ETB';
        document.getElementById('slip_ot').innerText = '+' + parseFloat(p.overtime_amount).toLocaleString() + ' ETB';
        document.getElementById('slip_advance').innerText = '-' + parseFloat(p.advance_deduction).toLocaleString() + ' ETB';

        const statusEl = document.getElementById('slip_status');
        if (p.status === 'Paid') { statusEl.style.color = '#059669'; statusEl.innerHTML = '<i class="fa-solid fa-circle-check"></i> PAID'; }
        else { statusEl.style.color = '#3b82f6'; statusEl.innerHTML = '<i class="fa-solid fa-clock"></i> PENDING'; }
        document.getElementById('payslipModal').style.display = 'flex';
    }

    window.onclick = function (event) {
        if (event.target.classList.contains('modal-overlay')) { closeModals(); }
        if (!event.target.closest('.profile-container')) {
            const dropdown = document.getElementById('logoutDropdown');
            if (dropdown && dropdown.classList.contains('show')) { dropdown.classList.remove('show'); }
        }
    }

    // Preloader Logic
    window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            setTimeout(() => {
                preloader.classList.add('fade-out');
            }, 600);
        }
    });
</script>
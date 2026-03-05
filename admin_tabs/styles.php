<style>
    :root {
        --primary: #ff2d55;
        --accent: #ffee58;
        --cyan: #00e5ff;
        --blue: #0f172a;
        --bg: #f4f6fc;
        --surface: #ffffff;
        --text: #2c3e50;
        --border: #e9ecef;
        --sidebar: #0a192f;
        --sidebar-text: #e0f2f1;
    }

    /* Preloader Styles */
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, #ffffff, #f8fafc);
        z-index: 999999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.8s;
        gap: 60px;
    }

    .preloader.fade-out {
        opacity: 0;
        visibility: hidden;
    }

    .loader-container {
        position: relative;
        width: 120px;
        height: 120px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .loader-circle {
        width: 80px;
        height: 80px;
        border: 4px solid rgba(255, 45, 85, 0.1);
        border-top: 4px solid var(--primary);
        border-bottom: 4px solid var(--primary);
        border-radius: 50%;
        animation: rotateLoader 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
    }

    .loader-inner {
        position: absolute;
        width: 40px;
        height: 40px;
        border: 3px solid transparent;
        border-left: 3px solid rgba(255, 45, 85, 0.3);
        border-right: 3px solid rgba(255, 45, 85, 0.3);
        border-radius: 50%;
        animation: rotateLoader 1s linear infinite reverse;
        opacity: 0.8;
    }

    @keyframes rotateLoader {
        0% {
            transform: rotate(0deg) scale(1);
        }

        50% {
            transform: rotate(180deg) scale(1.15);
        }

        100% {
            transform: rotate(360deg) scale(1);
        }
    }

    .loader-text {
        position: absolute;
        bottom: -30px;
        font-size: 11px;
        letter-spacing: 5px;
        text-transform: uppercase;
        color: var(--primary);
        font-weight: 900;
        opacity: 0.9;
        animation: pulseText 2s ease-in-out infinite;
    }

    @keyframes pulseText {

        0%,
        100% {
            opacity: 0.4;
            transform: translateY(0);
        }

        50% {
            opacity: 1;
            transform: translateY(-3px);
        }
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        display: flex;
        height: 100vh;
        background: var(--bg);
        color: var(--text);
    }

    .sidebar {
        width: 260px;
        min-width: 260px;
        background: var(--sidebar);
        color: #fff;
        display: flex;
        flex-direction: column;
        padding: 30px 0;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }

    .brand {
        font-size: 26px;
        font-weight: 800;
        padding: 0 20px 40px;
        margin-bottom: 20px;
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-direction: column;
        text-align: center;
    }

    .brand-icon {
        background: rgba(255, 255, 255, 0.15);
        padding: 18px;
        border-radius: 16px;
        font-size: 32px;
        margin-bottom: 10px;
        color: #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
    }

    .nav-items {
        flex: 1;
        padding: 0 15px;
    }

    .nav-item {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
        margin-bottom: 8px;
        border-radius: 12px;
        font-size: 15px;
    }

    .nav-item i {
        width: 20px;
        text-align: center;
        font-size: 18px;
    }

    .nav-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        transform: translateX(5px);
    }

    .nav-item.active {
        background: var(--accent);
        color: var(--blue);
        transform: translateX(8px);
        box-shadow: 0 4px 15px rgba(255, 238, 88, 0.4);
    }

    .nav-badge {
        background: #ff2d55;
        color: white;
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 20px;
        font-weight: 800;
        margin-left: auto;
        box-shadow: 0 4px 10px rgba(255, 45, 85, 0.4);
        border: 2px solid var(--sidebar);
        animation: pulseBadge 2s infinite;
    }

    @keyframes pulseBadge {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Sidebar Dropdown Styles */
    .nav-dropdown {
        margin-bottom: 8px;
    }

    .nav-dropdown-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 700;
        border-radius: 12px;
        transition: 0.3s;
        font-size: 15px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-dropdown-toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .nav-dropdown-toggle i:last-child {
        font-size: 12px;
        transition: 0.3s;
    }

    .nav-dropdown.open .nav-dropdown-toggle i:last-child {
        transform: rotate(180deg);
    }

    .nav-dropdown.open .nav-dropdown-toggle {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .submenu {
        display: none;
        padding-left: 15px;
        margin-top: 5px;
        border-left: 2px solid rgba(255, 255, 255, 0.1);
        margin-left: 10px;
    }

    .nav-dropdown.open .submenu {
        display: block;
    }

    .submenu .nav-item {
        font-size: 14px;
        padding: 10px 15px;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .submenu .nav-item.active {
        transform: translateX(5px);
    }

    .main-content {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 25px;
        min-width: 0;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        padding: 20px 30px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.5);
        flex-wrap: wrap;
        gap: 15px;
    }

    .profile-container {
        position: relative;
    }

    .profile-clickarea {
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 12px;
        transition: 0.3s;
    }

    .profile-clickarea:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .logout-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 10px;
        background: var(--surface);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 10px;
        display: none;
        z-index: 1000;
        width: 160px;
        border: 1px solid var(--border);
        animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .logout-dropdown.show {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .logout-dropdown .nav-item {
        color: #1e293b !important;
        margin: 0 !important;
        padding: 12px 15px !important;
        border-radius: 0 !important;
        font-size: 14px !important;
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logout-dropdown .nav-item:hover {
        background: #f8fafc !important;
        color: var(--primary) !important;
        transform: none !important;
    }

    h1 {
        font-size: 28px;
        font-weight: 800;
        color: var(--blue);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--surface);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .stat-label {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
        margin-top: 5px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .card {
        background: var(--surface);
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        padding: 25px;
        margin-bottom: 30px;
        overflow-x: auto;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        cursor: move;
        user-select: none;
    }

    .card-title {
        font-weight: 700;
        font-size: 20px;
        color: #1e293b;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: -8px;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        font-size: 14px;
        background: var(--surface);
    }

    th {
        background: transparent;
        font-weight: 700;
        color: #0f172a;
        border: none;
        padding-bottom: 8px;
        font-size: 15px;
    }

    td {
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    td:first-child {
        border-left: 1px solid var(--border);
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    td:last-child {
        border-right: 1px solid var(--border);
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
        min-width: 80px;
    }

    .badge.pending {
        background: #1bc5e0;
        color: #ffffff;
    }

    .badge.confirmed,
    .badge.active,
    .badge.paid {
        background: #28a745;
        color: #ffffff;
    }

    .badge.rejected,
    .badge.closed,
    .badge.unpaid {
        background: #6c757d;
        color: #ffffff;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 20px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        font-size: 14px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }

    input,
    select,
    textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: var(--primary);
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
    }

    .modal-content {
        background: var(--surface);
        padding: 30px;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    .action-flex {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: 0.2s;
        font-size: 14px;
    }

    .btn-edit {
        background: #fff3cd;
        color: #856404;
    }

    .btn-view {
        background: #e0f2f1;
        color: #00796b;
    }

    .btn-delete {
        background: #f8d7da;
        color: #721c24;
    }

    .menu-row {
        background: #fff;
        margin-bottom: 10px;
        border-radius: 12px;
        transition: 0.3s;
        border: 1px solid #eee;
    }

    .menu-row:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    .item-cell {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
    }

    .item-img {
        width: 55px;
        height: 55px;
        border-radius: 10px;
        object-fit: cover;
        background: #f0f0f0;
    }

    .item-info {
        display: flex;
        flex-direction: column;
    }

    .item-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
    }

    .item-desc {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 250px;
    }

    .badge-cat {
        background: #f1f5f9;
        color: #475569;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        background: #dcfce7;
        color: #15803d;
    }

    .price-text {
        font-weight: 800;
        color: #0f172a;
    }

    .tax-text {
        color: #0ea5e9;
        font-weight: 600;
        font-size: 13px;
    }

    .action-btn-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid;
        background: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-circle-edit {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-circle-delete {
        border-color: #ef4444;
        color: #ef4444;
    }

    /* Redesigned Vertical ID Card */
    .id-card-modal {
        background: #fff;
        width: 380px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.5);
        position: relative;
        text-align: center;
        border: 2px solid #eee;
    }

    .id-card-top-accent {
        height: 140px;
        background: #ee1d23;
        position: relative;
        padding-top: 30px;
    }

    .id-card-top-accent::after {
        content: "";
        position: absolute;
        bottom: -60px;
        left: 0;
        width: 100%;
        height: 100px;
        background: #0054a6;
        clip-path: polygon(0 40%, 100% 0, 100% 100%, 0 100%);
    }

    .id-card-logo-area {
        position: relative;
        z-index: 2;
        color: #fff;
    }

    .id-card-logo-area h3 {
        margin: 0;
        font-weight: 900;
        letter-spacing: 2px;
        font-size: 22px;
    }

    .id-card-logo-area p {
        margin: 0;
        font-size: 10px;
        opacity: 0.9;
    }

    .id-card-body {
        padding: 0 30px 40px;
        position: relative;
        z-index: 3;
        margin-top: -60px;
        background: #fff;
    }

    .id-avatar-circle {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 6px solid #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        margin: 0 auto 20px;
        overflow: hidden;
        background: #f8fafc;
    }

    .id-avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .id-name-tag {
        font-size: 24px;
        font-weight: 900;
        color: #1a1a1a;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .id-role-tag {
        font-size: 14px;
        font-weight: 700;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 30px;
    }

    .id-details-list {
        text-align: left;
        margin-bottom: 40px;
        border-top: 1px solid #f1f5f9;
        padding-top: 20px;
    }

    .id-detail-item {
        display: flex;
        margin-bottom: 8px;
        font-size: 13px;
        color: #333;
    }

    .id-detail-label {
        width: 100px;
        font-weight: 800;
        color: #777;
    }

    .id-detail-value {
        font-weight: 700;
        color: #222;
        flex: 1;
    }

    .id-barcode-area {
        background: #f8fafc;
        padding: 15px;
        border-radius: 10px;
    }

    .payslip-modal {
        background: #fff;
        width: 100%;
        max-width: 600px;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        position: relative;
        color: #333;
    }

    .payslip-header {
        text-align: center;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }

    .payslip-title {
        font-size: 24px;
        font-weight: 800;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .payslip-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
    }

    .info-value {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
    }

    .earnings-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .total-row {
        background: #1e293b;
        color: #fff !important;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .id-card-printable,
        .id-card-printable *,
        #payslipModal,
        #payslipModal * {
            visibility: visible;
        }

        .id-card-printable {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        #payslipModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }

        .no-print {
            display: none !important;
        }
    }

    /* Computer View Optimization (100% Zoom) */
    @media (max-width: 1440px) {
        .sidebar {
            width: 240px;
            min-width: 240px;
        }

        .main-content {
            padding: 20px;
        }
    }

    @media (max-width: 1200px) {
        .sidebar {
            width: 220px;
            min-width: 220px;
        }

        header {
            padding: 15px 20px;
            margin-bottom: 25px;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }
    }

    @media (max-width: 1024px) {
        body {
            flex-direction: column;
            height: auto;
        }

        .sidebar {
            width: 100% !important;
            min-width: 100% !important;
            height: auto !important;
            padding: 10px 0 !important;
        }

        .brand {
            padding-bottom: 10px;
            margin-bottom: 5px;
            flex-direction: row;
            justify-content: center;
            font-size: 20px;
        }

        .brand-icon {
            padding: 8px;
            font-size: 18px;
            margin-bottom: 0;
            margin-right: 10px;
        }

        .nav-items {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            padding: 0 10px;
        }

        .nav-item {
            padding: 8px 12px;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .main-content {
            padding: 15px;
            width: 100%;
        }
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
</style>
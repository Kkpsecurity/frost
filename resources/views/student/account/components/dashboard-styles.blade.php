{{-- Account Dashboard Styles Component --}}
<style>
    /* Modern Account Dashboard Styling */
    main {
        background-color: var(--frost-secondary-color) !important;
        min-height: 100vh;
        overflow-y: hidden;
    }

    .account-dashboard {
        display: flex;
        min-height: calc(100vh - 140px);
        background: var(--frost-secondary-color, #394867);
    }

    /* Sidebar Navigation */
    .account-sidebar {
        width: 300px;
        background: var(--frost-primary-color);
        color: white;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
        min-height: 100%;
        position: sticky;
        top: 0;
        padding: 0;
    }

    .sidebar-header {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        background: var(--frost-primary-color);
    }

    .sidebar-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 1rem;
    }



    .profile-avatar-section {
        text-align: center;
        position: relative;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        margin-bottom: 1.5rem;
    }


    .account-dashboard .account-sidebar .sidebar-header .sidebar-avatar i {
        font-size: 2rem;
        color: rgba(54, 54, 54, 0.7);
    }

    .sidebar-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.25rem;
    }

    .sidebar-role {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .profile-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ebf3ff;
        margin-bottom: 0.5rem;
    }

    .profile-role {
        color: #f8fbff;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }

    /* Navigation Links */
    .sidebar-nav {
        list-style: none;
        padding: 1rem 0;
        margin: 0;
    }

    .nav-item {
        margin-bottom: 0;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.5rem;
        color: #cbd5e1;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        position: relative;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: rgba(255, 255, 255, 0.3);
    }

    .nav-link.active {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: #10b981;
        position: relative;
    }

    .nav-link.active::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #10b981;
    }

    .nav-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    /* Main Content Area */
    .account-content {
        flex: 1;
        background: linear-gradient(135deg, var(--frost-secondary-color, #394867) 0%, #394867 20%);
        min-height: 100%;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .content-header {
        background: linear-gradient(135deg, rgba(189, 199, 255, 0.493) 0%, rgba(189, 199, 255, 0.493) 100%);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        padding: 2rem;
        margin-bottom: 0;
    }

    .content-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #1e293b;
    }

    .content-description {
        color: #1e2530;
        font-size: 1rem;
        margin: 0;
    }

    .content-body {
        padding: 2rem;
    }


    /* Modern Cards */
    .modern-card {
        background: #c5c5c5;
        border-radius: var(--frost-radius-xl);
        border: 1px solid var(--frost-light-primary-color);
        box-shadow: var(--frost-shadow-md);
        transition: var(--frost-transition-base);
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .modern-card .card-body {
        padding: var(--frost-space-xl);
        background: linear-gradient(135deg, rgba(189, 199, 255, 0.493) 0%, rgba(0, 33, 66, 0.95) 100%);
        backdrop-filter: blur(10px);
        color: #1e293b;
    }

    /* Bootstrap card styling - lighter for better readability */
    .card {
        background: linear-gradient(135deg, rgba(189, 199, 255, 0.493) 0%, rgba(0, 33, 66, 0.95) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: var(--frost-radius-lg);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }

    .card-body {
        background: rgba(255, 255, 255, 0.95);
        color: #1e293b;
    }



    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 4px;
    }

    .status-badge.verified {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.action {
        background: #dbeafe;
        color: #1d4ed8;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-badge.action:hover {
        background: #bfdbfe;
        transform: translateY(-1px);
    }


    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        color: #dddddd;
        border-bottom: 1px solid #f1f5f9;
    }

    .summary-item:last-child {
        border-bottom: 0;
    }

    .summary-label {
        color: #64748b;
        font-weight: 500;
    }

    .summary-value {
        color: #1e293b;
        font-weight: 600;
    }

    .summary-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .summary-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .summary-badge.count {
        background: var(--frost-primary-color, #212a3e);
        color: white;
    }

    /* Form Elements */
    .form-section {
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);
        color: white;
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 500;
        transition: all 0.2s ease;
        background: #f8fafc;
    }

    .form-control:focus {
        border-color: var(--frost-primary-color, #212a3e);
        box-shadow: 0 0 0 3px rgba(33, 42, 62, 0.1);
        background: white;
    }

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    /* Modern Buttons */
    .btn-modern {
        padding: 12px 32px;
        border-radius: 12px;
        font-weight: 600;
        border: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(33, 42, 62, 0.3);
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(33, 42, 62, 0.4);
        color: white;
    }



    /* Tab Content */
    .tab-content {
        min-height: 500px;
    }


    /* Statistics and Content Styling */
    .stat-item {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    }

    .stat-number {
        color: var(--frost-primary-color);
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #64748b;
        font-weight: 500;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }


    /* Text Colors */
    .content-title {
        color: white;
    }

    .content-description {
        color: rgba(255, 255, 255, 0.8);
    }

    .section-title {
        color: #1e293b;
        font-weight: 600;
    }

    .card-body h1,
    .card-body h2,
    .card-body h3,
    .card-body h4,
    .card-body h5,
    .card-body h6 {
        color: #1e293b;
    }

    .card-body p,
    .card-body span,
    .card-body div {
        color: #374151;
    }

    .card-body .text-muted {
        color: #6b7280;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .account-dashboard {
            flex-direction: column;
        }

        .account-sidebar {
            width: 100%;
            position: relative;
            top: 0;
            min-height: auto;
        }

        .sidebar-nav {
            display: flex;
            overflow-x: auto;
            padding: 1rem 0;
        }

        .nav-item {
            flex-shrink: 0;
        }

        .nav-link {
            white-space: nowrap;
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 1rem 1.5rem;
        }

        .nav-link.active {
            border-left: none;
            border-bottom-color: #10b981;
        }

        .nav-link.active::before {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .account-dashboard {
            min-height: calc(100vh - 120px);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
        }

        .sidebar-avatar {
            width: 50px;
            height: 50px;
            margin-bottom: 0;
        }

        .content-header {
            padding: 1.5rem;
        }

        .content-title {
            font-size: 1.5rem;
        }

        .content-body {
            padding: 1.5rem;
        }

        .modern-card .card-body {
            padding: 1.5rem;
        }
    }
</style>


{{--

 --}}

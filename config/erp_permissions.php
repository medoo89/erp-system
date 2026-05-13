<?php

return [
    'areas' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'group' => 'General',
            'actions' => [
                'view' => 'View Dashboard',
            ],
        ],

        'jobs' => [
            'label' => 'Job Openings',
            'group' => 'Recruitment',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'publish' => 'Publish',
                'close' => 'Close',
                'manage_form' => 'Manage Application Form',
                'view_applications' => 'View Applications',
            ],
        ],

        'job_applications' => [
            'label' => 'Job Applications',
            'group' => 'Recruitment',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'reject' => 'Reject',
                'archive' => 'Archive',
                'send_email' => 'Send Email',
                'move_pre_employment' => 'Move to Pre-Employment',
                'screening' => 'Screening / Status Updates',
                'hire' => 'Hire / Create Pre-Employment',
                'decline' => 'Decline',
                'create_request' => 'Create Candidate Request',
                'delete_request' => 'Delete Candidate Request',
                'export' => 'Export',
            ],
        ],

        'pre_employments' => [
            'label' => 'Pre-Employment',
            'group' => 'Recruitment / HR',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'archive' => 'Archive',
                'send_request' => 'Send Candidate Request',
                'send_email' => 'Send Email',
                'upload_file' => 'Upload File',
                'move_employment' => 'Move to Employment',
            ],
        ],

        'employments' => [
            'label' => 'Employments',
            'group' => 'HR',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit Profile',
                'delete' => 'Delete',
                'archive' => 'Archive',

                'upload_file' => 'Upload File',
                'request_file' => 'Request File',
                'delete_file' => 'Delete File',
                'send_email' => 'Send Email',

                'portal_send_password' => 'Send Portal Password',
                'portal_reset_password' => 'Reset Portal Password',
                'portal_enable' => 'Enable Portal',
                'portal_disable' => 'Disable Portal',
                'erp_login_open_page_rules' => 'Open Page Rules',
                'erp_login_disable' => 'Disable ERP User',
                'erp_login_enable' => 'Enable ERP User',
                'erp_login_create_update' => 'Create / Update ERP User',
                'erp_login_view' => 'View ERP Login Setup',
                'portal_preview' => 'Open Portal Preview',

                'rotation_add' => 'Add Rotation',
                'rotation_edit' => 'Edit Rotation',
                'rotation_delete' => 'Delete Rotation',
                'rotation_print' => 'Print Rotation History',

                'finance_profile_view' => 'View Finance Profile',
                'finance_profile_edit' => 'Edit Finance Profile',

                'add_expense' => 'Add Expense',
                'generate_salary_slip' => 'Generate Salary Slip',
                'print_profile' => 'Print Profile',
            ],
        ],

        'salary_slips' => [
            'label' => 'Salary Slips',
            'group' => 'Finance',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'back_to_draft' => 'Back To Draft',
                'send_to_bank' => 'Send To Bank',
                'mark_paid' => 'Mark Paid',
                'process_cash' => 'Process Cash Payment',
                'upload_attachment' => 'Upload Attachment',
                'print' => 'Print',
                'send_email' => 'Send Email',
                'update_attendance' => 'Update Attendance Days',
                'process_payment' => 'Process Payment',
                'update_attendance_days' => 'Update Attendance Days',
            ],
        ],

        'finance_expenses' => [
            'label' => 'Finance Expenses',
            'group' => 'Finance',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'post' => 'Post',
                'upload_attachment' => 'Upload Attachment',
                'cancel' => 'Cancel',
                'back_to_draft' => 'Back To Draft',
                'reopen' => 'Reopen',
                'process_payment' => 'Process Payment',
                'mark_paid' => 'Mark Paid',
                'view_treasury_posting' => 'View Treasury Posting',
            ],
        ],

        'treasury' => [
            'label' => 'Treasury',
            'group' => 'Finance',
            'actions' => [
                'view' => 'View',
                'create_account' => 'Create Account',
                'edit_account' => 'Edit Account',
                'delete_account' => 'Delete Account',
                'transfer' => 'Transfer',
                'receive' => 'Receive',
                'pay' => 'Pay',
                'reconcile' => 'Reconcile',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'view_totals' => 'View Global Totals',
                'view_clearing' => 'View Clearing Monitor',
            ],
        ],

        'client_invoices' => [
            'label' => 'Client Invoices',
            'group' => 'Finance',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'approve' => 'Approve',
                'send_to_client' => 'Send To Client',
                'record_payment' => 'Record Payment',
                'print' => 'Print',
                'cancel' => 'Cancel',
                'back_to_draft' => 'Back To Draft',
                'reopen' => 'Reopen',
                'settle_receipts' => 'Settle Pending Receipts',
            ],
        ],

        'clients' => [
            'label' => 'Clients',
            'group' => 'Operations',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'finance_view' => 'View Finance',
                'create_project' => 'Create Project',
            ],
        ],

        'projects' => [
            'label' => 'Projects',
            'group' => 'Operations',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'finance_view' => 'View Finance',
                'salary_view' => 'View Salary',
                'generate_salary_slips' => 'Generate Salary Slips',
                'generate_invoice' => 'Generate Invoice',
                'contract_terms' => 'Manage Contract Terms',
            ],
        ],

        'travel_tickets' => [
            'label' => 'Travel & Tickets',
            'group' => 'Operations',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'edit' => 'Edit',
                'delete' => 'Delete',
                'upload_ticket' => 'Upload Ticket',
                'upload_travel_request' => 'Upload Travel Request',
                'open_file' => 'Open File',
            ],
        ],

        'archive' => [
            'label' => 'Archive',
            'group' => 'Admin',
            'actions' => [
                'view' => 'View',
                'restore' => 'Restore',
                'delete' => 'Delete Permanently',
            ],
        ],

        'access_control' => [
            'label' => 'Page Rules',
            'group' => 'Admin',
            'actions' => [
                'view' => 'View',
                'create_user' => 'Create User',
                'edit_user' => 'Edit User',
                'delete_user' => 'Delete User',
                'reset_password' => 'Reset Password',
                'manage_permissions' => 'Manage Permissions',
            ],
        ],

        'audit_logs' => [
            'label' => 'Audit Logs',
            'group' => 'Admin',
            'actions' => [
                'view' => 'View',
                'export' => 'Export',
            ],
        ],
    ],
];

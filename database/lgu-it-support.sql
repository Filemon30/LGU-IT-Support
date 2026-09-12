
CREATE DATABASE IF NOT EXISTS `lgu-it-support`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `lgu-it-support`;


/* =========================================================
   2. ROLES
   ========================================================= */

CREATE TABLE roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name ENUM('Admin', 'Staff') NOT NULL UNIQUE
) ENGINE=InnoDB;


/* =========================================================
   3. USER INFORMATION
   ========================================================= */

CREATE TABLE user_informations (
    user_info_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    suffix VARCHAR(20),

    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    birth_date DATE NOT NULL,

    barangay VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    province VARCHAR(255) NOT NULL,

    contact_number VARCHAR(20) NOT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


/* =========================================================
   4. USER ACCOUNTS
   ========================================================= */

CREATE TABLE user_accounts (
    user_acc_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,

    last_login_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB;


/* =========================================================
   5. SYSTEM USERS
   ========================================================= */

CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    staff_ref_num VARCHAR(50) NOT NULL UNIQUE,

    role_id INT UNSIGNED NOT NULL,
    user_info_id INT UNSIGNED NOT NULL,
    user_acc_id INT UNSIGNED NOT NULL,

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id)
        REFERENCES roles(role_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_users_information
        FOREIGN KEY (user_info_id)
        REFERENCES user_informations(user_info_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_users_account
        FOREIGN KEY (user_acc_id)
        REFERENCES user_accounts(user_acc_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uq_users_user_info (user_info_id),
    UNIQUE KEY uq_users_user_account (user_acc_id),

    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status)

) ENGINE=InnoDB;


/* =========================================================
   6. BARANGAYS
   ========================================================= */

CREATE TABLE barangays (
    barangay_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    barangay_ref_num VARCHAR(50) NOT NULL UNIQUE,
    barangay_name VARCHAR(255) NOT NULL UNIQUE,

    /*
       Store a HASH of the secret key instead of the
       actual secret key.
    */
    secret_key_hash VARCHAR(255) NOT NULL,

    key_status ENUM(
        'Active',
        'Disabled'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_barangay_key_status (key_status)

) ENGINE=InnoDB;


/* =========================================================
   7. OFFICES
   ========================================================= */

CREATE TABLE offices (
    office_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    office_ref_num VARCHAR(50) NOT NULL UNIQUE,
    office_name VARCHAR(255) NOT NULL UNIQUE,

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_offices_status (status)

) ENGINE=InnoDB;


/* =========================================================
   8. DIVISIONS
   =========================================================
   
   A division belongs to one office.

   Office
      |
      +--- Division A
      +--- Division B
      +--- Division C
   ========================================================= */

CREATE TABLE divisions (
    division_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    office_id INT UNSIGNED NOT NULL,

    division_ref_num VARCHAR(50) NOT NULL UNIQUE,
    division_name VARCHAR(255) NOT NULL,

    secret_key_hash VARCHAR(255) NOT NULL,

    key_status ENUM(
        'Active',
        'Disabled'
    ) NOT NULL DEFAULT 'Active',

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_divisions_office
        FOREIGN KEY (office_id)
        REFERENCES offices(office_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uq_division_office_name (
        office_id,
        division_name
    ),

    INDEX idx_divisions_office (office_id),
    INDEX idx_divisions_status (status),
    INDEX idx_divisions_key_status (key_status)

) ENGINE=InnoDB;


/* =========================================================
   9. REQUESTERS
   =========================================================
   
   A requester can be:

      Barangay
      OR
      Office Division

   This solves the original problem where requester_id
   attempted to reference two different tables.
   ========================================================= */

CREATE TABLE requesters (
    requester_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    requester_type ENUM(
        'Barangay',
        'Office Division'
    ) NOT NULL,

    barangay_id INT UNSIGNED NULL,
    division_id INT UNSIGNED NULL,

    status ENUM(
        'Active',
        'Disabled'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_requesters_barangay
        FOREIGN KEY (barangay_id)
        REFERENCES barangays(barangay_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_requesters_division
        FOREIGN KEY (division_id)
        REFERENCES divisions(division_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uq_requester_barangay (barangay_id),
    UNIQUE KEY uq_requester_division (division_id),

    INDEX idx_requester_type (requester_type),
    INDEX idx_requester_status (status)

) ENGINE=InnoDB;


/* =========================================================
   10. PRIORITY LEVELS
   ========================================================= */

CREATE TABLE priority_levels (
    priority_level_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    priority_name ENUM(
        'Critical',
        'High',
        'Medium',
        'Low'
    ) NOT NULL UNIQUE,

    priority_description VARCHAR(500),

    response_target_minutes INT UNSIGNED NULL,
    resolution_target_minutes INT UNSIGNED NULL

) ENGINE=InnoDB;


/* =========================================================
   11. SERVICES
   =========================================================
   
   Added because your original admin transaction included
   "New Service Added" but there was no services table.
   ========================================================= */

CREATE TABLE services (
    service_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    service_ref_num VARCHAR(50) NOT NULL UNIQUE,
    service_name VARCHAR(255) NOT NULL UNIQUE,

    description TEXT,

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_services_status (status)

) ENGINE=InnoDB;


/* =========================================================
   12. CATEGORIES
   ========================================================= */

CREATE TABLE categories (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    service_id INT UNSIGNED NOT NULL,

    category_name VARCHAR(255) NOT NULL,
    description TEXT,

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_categories_service
        FOREIGN KEY (service_id)
        REFERENCES services(service_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    UNIQUE KEY uq_category_service_name (
        service_id,
        category_name
    ),

    INDEX idx_categories_service (service_id),
    INDEX idx_categories_status (status)

) ENGINE=InnoDB;


/* =========================================================
   13. ISSUES
   ========================================================= */

CREATE TABLE issues (
    issue_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_id INT UNSIGNED NOT NULL,

    issue_ref_num VARCHAR(50) NOT NULL UNIQUE,
    issue_name VARCHAR(255) NOT NULL,

    description TEXT,

    /*
       Default priority for this issue.
       Actual ticket priority can still override it.
    */
    default_priority_level_id INT UNSIGNED NULL,

    status ENUM(
        'Active',
        'Disabled',
        'Archived'
    ) NOT NULL DEFAULT 'Active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_issues_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_issues_default_priority
        FOREIGN KEY (default_priority_level_id)
        REFERENCES priority_levels(priority_level_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    UNIQUE KEY uq_issue_category_name (
        category_id,
        issue_name
    ),

    INDEX idx_issues_category (category_id),
    INDEX idx_issues_priority (default_priority_level_id),
    INDEX idx_issues_status (status)

) ENGINE=InnoDB;


/* =========================================================
   14. KNOWLEDGE BASE
   ========================================================= */

CREATE TABLE knowledge_base (
    knowledge_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    category_id INT UNSIGNED NOT NULL,

    knowledge_ref_num VARCHAR(50) NOT NULL UNIQUE,
    knowledge_title VARCHAR(255) NOT NULL,
    knowledge_description TEXT NOT NULL,

    created_by INT UNSIGNED NOT NULL,
    updated_by INT UNSIGNED NULL,

    status ENUM(
        'Draft',
        'Published',
        'Archived'
    ) NOT NULL DEFAULT 'Draft',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_knowledge_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_knowledge_created_by
        FOREIGN KEY (created_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_knowledge_updated_by
        FOREIGN KEY (updated_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_knowledge_category (category_id),
    INDEX idx_knowledge_status (status),
    INDEX idx_knowledge_created_by (created_by)

) ENGINE=InnoDB;


/* =========================================================
   15. TICKETS
   ========================================================= */

CREATE TABLE tickets (
    ticket_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ticket_ref_num VARCHAR(50) NOT NULL UNIQUE,

    requester_id INT UNSIGNED NOT NULL,
    issue_id INT UNSIGNED NOT NULL,

    description TEXT NOT NULL,

    /*
       Actual priority of this ticket.
       This can differ from the issue's default priority.
    */
    priority_level_id INT UNSIGNED NOT NULL,

    /*
       Current assigned staff member.
    */
    assigned_to INT UNSIGNED NULL,

    ticket_status ENUM(
        'Pending',
        'Confirmed',
        'On Progress',
        'Cancelled',
        'Resolved',
        'Request Reassignment'
    ) NOT NULL DEFAULT 'Pending',

    resolved_at DATETIME NULL,
    cancelled_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tickets_requester
        FOREIGN KEY (requester_id)
        REFERENCES requesters(requester_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_tickets_issue
        FOREIGN KEY (issue_id)
        REFERENCES issues(issue_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_tickets_priority
        FOREIGN KEY (priority_level_id)
        REFERENCES priority_levels(priority_level_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_tickets_assigned_to
        FOREIGN KEY (assigned_to)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_tickets_requester (requester_id),
    INDEX idx_tickets_issue (issue_id),
    INDEX idx_tickets_priority (priority_level_id),
    INDEX idx_tickets_assigned_to (assigned_to),
    INDEX idx_tickets_status (ticket_status),
    INDEX idx_tickets_created_at (created_at)

) ENGINE=InnoDB;


/* =========================================================
   16. TICKET ASSIGNMENTS
   =========================================================
   
   Keeps the complete assignment/reassignment history.
   ========================================================= */

CREATE TABLE ticket_assignments (
    assignment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ticket_id INT UNSIGNED NOT NULL,

    assigned_to INT UNSIGNED NOT NULL,
    assigned_by INT UNSIGNED NOT NULL,

    assignment_type ENUM(
        'Initial Assignment',
        'Reassignment'
    ) NOT NULL DEFAULT 'Initial Assignment',

    remarks TEXT,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_assignments_ticket
        FOREIGN KEY (ticket_id)
        REFERENCES tickets(ticket_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_assignments_assigned_to
        FOREIGN KEY (assigned_to)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_assignments_assigned_by
        FOREIGN KEY (assigned_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_assignments_ticket (ticket_id),
    INDEX idx_assignments_assigned_to (assigned_to),
    INDEX idx_assignments_created_at (created_at)

) ENGINE=InnoDB;


/* =========================================================
   17. TICKET STATUS HISTORY
   ========================================================= */

CREATE TABLE ticket_status_history (
    history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ticket_id INT UNSIGNED NOT NULL,

    old_status ENUM(
        'Pending',
        'Confirmed',
        'On Progress',
        'Cancelled',
        'Resolved',
        'Request Reassignment'
    ) NULL,

    new_status ENUM(
        'Pending',
        'Confirmed',
        'On Progress',
        'Cancelled',
        'Resolved',
        'Request Reassignment'
    ) NOT NULL,

    changed_by INT UNSIGNED NOT NULL,

    remarks TEXT,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_status_history_ticket
        FOREIGN KEY (ticket_id)
        REFERENCES tickets(ticket_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_status_history_user
        FOREIGN KEY (changed_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_status_history_ticket (ticket_id),
    INDEX idx_status_history_user (changed_by),
    INDEX idx_status_history_created_at (created_at)

) ENGINE=InnoDB;


/* =========================================================
   18. TICKET COMMENTS
   ========================================================= */

CREATE TABLE ticket_comments (
    comment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ticket_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,

    comment TEXT NOT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_ticket_comments_ticket
        FOREIGN KEY (ticket_id)
        REFERENCES tickets(ticket_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_ticket_comments_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_ticket_comments_ticket (ticket_id),
    INDEX idx_ticket_comments_user (user_id),
    INDEX idx_ticket_comments_created_at (created_at)

) ENGINE=InnoDB;


/* =========================================================
   19. TICKET ATTACHMENTS
   ========================================================= */

CREATE TABLE ticket_attachments (
    attachment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    ticket_id INT UNSIGNED NOT NULL,
    uploaded_by INT UNSIGNED NOT NULL,

    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100),
    file_size BIGINT UNSIGNED,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_attachments_ticket
        FOREIGN KEY (ticket_id)
        REFERENCES tickets(ticket_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_attachments_user
        FOREIGN KEY (uploaded_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_attachments_ticket (ticket_id),
    INDEX idx_attachments_uploaded_by (uploaded_by)

) ENGINE=InnoDB;


/* =========================================================
   20. ADMIN AUDIT TRANSACTIONS
   =========================================================
   
   IMPORTANT:
   entity_id is intentionally NOT a foreign key.

   An audit transaction can refer to:
       User
       Ticket
       Barangay
       Office
       Division
       Service
       Category
       Issue
       Knowledge Base

   entity_type tells the application which table entity_id
   belongs to.
   ========================================================= */

CREATE TABLE admin_transactions (
    admin_transaction_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    admin_transaction_ref VARCHAR(50) NOT NULL UNIQUE,

    handled_by INT UNSIGNED NOT NULL,

    action_type ENUM(
        'New Staff Added',
        'Staff Password Updated',
        'Staff Status Updated',
        'Staff Deleted',

        'Re-assigned Ticket',
        'Ticket Updated',

        'New Barangay Added',
        'Activated Barangay Secret Key',
        'Disabled Barangay Secret Key',

        'New Office Added',
        'Updated Office',
        'Disabled Office',
        'Archived Office',

        'New Division Added',
        'Updated Division',
        'Activated Division Secret Key',
        'Disabled Division Secret Key',

        'New Service Added',
        'Updated Service',
        'Deleted Service',

        'New Category Added',
        'Updated Category',
        'Deleted Category',

        'New Knowledge Base Added',
        'Knowledge Base Updated',
        'Knowledge Base Deleted',

        'New Issue Added',
        'Updated Issue',
        'Deleted Issue'
    ) NOT NULL,

    entity_type ENUM(
        'User',
        'Ticket',
        'Barangay',
        'Office',
        'Division',
        'Service',
        'Category',
        'Issue',
        'Knowledge Base'
    ) NOT NULL,

    entity_id INT UNSIGNED NOT NULL,

    description TEXT,

    /*
       Optional snapshots of what changed.
       Useful for auditing.
    */
    old_values JSON NULL,
    new_values JSON NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_admin_transactions_user
        FOREIGN KEY (handled_by)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_admin_transactions_user (handled_by),
    INDEX idx_admin_transactions_action (action_type),
    INDEX idx_admin_transactions_entity (
        entity_type,
        entity_id
    ),
    INDEX idx_admin_transactions_created_at (created_at)

) ENGINE=InnoDB;


/* =========================================================
   21. NOTIFICATIONS
   ========================================================= */

CREATE TABLE notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    notification_ref_num VARCHAR(50) NOT NULL UNIQUE,

    user_id INT UNSIGNED NOT NULL,

    notification_message TEXT NOT NULL,

    notification_status ENUM(
        'Unread',
        'Read'
    ) NOT NULL DEFAULT 'Unread',

    read_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_status (notification_status),
    INDEX idx_notifications_created_at (created_at)

) ENGINE=InnoDB;
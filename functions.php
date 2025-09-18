<?php
// Helper functions for session management and role checking
session_start();

/**
 * Determine if the current visitor is logged in.
 *
 * @return bool
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to the login page if the user is not authenticated.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /procurement_system/index.php');
        exit;
    }
}

/**
 * Verify that the current user possesses one of the allowed roles.
 * If not, redirect them to the login page.
 *
 * @param array $roles Array of permitted role strings
 */
function check_role(array $roles): void
{
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header('Location: /procurement_system/index.php');
        exit;
    }
}

/**
 * Redirect a logged-in user to their appropriate dashboard based on role.
 */
function redirect_to_dashboard(): void
{
    if (!is_logged_in()) {
        return;
    }
    $role = $_SESSION['role'] ?? '';
    switch ($role) {
        case 'Admin':
            header('Location: /procurement_system/admin/index.php');
            break;
        case 'Employee':
            header('Location: /procurement_system/employee/index.php');
            break;
        case 'DeptHead':
            header('Location: /procurement_system/dept_head/index.php');
            break;
        case 'Purchasing':
            header('Location: /procurement_system/purchasing/index.php');
            break;
        case 'PurchasingHead':
            header('Location: /procurement_system/purchasing_head/index.php');
            break;
        case 'Seller':
            header('Location: /procurement_system/seller/index.php');
            break;
        default:
            header('Location: /procurement_system/index.php');
    }
    exit;
}

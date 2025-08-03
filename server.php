<?php
/**
 * Simple PHP Development Server for Maharashtra Police Duty Management System
 * This file provides a basic routing system for development purposes
 */

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove query string
$uri = strtok($uri, '?');

// Basic routing for our application
switch ($uri) {
    case '/':
        include 'app/Views/home/index.php';
        break;
        
    case '/admin/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simple admin authentication
            if ($_POST['username'] === 'admin' && $_POST['password'] === 'admin123') {
                session_start();
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = 'admin';
                $_SESSION['admin_full_name'] = 'System Administrator';
                header('Location: /admin/dashboard');
                exit;
            } else {
                $error = 'चुकीचे वापरकर्ता नाव किंवा पासवर्ड';
                include 'app/Views/admin/auth/login.php';
            }
        } else {
            include 'app/Views/admin/auth/login.php';
        }
        break;
        
    case '/admin/dashboard':
        session_start();
        if (!isset($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
        
        // Mock data for dashboard
        $title = 'प्रशासक डॅशबोर्ड';
        $total_officers = 25;
        $total_points = 12;
        $total_duties = 45;
        $active_duties = 8;
        $todays_duties = [];
        $low_compliance_alerts = [];
        $compliance_summary = [];
        
        include 'app/Views/admin/dashboard/index.php';
        break;
        
    case '/admin/logout':
        session_start();
        session_destroy();
        header('Location: /admin/login');
        exit;
        break;
        
    case '/officer/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simple officer authentication
            if ($_POST['badge_no'] === 'MH001' && $_POST['password'] === 'admin123') {
                session_start();
                $_SESSION['officer_logged_in'] = true;
                $_SESSION['officer_id'] = 1;
                $_SESSION['officer_badge_no'] = 'MH001';
                $_SESSION['officer_name'] = 'राजेश शर्मा';
                $_SESSION['officer_rank'] = 'Police Constable';
                $_SESSION['officer_station'] = 'Pune City Police';
                header('Location: /officer/dashboard');
                exit;
            } else {
                $error = 'चुकीचा बॅज नंबर किंवा पासवर्ड';
                include 'app/Views/officer/auth/login.php';
            }
        } else {
            include 'app/Views/officer/auth/login.php';
        }
        break;
        
    case '/officer/dashboard':
        session_start();
        if (!isset($_SESSION['officer_logged_in'])) {
            header('Location: /officer/login');
            exit;
        }
        
        // Mock data for officer dashboard
        $title = 'अधिकारी डॅशबोर्ड';
        $officer_name = $_SESSION['officer_name'];
        $officer_badge = $_SESSION['officer_badge_no'];
        $officer_rank = $_SESSION['officer_rank'];
        $officer_station = $_SESSION['officer_station'];
        $active_duty = null; // No active duty for demo
        $recent_duties = [];
        $compliance_history = [];
        $average_compliance = null;
        
        include 'app/Views/officer/dashboard/index.php';
        break;
        
    case '/officer/logout':
        session_start();
        session_destroy();
        header('Location: /officer/login');
        exit;
        break;
        
    default:
        // Check if it's a static file
        if (file_exists('.' . $uri)) {
            return false; // Let PHP serve the file
        }
        
        // 404 Not Found
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
        echo '<a href="/">Go to Home</a>';
        break;
}
?>

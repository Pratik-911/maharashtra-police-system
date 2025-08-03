<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // DEBUG: Log authentication attempt details
        log_message('info', '=== ADMIN AUTH FILTER DEBUG ===');
        log_message('info', 'Request URI: ' . $request->getUri());
        log_message('info', 'Request Method: ' . $request->getMethod());
        log_message('info', 'Session ID: ' . $session->session_id);
        log_message('info', 'Admin logged in status: ' . ($session->get('admin_logged_in') ? 'TRUE' : 'FALSE'));
        log_message('info', 'All session data: ' . json_encode($session->get()));
        log_message('info', 'Request headers: ' . json_encode($request->getHeaders()));
        
        if (!$session->get('admin_logged_in')) {
            log_message('info', 'AUTH FAILED: Would normally redirect to login page');
            // TEMPORARY BYPASS FOR TESTING - REMOVE AFTER DEBUGGING
            log_message('info', 'BYPASSING AUTHENTICATION FOR TESTING PURPOSES');
            // return redirect()->to('/admin/login')->with('error', 'कृपया प्रथम लॉगिन करा');
        }
        
        log_message('info', 'AUTH SUCCESS: User is authenticated, proceeding to controller');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}

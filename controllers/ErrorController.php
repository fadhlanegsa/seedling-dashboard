<?php
/**
 * Error Controller
 * Handles error pages
 */

require_once CORE_PATH . 'Controller.php';

class ErrorController extends Controller {
    
    /**
     * 404 Not Found page
     */
    public function notFound() {
        http_response_code(404);
        
        $data = [
            'title' => '404 - Halaman Tidak Ditemukan'
        ];
        
        $this->render('errors/404', $data, 'error');
    }
    
    /**
     * 500 Internal Server Error page
     */
    public function serverError() {
        http_response_code(500);
        
        $data = [
            'title' => '500 - Terjadi Kesalahan Server'
        ];
        
        $this->render('errors/500', $data, 'error');
    }
    
    /**
     * 403 Forbidden page
     */
    public function forbidden() {
        http_response_code(403);
        
        $data = [
            'title' => '403 - Akses Ditolak'
        ];
        
        $this->render('errors/403', $data, 'error');
    }
}

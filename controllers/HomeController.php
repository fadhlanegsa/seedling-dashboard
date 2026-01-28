<?php
/**
 * Home Controller
 * Handles public pages
 */

require_once CORE_PATH . 'Controller.php';

class HomeController extends Controller {
    
    /**
     * Landing page
     */
    public function index() {
        $bpdasModel = $this->model('BPDAS');
        $seedlingTypeModel = $this->model('SeedlingType');
        $provinceModel = $this->model('Province');
        
        // Get statistics
        $stats = $bpdasModel->getStatistics();
        
        // Get provinces for dropdown
        $provinces = $provinceModel->getAllOrdered();
        
        // Get seedling types for search
        $seedlingTypes = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Dashboard Stok Bibit Persemaian Indonesia',
            'stats' => $stats,
            'provinces' => $provinces,
            'seedlingTypes' => $seedlingTypes
        ];
        
        $this->render('public/landing', $data, 'public');
    }
    
    /**
     * Search results page
     */
    public function search() {
        $bpdasModel = $this->model('BPDAS');
        $provinceModel = $this->model('Province');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        // Get search filters
        $filters = [
            'province_id' => $this->get('province_id'),
            'seedling_type_id' => $this->get('seedling_type_id'),
            'min_stock' => $this->get('min_stock')
        ];
        
        $page = $this->get('page', 1);
        
        // Search BPDAS
        $results = $bpdasModel->search($filters, $page);
        
        // Get provinces and seedling types for filters
        $provinces = $provinceModel->getAllOrdered();
        $seedlingTypes = $seedlingTypeModel->getAllActive();
        
        $data = [
            'title' => 'Hasil Pencarian BPDAS',
            'results' => $results,
            'filters' => $filters,
            'provinces' => $provinces,
            'seedlingTypes' => $seedlingTypes
        ];
        
        $this->render('public/search', $data, 'public');
    }
    
    /**
     * BPDAS detail page
     */
    public function detail($id) {
        $bpdasModel = $this->model('BPDAS');
        $stockModel = $this->model('Stock');
        
        // Get BPDAS details
        $bpdas = $bpdasModel->getWithStockDetails($id);
        
        if (!$bpdas) {
            $this->redirect('home');
            return;
        }
        
        // Get stock for this BPDAS
        $stock = $stockModel->getByBPDAS($id);
        
        $data = [
            'title' => $bpdas['name'],
            'bpdas' => $bpdas,
            'stock' => $stock
        ];
        
        $this->render('public/bpdas-detail', $data, 'public');
    }
    
    /**
     * How to get seedlings page
     */
    public function howto() {
        $data = [
            'title' => 'Cara Mendapatkan Bibit Gratis'
        ];
        
        $this->render('public/howto', $data, 'public');
    }
    
    /**
     * About page
     */
    public function about() {
        $data = [
            'title' => 'Tentang Kami'
        ];
        
        $this->render('public/about', $data, 'public');
    }
    
    /**
     * Contact page
     */
    public function contact() {
        $data = [
            'title' => 'Kontak'
        ];
        
        $this->render('public/contact', $data, 'public');
    }
    
    /**
     * AJAX: Autocomplete provinces
     */
    public function autocompleteProvinces() {
        $term = $this->get('term', '');
        $provinceModel = $this->model('Province');
        
        $results = $provinceModel->autocomplete($term);
        
        $this->json($results);
    }
    
    /**
     * AJAX: Autocomplete seedling types
     */
    public function autocompleteSeedlings() {
        $term = $this->get('term', '');
        $seedlingTypeModel = $this->model('SeedlingType');
        
        $results = $seedlingTypeModel->autocomplete($term);
        
        $this->json($results);
    }
    
    /**
     * AJAX: Get BPDAS by province
     */
    public function getBPDASByProvince() {
        $provinceId = $this->get('province_id');
        
        if (!$provinceId) {
            $this->json(['success' => false, 'message' => 'Province ID required']);
            return;
        }
        
        $bpdasModel = $this->model('BPDAS');
        $bpdasList = $bpdasModel->getByProvince($provinceId);
        
        $this->json(['success' => true, 'data' => $bpdasList]);
    }
    
    /**
     * AJAX: Get stock by BPDAS and seedling type
     */
    public function checkStock() {
        $bpdasId = $this->get('bpdas_id');
        $seedlingTypeId = $this->get('seedling_type_id');
        
        if (!$bpdasId || !$seedlingTypeId) {
            $this->json(['success' => false, 'message' => 'Missing parameters']);
            return;
        }
        
        $stockModel = $this->model('Stock');
        $stock = $stockModel->findByBPDASAndSeedling($bpdasId, $seedlingTypeId);
        
        if ($stock) {
            $this->json([
                'success' => true,
                'available' => true,
                'quantity' => $stock['quantity']
            ]);
        } else {
            $this->json([
                'success' => true,
                'available' => false,
                'quantity' => 0
            ]);
        }
    }
}

<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/scratch_test_delete3.php';
require 'C:\xampp\htdocs\seedling-dashboard\seedling-dashboard\config\config.php';
require 'C:\xampp\htdocs\seedling-dashboard\seedling-dashboard\core\Model.php';

class TestDelete extends Model {
    protected $table = 'bahan_baku_transactions';
    public function check() {
        try {
            $stmt = $this->db->prepare('DELETE FROM bahan_baku_transactions WHERE id = 1');
            $res = $stmt->execute();
            var_dump($res);
            if (!$res) {
                var_dump($stmt->errorInfo());
            }
        } catch(Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
    }
}

$t = new TestDelete();
$t->check();

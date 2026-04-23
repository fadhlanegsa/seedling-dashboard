<?php
$file = 'c:\\xampp\\htdocs\\seedling-dashboard\\seedling-dashboard\\controllers\\SeedlingAdminController.php';
$content = file_get_contents($file);

$helperMethod = "
    /**
     * Resolves BPDAS and Nursery IDs to prevent NULL values from crashing the system.
     */
    private function resolveLocationIds(\$postBpdas, \$postNursery, \$sourceBpdas = null, \$sourceNursery = null) {
        \$user = currentUser();
        
        \$nurseryId = (\$user['role'] === 'admin' && !empty(\$postNursery)) ? \$postNursery : (\$sourceNursery ?? \$user['nursery_id']);
        \$bpdasId = (\$user['role'] === 'admin' && !empty(\$postBpdas)) ? \$postBpdas : (\$sourceBpdas ?? \$user['bpdas_id']);

        if (empty(\$bpdasId) && !empty(\$nurseryId)) {
            \$stmt = \$this->db->prepare(\"SELECT bpdas_id FROM nurseries WHERE id = ?\");
            \$stmt->execute([\$nurseryId]);
            \$nursery = \$stmt->fetch();
            if (\$nursery && !empty(\$nursery['bpdas_id'])) {
                \$bpdasId = \$nursery['bpdas_id'];
            }
        }
        
        if (empty(\$bpdasId)) {
            \$bpdasData = \$this->db->query(\"SELECT id FROM bpdas LIMIT 1\")->fetch();
            if (\$bpdasData) {
                \$bpdasId = \$bpdasData['id'];
            }
        }
        
        return [
            'nursery_id' => \$nurseryId ?: null,
            'bpdas_id' => \$bpdasId ?: null
        ];
    }
";

if (strpos($content, 'resolveLocationIds') === false) {
    $content = preg_replace('/(class SeedlingAdminController extends Controller \{[\s\S]*?public function __construct\(\) \{[\s\S]*?\}\s*)/', "$1\n$helperMethod\n", $content);
}

// Fix array assignments:
// 'bpdas_id'         => ($user['role'] === 'admin' && $this->post('bpdas_id')) ? $this->post('bpdas_id') : $user['bpdas_id'],
// 'nursery_id'       => ($user['role'] === 'admin' && $this->post('nursery_id')) ? $this->post('nursery_id') : $user['nursery_id'],

$pattern = "/(\s*)'bpdas_id'\s*=>\s*\(\\$user\['role'\]\s*===\s*'admin'\s*&&\s*\\$this->post\('bpdas_id'\)\)\s*\?\s*\\$this->post\('bpdas_id'\)\s*:\s*(\\$sourceBatch\['bpdas_id'\]\s*\?\?\s*\\$user\['bpdas_id'\]|\\$user\['bpdas_id'\])\s*,(\s*)'nursery_id'\s*=>\s*\(\\$user\['role'\]\s*===\s*'admin'\s*&&\s*\\$this->post\('nursery_id'\)\)\s*\?\s*\\$this->post\('nursery_id'\)\s*:\s*(\\$sourceBatch\['nursery_id'\]\s*\?\?\s*\\$user\['nursery_id'\]|\\$user\['nursery_id'\])\s*,/m";

$content = preg_replace_callback($pattern, function($matches) {
    $indent = $matches[1];
    $sourceBpdas = (strpos($matches[2], 'sourceBatch') !== false) ? "\$sourceBatch['bpdas_id']" : "null";
    $sourceNursery = (strpos($matches[4], 'sourceBatch') !== false) ? "\$sourceBatch['nursery_id']" : "null";
    
    // We can't inject a variable assignment inline in an array. 
    // Wait, we need to call \$this->resolveLocationIds() BEFORE the array!
    // But this regex is replacing the inside of the array.
    // Instead, let's just inline the function call!
    return $indent . "'bpdas_id' => \$this->resolveLocationIds(\$this->post('bpdas_id'), \$this->post('nursery_id'), $sourceBpdas, $sourceNursery)['bpdas_id']," .
           $matches[3] . "'nursery_id' => \$this->resolveLocationIds(\$this->post('bpdas_id'), \$this->post('nursery_id'), $sourceBpdas, $sourceNursery)['nursery_id'],";
}, $content);

file_put_contents($file, $content);
echo "Patched SeedlingAdminController.php\n";

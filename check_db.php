<?php
require_once 'config/config.php';

try {
    echo "Checking database connection...\n";

    // Check if requests table exists and has data
    $stmt = $db->query('SELECT COUNT(*) as count FROM requests');
    $result = $stmt->fetch();
    echo 'Total requests in database: ' . $result['count'] . "\n";

    if ($result['count'] > 0) {
        echo "Sample requests:\n";
        $stmt = $db->query('SELECT id, request_number, user_id, bpdas_id FROM requests LIMIT 5');
        $requests = $stmt->fetchAll();
        foreach ($requests as $req) {
            echo 'ID: ' . $req['id'] . ', Number: ' . $req['request_number'] . ', User: ' . $req['user_id'] . ', BPDAS: ' . $req['bpdas_id'] . "\n";
        }

        // Test getWithDetails method
        echo "\nTesting getWithDetails method for request ID 1:\n";
        $requestModel = new Request();
        $request = $requestModel->getWithDetails(1);
        if ($request) {
            echo "Request found: " . $request['request_number'] . "\n";
            echo "Requester: " . $request['requester_name'] . "\n";
            echo "BPDAS: " . $request['bpdas_name'] . "\n";
        } else {
            echo "Request not found!\n";
        }
    } else {
        echo "No requests found in database. Please run the sample data SQL.\n";
    }

} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage() . "\n";
}
?>

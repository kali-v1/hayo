<?php
// Include necessary files
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/admin/classes/AdminAuth.php';

// Test credentials
$testUsers = [
    [
        'username' => 'admin',
        'password' => 'admin123',
        'hash' => '$2y$12$uPQ0FPMokqH5wANZeAfZeu58BGBUnE0Fydr8gapH2MZo5nSW9N0yO'
    ],
    [
        'username' => 'instructor',
        'password' => 'instructor123',
        'hash' => '$2y$12$hdQwpr2cHsrIuii4atL8Zu2EijOEBuq1dp.pudZPjHIVMVdtENIeW'
    ],
    [
        'username' => 'dataentry',
        'password' => 'dataentry123',
        'hash' => '$2y$12$/6vFWokm2ENL17rdKp9kpOSyWmIqNHyK2xicZhDdMPJbg3SXVDhMi'
    ]
];

echo "=== Login Debug Test ===\n\n";

// Test direct password verification
echo "Testing direct password_verify() function:\n";
foreach ($testUsers as $user) {
    $result = password_verify($user['password'], $user['hash']);
    echo "- {$user['username']}: " . ($result ? "SUCCESS" : "FAILED") . "\n";
}
echo "\n";

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

// Test database retrieval and verification
echo "Testing database retrieval and verification:\n";
foreach ($testUsers as $user) {
    // Get user from database
    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminData = $result->fetch_assoc();
    
    if ($adminData) {
        echo "- {$user['username']} found in database\n";
        echo "  Stored hash: {$adminData['password']}\n";
        
        // Test password verification with stored hash
        $verified = password_verify($user['password'], $adminData['password']);
        echo "  Password verification: " . ($verified ? "SUCCESS" : "FAILED") . "\n";
        
        // Check if hashes match what we expect
        $hashesMatch = ($adminData['password'] === $user['hash']);
        echo "  Hash matches expected: " . ($hashesMatch ? "YES" : "NO") . "\n";
    } else {
        echo "- {$user['username']} NOT found in database\n";
    }
    echo "\n";
}

// Test the actual login method
echo "Testing AdminAuth login method:\n";
$auth = new AdminAuth();
foreach ($testUsers as $user) {
    $loginResult = $auth->login($user['username'], $user['password']);
    echo "- {$user['username']}: " . ($loginResult ? "SUCCESS" : "FAILED") . "\n";
}
echo "\n";

echo "=== Debug Complete ===\n";
?>
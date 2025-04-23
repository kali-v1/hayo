<?php
// Test password verification
$storedHash = '$2y$12$1WtUipm3qbpEpYS0MRwzpuRwfaJXK38FYhWcXDH7xQm5UpyQSL7Hy';
$password = 'password';

echo "Testing password verification:\n";
echo "Stored hash: $storedHash\n";
echo "Password: $password\n";
echo "Result: " . (password_verify($password, $storedHash) ? "true" : "false") . "\n";

// Generate a new hash for comparison
$newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "\nGenerating new hash for the same password:\n";
echo "New hash: $newHash\n";
echo "Verification with new hash: " . (password_verify($password, $newHash) ? "true" : "false") . "\n";

// Test with a different password
$differentPassword = 'Password123';
echo "\nTesting with a different password ($differentPassword):\n";
echo "Result: " . (password_verify($differentPassword, $storedHash) ? "true" : "false") . "\n";
<?php
// Debug file to check the structure of the data

// Include the database connection
require_once __DIR__ . '/config/database.php';

// Get the question ID from the query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "Please provide a valid question ID";
    exit;
}

// Get the question data
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    echo "Question not found";
    exit;
}

// Display the question data
echo "<h1>Question Data</h1>";
echo "<pre>";
print_r($question);
echo "</pre>";

// Parse the JSON data
echo "<h2>Options</h2>";
$options = json_decode($question['options'], true);
echo "<pre>";
print_r($options);
echo "</pre>";

echo "<h2>Correct Answers</h2>";
$correct_answer = json_decode($question['correct_answer'], true);
echo "<pre>";
print_r($correct_answer);
echo "</pre>";
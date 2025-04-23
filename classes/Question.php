<?php
/**
 * Question Class
 * 
 * Handles question-related operations.
 */
class Question {
    private $conn;
    private $table = "questions";
    
    // Question properties
    public $id;
    public $exam_id;
    public $question_text;
    public $question_type; // single_choice, multiple_choice, drag_drop
    public $options;
    public $correct_answer;
    public $explanation;
    public $points;
    public $order_number;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection
     */
    public function __construct($db = null) {
        if ($db) {
            $this->conn = $db;
        } else {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    }
    
    /**
     * Get all questions
     * 
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of questions
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT q.*, e.title as exam_title 
                  FROM " . $this->table . " q
                  LEFT JOIN exams e ON q.exam_id = e.id
                  ORDER BY q.exam_id, q.order_number LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get question by ID
     * 
     * @param int $id Question ID
     * @return bool True if question found, false otherwise
     */
    public function getById($id) {
        $query = "SELECT q.*, e.title as exam_title 
                  FROM " . $this->table . " q
                  LEFT JOIN exams e ON q.exam_id = e.id
                  WHERE q.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->exam_id = $row['exam_id'];
            $this->question_text = $row['question_text'];
            $this->question_type = $row['question_type'];
            $this->options = json_decode($row['options'], true);
            $this->correct_answer = json_decode($row['correct_answer'], true);
            $this->explanation = $row['explanation'];
            $this->points = $row['points'];
            $this->order_number = $row['order_number'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a new question
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (exam_id, question_text, question_type, options, correct_answer, explanation, points, order_number, created_at, updated_at) 
                  VALUES 
                  (:exam_id, :question_text, :question_type, :options, :correct_answer, :explanation, :points, :order_number, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->exam_id = (int) $this->exam_id;
        $this->question_text = htmlspecialchars(strip_tags($this->question_text));
        $this->question_type = htmlspecialchars(strip_tags($this->question_type));
        $this->explanation = htmlspecialchars(strip_tags($this->explanation));
        $this->points = (int) $this->points;
        $this->order_number = (int) $this->order_number;
        
        // Convert arrays to JSON
        $options_json = json_encode($this->options);
        $correct_answer_json = json_encode($this->correct_answer);
        
        // Bind parameters
        $stmt->bindParam(':exam_id', $this->exam_id);
        $stmt->bindParam(':question_text', $this->question_text);
        $stmt->bindParam(':question_type', $this->question_type);
        $stmt->bindParam(':options', $options_json);
        $stmt->bindParam(':correct_answer', $correct_answer_json);
        $stmt->bindParam(':explanation', $this->explanation);
        $stmt->bindParam(':points', $this->points);
        $stmt->bindParam(':order_number', $this->order_number);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update question
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET 
                  exam_id = :exam_id, 
                  question_text = :question_text, 
                  question_type = :question_type, 
                  options = :options, 
                  correct_answer = :correct_answer, 
                  explanation = :explanation, 
                  points = :points, 
                  order_number = :order_number, 
                  updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->exam_id = (int) $this->exam_id;
        $this->question_text = htmlspecialchars(strip_tags($this->question_text));
        $this->question_type = htmlspecialchars(strip_tags($this->question_type));
        $this->explanation = htmlspecialchars(strip_tags($this->explanation));
        $this->points = (int) $this->points;
        $this->order_number = (int) $this->order_number;
        
        // Convert arrays to JSON
        $options_json = json_encode($this->options);
        $correct_answer_json = json_encode($this->correct_answer);
        
        // Bind parameters
        $stmt->bindParam(':exam_id', $this->exam_id);
        $stmt->bindParam(':question_text', $this->question_text);
        $stmt->bindParam(':question_type', $this->question_type);
        $stmt->bindParam(':options', $options_json);
        $stmt->bindParam(':correct_answer', $correct_answer_json);
        $stmt->bindParam(':explanation', $this->explanation);
        $stmt->bindParam(':points', $this->points);
        $stmt->bindParam(':order_number', $this->order_number);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete question
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Count total questions
     * 
     * @param int|null $exam_id Exam ID to filter by
     * @return int Total number of questions
     */
    public function count($exam_id = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        
        if ($exam_id) {
            $query .= " WHERE exam_id = :exam_id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($exam_id) {
            $stmt->bindParam(':exam_id', $exam_id);
        }
        
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }
    
    /**
     * Get questions by exam
     * 
     * @param int $exam_id Exam ID
     * @return array Array of questions
     */
    public function getByExam($exam_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE exam_id = :exam_id
                  ORDER BY order_number";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Alias for getByExam
     * 
     * @param int $exam_id Exam ID
     * @return array Array of questions
     */
    public function getByExamId($exam_id) {
        return $this->getByExam($exam_id);
    }
    
    /**
     * Check if answer is correct
     * 
     * @param mixed $answer User's answer
     * @return bool True if answer is correct, false otherwise
     */
    public function checkAnswer($answer) {
        switch ($this->question_type) {
            case 'single_choice':
                return $answer == $this->correct_answer;
                
            case 'multiple_choice':
                // Sort both arrays to ensure consistent comparison
                sort($answer);
                sort($this->correct_answer);
                return $answer == $this->correct_answer;
                
            case 'drag_drop':
                // For drag and drop, we need to check if all items are in the correct positions
                return $answer == $this->correct_answer;
                
            default:
                return false;
        }
    }
}
?>
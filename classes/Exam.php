<?php
/**
 * Exam Class
 * 
 * Handles exam-related operations.
 */
class Exam {
    private $conn;
    private $table = "exams";
    
    // Exam properties
    public $id;
    public $title;
    public $description;
    public $course_id;
    public $company_id;
    public $duration_minutes;
    public $passing_score;
    public $is_free;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    
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
     * Get all exams
     * 
     * @param string $search Search keyword
     * @param string $filter Filter type (all, free, paid)
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Array with 'exams' and 'total' keys
     */
    public function getAll($search = '', $filter = 'all', $sort = 'newest', $page = 1, $perPage = 10) {
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Build the query
        $query = "SELECT e.*, e.duration_minutes as duration, 
                 IFNULL(c.title, 'No Course') as course_title,
                 (SELECT COUNT(*) FROM exam_attempts WHERE exam_id = e.id AND deleted_at IS NULL) as attempts,
                 comp.name as company_name
                 FROM " . $this->table . " e
                 LEFT JOIN courses c ON e.course_id = c.id
                 LEFT JOIN companies comp ON e.company_id = comp.id
                 WHERE e.is_published = 1 AND e.deleted_at IS NULL";
        
        $params = [];
        
        // Add search condition
        if (!empty($search)) {
            $query .= " AND (e.title LIKE :search OR e.description LIKE :search)";
            $searchParam = "%{$search}%";
            $params[':search'] = $searchParam;
        }
        
        // Add filter condition
        if ($filter === 'free') {
            $query .= " AND e.is_free = 1";
        } elseif ($filter === 'paid') {
            $query .= " AND e.is_free = 0";
        }
        
        // Add sorting
        if ($sort === 'oldest') {
            $query .= " ORDER BY e.created_at ASC";
        } else {
            // Default: newest
            $query .= " ORDER BY e.created_at DESC";
        }
        
        // Count total exams (for pagination)
        $countQuery = "SELECT COUNT(*) as total FROM " . $this->table . " e 
                      LEFT JOIN courses c ON e.course_id = c.id 
                      LEFT JOIN companies comp ON e.company_id = comp.id
                      WHERE e.is_published = 1 AND e.deleted_at IS NULL";
                      
        // Add search condition to count query
        if (!empty($search)) {
            $countQuery .= " AND (e.title LIKE :search OR e.description LIKE :search)";
        }
        
        // Add filter condition to count query
        if ($filter === 'free') {
            $countQuery .= " AND e.is_free = 1";
        } elseif ($filter === 'paid') {
            $countQuery .= " AND e.is_free = 0";
        }
        
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
        $total = isset($totalRow['total']) ? $totalRow['total'] : 0;
        
        // Add limit and offset
        $query .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;
        
        // Execute the query
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();
        $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return both the exams and the total count
        return [
            'exams' => $exams,
            'total' => $total
        ];
    }
    
    /**
     * Get exam by ID
     * 
     * @param int $id Exam ID
     * @return bool True if exam found, false otherwise
     */
    public function getById($id) {
        $query = "SELECT e.*, IFNULL(c.title, 'No Course') as course_title, comp.name as company_name
                  FROM " . $this->table . " e
                  LEFT JOIN courses c ON e.course_id = c.id
                  LEFT JOIN companies comp ON e.company_id = comp.id
                  WHERE e.id = :id AND e.deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->course_id = $row['course_id'];
            $this->duration_minutes = $row['duration_minutes'];
            $this->passing_score = $row['passing_score'];
            $this->is_free = $row['is_free'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Load exam by ID
     * 
     * @param int $id Exam ID
     * @return bool True if exam found, false otherwise
     */
    public function loadById($id) {
        return $this->getById($id);
    }
    
    /**
     * Convert exam object to array
     * 
     * @return array Exam data as array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'company_id' => $this->company_id,
            'duration_minutes' => $this->duration_minutes,
            'duration' => $this->duration_minutes, // Added for compatibility
            'passing_score' => $this->passing_score,
            'is_free' => $this->is_free,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
    
    /**
     * Create a new exam
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create() {
        // Generate slug from title
        $slug = $this->generateSlug($this->title);
        
        $query = "INSERT INTO " . $this->table . " 
                  (title, slug, description, course_id, company_id, duration_minutes, passing_score, is_free, created_at, updated_at) 
                  VALUES 
                  (:title, :slug, :description, :course_id, :company_id, :duration_minutes, :passing_score, :is_free, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        // Allow course_id and company_id to be null
        $this->course_id = $this->course_id ? (int) $this->course_id : null;
        $this->company_id = !empty($this->company_id) ? (int) $this->company_id : null;
        $this->duration_minutes = (int) $this->duration_minutes;
        $this->passing_score = (int) $this->passing_score;
        $this->is_free = (int) $this->is_free;
        
        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->bindParam(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':passing_score', $this->passing_score);
        $stmt->bindParam(':is_free', $this->is_free);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update exam
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET 
                  title = :title, 
                  description = :description, 
                  course_id = :course_id, 
                  company_id = :company_id,
                  duration_minutes = :duration_minutes, 
                  passing_score = :passing_score, 
                  is_free = :is_free, 
                  updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        // Allow course_id and company_id to be null
        $this->course_id = $this->course_id ? (int) $this->course_id : null;
        $this->company_id = !empty($this->company_id) ? (int) $this->company_id : null;
        $this->duration_minutes = (int) $this->duration_minutes;
        $this->passing_score = (int) $this->passing_score;
        $this->is_free = (int) $this->is_free;
        
        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':course_id', $this->course_id, PDO::PARAM_INT);
        $stmt->bindParam(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindParam(':duration_minutes', $this->duration_minutes);
        $stmt->bindParam(':passing_score', $this->passing_score);
        $stmt->bindParam(':is_free', $this->is_free);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Soft delete exam
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function delete() {
        $query = "UPDATE " . $this->table . " SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Permanently delete exam (for admin use only)
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function permanentDelete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Count total exams
     * 
     * @param bool $free_only Only count free exams
     * @return int Total number of exams
     */
    public function count($free_only = false) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE deleted_at IS NULL";
        
        if ($free_only) {
            $query .= " AND is_free = 1";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }
    
    /**
     * Get exams by course
     * 
     * @param int $course_id Course ID
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of exams
     */
    public function getByCourse($course_id, $limit = 10, $offset = 0) {
        $query = "SELECT e.*, IFNULL(c.title, 'No Course') as course_title, comp.name as company_name
                  FROM " . $this->table . " e
                  LEFT JOIN courses c ON e.course_id = c.id
                  LEFT JOIN companies comp ON e.company_id = comp.id
                  WHERE e.course_id = :course_id AND e.is_published = 1 AND e.deleted_at IS NULL
                  ORDER BY e.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Alias for getByCourse
     * 
     * @param int $course_id Course ID
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of exams
     */
    public function getByCourseId($course_id, $limit = 10, $offset = 0) {
        return $this->getByCourse($course_id, $limit, $offset);
    }
    
    /**
     * Get questions for an exam
     * 
     * @return array Array of questions
     */
    public function getQuestions() {
        $query = "SELECT q.* 
                  FROM questions q
                  WHERE q.exam_id = :exam_id AND q.deleted_at IS NULL
                  ORDER BY q.order_number";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exam_id', $this->id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if user has access to exam
     * 
     * @param int $user_id User ID
     * @return bool True if user has access, false otherwise
     */
    public function hasAccess($user_id) {
        // If exam is free, everyone has access
        if ($this->is_free) {
            return true;
        }
        
        // Check if user has purchased the course
        $query = "SELECT COUNT(*) as count 
                  FROM user_courses 
                  WHERE user_id = :user_id AND course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $this->course_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['count'] > 0;
    }
    
    /**
     * Get user's attempts for an exam
     * 
     * @param int $user_id User ID
     * @param int $exam_id Exam ID
     * @return array Array of user's exam attempts
     */
    public function getUserAttempts($user_id, $exam_id) {
        $query = "SELECT ea.*, 
                 ea.score as percentage,
                 CASE WHEN ea.score >= e.passing_score 
                      THEN 'passed' ELSE 'failed' END as result
                 FROM exam_attempts ea
                 JOIN exams e ON ea.exam_id = e.id
                 WHERE ea.user_id = :user_id AND ea.exam_id = :exam_id
                 ORDER BY ea.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get statistics for an exam
     * 
     * @param int $exam_id Exam ID
     * @return array Exam statistics
     */
    public function getStats($exam_id) {
        // Get total attempts
        $query1 = "SELECT COUNT(*) as total_attempts FROM exam_attempts WHERE exam_id = :exam_id";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(':exam_id', $exam_id);
        $stmt1->execute();
        $totalAttempts = $stmt1->fetch(PDO::FETCH_ASSOC)['total_attempts'];
        
        if ($totalAttempts == 0) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'pass_rate' => 0
            ];
        }
        
        // Get average score
        $query2 = "SELECT AVG(ea.score) as average_score
                  FROM exam_attempts ea
                  WHERE ea.exam_id = :exam_id";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':exam_id', $exam_id);
        $stmt2->execute();
        $averageScore = $stmt2->fetch(PDO::FETCH_ASSOC)['average_score'];
        
        // Get pass rate
        $query3 = "SELECT 
                  (COUNT(CASE WHEN ea.score >= e.passing_score THEN 1 END) / COUNT(*)) * 100 as pass_rate
                  FROM exam_attempts ea
                  JOIN exams e ON ea.exam_id = e.id
                  WHERE ea.exam_id = :exam_id";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->bindParam(':exam_id', $exam_id);
        $stmt3->execute();
        $passRate = $stmt3->fetch(PDO::FETCH_ASSOC)['pass_rate'];
        
        return [
            'total_attempts' => $totalAttempts,
            'average_score' => round($averageScore, 1),
            'pass_rate' => round($passRate, 1)
        ];
    }
    
    /**
     * Get recommended courses for an exam
     * 
     * @param int $exam_id Exam ID
     * @return array Array of recommended courses
     */
    public function getRecommendedCourses($exam_id) {
        $query = "SELECT c.*
                 FROM exam_course_recommendations ecr
                 JOIN courses c ON ecr.course_id = c.id
                 WHERE ecr.exam_id = :exam_id
                 ORDER BY ecr.priority";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add a recommended course for an exam
     * 
     * @param int $exam_id Exam ID
     * @param int $course_id Course ID
     * @param int $priority Priority (lower number = higher priority)
     * @return bool True if added successfully, false otherwise
     */
    public function addRecommendedCourse($exam_id, $course_id, $priority = 0) {
        $query = "INSERT INTO exam_course_recommendations 
                 (exam_id, course_id, priority, created_at, updated_at) 
                 VALUES 
                 (:exam_id, :course_id, :priority, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->bindParam(':priority', $priority, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Remove a recommended course for an exam
     * 
     * @param int $exam_id Exam ID
     * @param int $course_id Course ID
     * @return bool True if removed successfully, false otherwise
     */
    public function removeRecommendedCourse($exam_id, $course_id) {
        $query = "DELETE FROM exam_course_recommendations 
                 WHERE exam_id = :exam_id AND course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Update the priority of a recommended course
     * 
     * @param int $exam_id Exam ID
     * @param int $course_id Course ID
     * @param int $priority New priority
     * @return bool True if updated successfully, false otherwise
     */
    public function updateRecommendedCoursePriority($exam_id, $course_id, $priority) {
        $query = "UPDATE exam_course_recommendations 
                 SET priority = :priority, updated_at = NOW()
                 WHERE exam_id = :exam_id AND course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':priority', $priority, PDO::PARAM_INT);
        $stmt->bindParam(':exam_id', $exam_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Start a new exam attempt
     * 
     * @param int $user_id User ID
     * @param int $exam_id Exam ID
     * @return int The attempt ID
     */
    public function startAttempt($user_id, $exam_id) {
        $query = "INSERT INTO exam_attempts (user_id, exam_id, started_at, created_at, updated_at)
                 VALUES (:user_id, :exam_id, NOW(), NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        
        return $this->conn->lastInsertId();
    }
    
    /**
     * Complete an exam attempt
     * 
     * @param int $attempt_id The attempt ID
     * @param int $score The raw score
     * @param int $percentage The percentage score
     * @param bool $passed Whether the user passed the exam
     * @return bool True if updated successfully, false otherwise
     */
    public function completeAttempt($attempt_id, $score, $percentage, $passed) {
        $query = "UPDATE exam_attempts 
                 SET completed_at = NOW(), 
                     score = :score, 
                     is_passed = :passed, 
                     updated_at = NOW() 
                 WHERE id = :attempt_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':score', $percentage);
        $stmt->bindParam(':passed', $passed, PDO::PARAM_BOOL);
        $stmt->bindParam(':attempt_id', $attempt_id);
        
        return $stmt->execute();
    }
    
    /**
     * Get answers for an exam attempt
     * 
     * @param int $attempt_id The attempt ID
     * @return array The answers for the attempt
     */
    public function getAttemptAnswers($attempt_id) {
        $query = "SELECT answers FROM exam_attempts WHERE id = :attempt_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attempt_id', $attempt_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && $row['answers']) {
            return json_decode($row['answers'], true);
        }
        
        return [];
    }
    
    /**
     * Save an answer for an exam attempt
     * 
     * @param int $attempt_id The attempt ID
     * @param int $question_id The question ID
     * @param string $answer The user's answer
     * @param bool $is_correct Whether the answer is correct
     * @return bool True if saved successfully, false otherwise
     */
    public function saveAnswer($attempt_id, $question_id, $answer, $is_correct) {
        // First, get the current answers
        $query = "SELECT answers FROM exam_attempts WHERE id = :attempt_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':attempt_id', $attempt_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $answers = [];
        
        if ($row && $row['answers']) {
            $answers = json_decode($row['answers'], true);
        }
        
        // Add the new answer
        $answers[$question_id] = [
            'answer' => $answer,
            'is_correct' => $is_correct
        ];
        
        // Update the answers in the database
        $answersJson = json_encode($answers);
        
        $query = "UPDATE exam_attempts SET answers = :answers, updated_at = NOW() WHERE id = :attempt_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':answers', $answersJson);
        $stmt->bindParam(':attempt_id', $attempt_id);
        
        return $stmt->execute();
    }
}
?>
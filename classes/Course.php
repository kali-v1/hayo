<?php
/**
 * Course Class
 * 
 * Handles course-related operations.
 */
class Course {
    private $conn;
    private $table = "courses";
    
    // Course properties
    public $id;
    public $title;
    public $description;
    public $price;
    public $is_free;
    public $image;
    public $admin_id;
    public $is_featured;
    public $is_published;
    public $slug;
    public $company_id;
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
     * Get all courses
     * 
     * @param string $search Search keyword
     * @param string $filter Filter type (all, free, paid)
     * @param string $sort Sort order (newest, oldest, price_low, price_high)
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Array with 'courses' and 'total' keys
     */
    public function getAll($search = '', $filter = 'all', $sort = 'newest', $page = 1, $perPage = 10) {
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Build the query
        $query = "SELECT c.*, a.username as admin_username,
                 (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.deleted_at IS NULL) as students,
                 (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id AND r.deleted_at IS NULL) as rating,
                 (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id AND r.deleted_at IS NULL) as reviews,
                 comp.name as company_name
                 FROM " . $this->table . " c
                 LEFT JOIN admins a ON c.admin_id = a.id
                 LEFT JOIN companies comp ON c.company_id = comp.id
                 WHERE c.is_published = 1 AND c.deleted_at IS NULL";
        
        $params = [];
        
        // Add search condition
        if (!empty($search)) {
            $query .= " AND (c.title LIKE :search OR c.description LIKE :search)";
            $searchParam = "%{$search}%";
            $params[':search'] = $searchParam;
        }
        
        // Add filter condition
        if ($filter === 'free') {
            $query .= " AND c.is_free = 1";
        } elseif ($filter === 'paid') {
            $query .= " AND c.is_free = 0";
        }
        
        // Add sorting
        if ($sort === 'oldest') {
            $query .= " ORDER BY c.created_at ASC";
        } elseif ($sort === 'price_low') {
            $query .= " ORDER BY c.price ASC, c.created_at DESC";
        } elseif ($sort === 'price_high') {
            $query .= " ORDER BY c.price DESC, c.created_at DESC";
        } else {
            // Default: newest
            $query .= " ORDER BY c.created_at DESC";
        }
        
        // Count total courses (for pagination)
        $countQuery = preg_replace('/SELECT c\.\*, a\.username as admin_username.*?FROM/', 'SELECT COUNT(*) as total FROM', $query);
        $countQuery = preg_replace('/ORDER BY.*$/', '', $countQuery);
        
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
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return both the courses and the total count
        return [
            'courses' => $courses,
            'total' => $total
        ];
    }
    
    /**
     * Get course by ID
     * 
     * @param int $id Course ID
     * @return bool True if course found, false otherwise
     */
    public function getById($id) {
        $query = "SELECT c.*, a.username as admin_username,
                 (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.deleted_at IS NULL) as students,
                 (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id AND r.deleted_at IS NULL) as rating,
                 (SELECT COUNT(*) FROM reviews r WHERE r.course_id = c.id AND r.deleted_at IS NULL) as reviews,
                 comp.name as company_name
                 FROM " . $this->table . " c
                 LEFT JOIN admins a ON c.admin_id = a.id
                 LEFT JOIN companies comp ON c.company_id = comp.id
                 WHERE c.id = :id AND c.deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->is_free = $row['is_free'];
            $this->image = $row['image'];
            $this->admin_id = $row['admin_id'];
            $this->admin_username = $row['admin_username'] ?? null;
            $this->students = $row['students'] ?? 0;
            $this->rating = $row['rating'] ?? 0;
            $this->reviews = $row['reviews'] ?? 0;
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a new course
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create() {
        // Generate slug from title
        $this->slug = $this->generateSlug($this->title);
        
        $query = "INSERT INTO " . $this->table . " 
                  (title, slug, description, price, is_free, image, admin_id, is_featured, is_published, company_id, created_at, updated_at) 
                  VALUES 
                  (:title, :slug, :description, :price, :is_free, :image, :admin_id, :is_featured, :is_published, :company_id, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = (float) $this->price;
        $this->is_free = (int) $this->is_free;
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->admin_id = (int) $this->admin_id;
        $this->is_featured = isset($this->is_featured) ? (int) $this->is_featured : 0;
        $this->is_published = isset($this->is_published) ? (int) $this->is_published : 0;
        $this->company_id = !empty($this->company_id) ? (int) $this->company_id : null;
        
        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':is_free', $this->is_free);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':admin_id', $this->admin_id);
        $stmt->bindParam(':is_featured', $this->is_featured);
        $stmt->bindParam(':is_published', $this->is_published);
        $stmt->bindParam(':company_id', $this->company_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update course
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update() {
        // Update slug if title has changed
        if (isset($this->title)) {
            $this->slug = $this->generateSlug($this->title);
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET 
                  title = :title, 
                  slug = :slug,
                  description = :description, 
                  price = :price, 
                  is_free = :is_free, 
                  image = :image, 
                  admin_id = :admin_id,
                  is_featured = :is_featured,
                  is_published = :is_published,
                  company_id = :company_id,
                  updated_at = NOW() 
                  WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = (float) $this->price;
        $this->is_free = (int) $this->is_free;
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->admin_id = (int) $this->admin_id;
        $this->is_featured = (int) $this->is_featured;
        $this->is_published = (int) $this->is_published;
        $this->company_id = !empty($this->company_id) ? (int) $this->company_id : null;
        
        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':is_free', $this->is_free);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':admin_id', $this->admin_id);
        $stmt->bindParam(':is_featured', $this->is_featured);
        $stmt->bindParam(':is_published', $this->is_published);
        $stmt->bindParam(':company_id', $this->company_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate a URL-friendly slug from a string
     * 
     * @param string $string The string to convert to a slug
     * @return string The slug
     */
    private function generateSlug($string) {
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        // Convert to lowercase
        $slug = strtolower($slug);
        // Remove leading/trailing hyphens
        $slug = trim($slug, '-');
        
        // Check if slug already exists
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE slug = :slug";
        if (isset($this->id)) {
            $query .= " AND id != :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        
        if (isset($this->id)) {
            $stmt->bindParam(':id', $this->id);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If slug exists, append a number
        if ($row['count'] > 0) {
            $i = 1;
            $original_slug = $slug;
            do {
                $slug = $original_slug . '-' . $i++;
                
                $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE slug = :slug";
                if (isset($this->id)) {
                    $query .= " AND id != :id";
                }
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':slug', $slug);
                
                if (isset($this->id)) {
                    $stmt->bindParam(':id', $this->id);
                }
                
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } while ($row['count'] > 0);
        }
        
        return $slug;
    }
    
    /**
     * Soft delete course
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
     * Permanently delete course (for admin use only)
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
     * Count total courses
     * 
     * @param bool $free_only Only count free courses
     * @return int Total number of courses
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
     * Get courses by admin
     * 
     * @param int $admin_id Admin ID
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of courses
     */
    public function getByAdmin($admin_id, $limit = 10, $offset = 0) {
        $query = "SELECT c.*, a.username as admin_username, comp.name as company_name
                  FROM " . $this->table . " c
                  LEFT JOIN admins a ON c.admin_id = a.id
                  LEFT JOIN companies comp ON c.company_id = comp.id
                  WHERE c.admin_id = :admin_id AND c.deleted_at IS NULL
                  ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Search courses
     * 
     * @param string $keyword Search keyword
     * @param int $limit Limit the number of results
     * @param int $offset Offset for pagination
     * @return array Array of courses
     */
    public function search($keyword, $limit = 10, $offset = 0) {
        $query = "SELECT c.*, a.username as admin_username, comp.name as company_name
                  FROM " . $this->table . " c
                  LEFT JOIN admins a ON c.admin_id = a.id
                  LEFT JOIN companies comp ON c.company_id = comp.id
                  WHERE (c.title LIKE :keyword OR c.description LIKE :keyword) AND c.deleted_at IS NULL
                  ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        
        $keyword = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get popular courses
     * 
     * @param int $limit Limit the number of results
     * @return array Array of popular courses
     */
    public function getPopular($limit = 3) {
        $query = "SELECT c.*, a.username as admin_username, comp.name as company_name,
                  (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id AND e.deleted_at IS NULL) as enrollment_count
                  FROM " . $this->table . " c
                  LEFT JOIN admins a ON c.admin_id = a.id
                  LEFT JOIN companies comp ON c.company_id = comp.id
                  WHERE c.is_published = 1 AND c.deleted_at IS NULL
                  ORDER BY enrollment_count DESC, c.is_featured DESC, c.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if a user is enrolled in a course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if user is enrolled, false otherwise
     */
    public function isUserEnrolled($user_id, $course_id) {
        try {
            // Check if the user_courses table exists
            $tableCheck = $this->conn->query("SHOW TABLES LIKE 'user_courses'");
            if ($tableCheck->rowCount() == 0) {
                // Table doesn't exist, so no enrollments
                return false;
            }
            
            $query = "SELECT COUNT(*) as count 
                      FROM user_courses 
                      WHERE user_id = :user_id AND course_id = :course_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int) $row['count'] > 0;
        } catch (Exception $e) {
            // Log the error
            error_log("Error checking enrollment: " . $e->getMessage());
            // Return false as a fallback
            return false;
        }
    }
    
    /**
     * Load course by ID
     * 
     * @param int $id Course ID
     * @return bool True if course found, false otherwise
     */
    public function loadById($id) {
        return $this->getById($id);
    }
    
    /**
     * Convert course object to array
     * 
     * @return array Course data as array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'is_free' => $this->is_free,
            'image' => $this->image,
            'admin_id' => $this->admin_id,
            'admin_username' => $this->admin_username ?? null,
            'students' => $this->students ?? 0,
            'rating' => $this->rating ?? 0,
            'reviews' => $this->reviews ?? 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
    
    /**
     * Get reviews for a course
     * 
     * @param int $course_id Course ID
     * @return array Array of reviews
     */
    public function getReviews($course_id) {
        $query = "SELECT r.*, u.username, u.profile_image 
                  FROM reviews r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.course_id = :course_id
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Enroll a user in a course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if enrolled successfully, false otherwise
     */
    public function enrollUser($user_id, $course_id) {
        try {
            // Check if already enrolled
            if ($this->isUserEnrolled($user_id, $course_id)) {
                return true; // Already enrolled
            }
            
            $query = "INSERT INTO enrollments (user_id, course_id, enrolled_at) 
                      VALUES (:user_id, :course_id, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':course_id', $course_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error enrolling user: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a user has reviewed a course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if user has reviewed, false otherwise
     */
    public function hasUserReviewed($user_id, $course_id) {
        $query = "SELECT COUNT(*) as count 
                  FROM reviews 
                  WHERE user_id = :user_id AND course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['count'] > 0;
    }
    
    /**
     * Update a review
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return bool True if updated successfully, false otherwise
     */
    public function updateReview($user_id, $course_id, $rating, $review) {
        $query = "UPDATE reviews 
                  SET rating = :rating, review = :review, updated_at = NOW() 
                  WHERE user_id = :user_id AND course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review', $review);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }
    
    /**
     * Add a review
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @param int $rating Rating (1-5)
     * @param string $review Review text
     * @return bool True if added successfully, false otherwise
     */
    public function addReview($user_id, $course_id, $rating, $review) {
        $query = "INSERT INTO reviews (user_id, course_id, rating, review, created_at, updated_at) 
                  VALUES (:user_id, :course_id, :rating, :review, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review', $review);
        
        return $stmt->execute();
    }
}
?>
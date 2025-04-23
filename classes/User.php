<?php
/**
 * User Class
 * 
 * This class represents a user and provides methods for user-related operations.
 */
class User {
    /**
     * @var int The user ID
     */
    private $id;
    
    /**
     * @var string The username
     */
    private $username;
    
    /**
     * @var string The email address
     */
    private $email;
    
    /**
     * @var string The first name
     */
    private $firstName;
    
    /**
     * @var string The last name
     */
    private $lastName;
    
    /**
     * @var string The profile image path
     */
    private $profileImage;
    
    /**
     * @var string The bio
     */
    private $bio;
    
    /**
     * @var bool Whether the user is active
     */
    private $isActive;
    
    /**
     * @var string The last login time
     */
    private $lastLogin;
    
    /**
     * @var string The creation time
     */
    private $createdAt;
    
    /**
     * @var string The last update time
     */
    private $updatedAt;
    
    /**
     * @var string The deletion time
     */
    private $deletedAt;
    
    /**
     * @var string The phone number
     */
    private $phone;
    
    /**
     * @var string The mobile number
     */
    private $mobileNumber;
    
    /**
     * @var string The address
     */
    private $address;
    
    /**
     * @var string The city
     */
    private $city;
    
    /**
     * @var string The country
     */
    private $country;
    
    /**
     * @var string The postal code
     */
    private $postalCode;
    
    /**
     * @var string The website URL
     */
    private $website;
    
    /**
     * @var string The Facebook username
     */
    private $facebook;
    
    /**
     * @var string The Twitter username
     */
    private $twitter;
    
    /**
     * @var string The LinkedIn username
     */
    private $linkedin;
    
    /**
     * @var string The Instagram username
     */
    private $instagram;
    
    /**
     * @var Database The database connection
     */
    private $db;
    
    /**
     * Constructor
     * 
     * @param int $id The user ID (optional)
     */
    public function __construct($id = null) {
        $this->db = new Database();
        
        if ($id) {
            $this->loadById($id);
        }
    }
    
    /**
     * Load a user by ID
     * 
     * @param int $id The user ID
     * @return bool True if successful, false otherwise
     */
    public function loadById($id) {
        $user = $this->db->queryOne("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL", [$id]);
        
        if (!$user) {
            return false;
        }
        
        $this->setProperties($user);
        
        return true;
    }
    
    /**
     * Load a user by username
     * 
     * @param string $username The username
     * @return bool True if successful, false otherwise
     */
    public function loadByUsername($username) {
        $user = $this->db->queryOne("SELECT * FROM users WHERE username = ? AND deleted_at IS NULL", [$username]);
        
        if (!$user) {
            return false;
        }
        
        $this->setProperties($user);
        
        return true;
    }
    
    /**
     * Load a user by email
     * 
     * @param string $email The email address
     * @return bool True if successful, false otherwise
     */
    public function loadByEmail($email) {
        $user = $this->db->queryOne("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL", [$email]);
        
        if (!$user) {
            return false;
        }
        
        $this->setProperties($user);
        
        return true;
    }
    
    /**
     * Save the user to the database
     * 
     * @return bool True if successful, false otherwise
     */
    public function save() {
        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'profile_image' => $this->profileImage,
            'bio' => $this->bio,
            'is_active' => $this->isActive,
            'phone' => $this->phone,
            'mobile_number' => $this->mobileNumber,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'website' => $this->website,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'linkedin' => $this->linkedin,
            'instagram' => $this->instagram,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->id) {
            // Update existing user
            return $this->db->update('users', $data, 'id = ?', [$this->id]) > 0;
        } else {
            // Create new user
            $data['created_at'] = date('Y-m-d H:i:s');
            
            $id = $this->db->insert('users', $data);
            
            if ($id) {
                $this->id = $id;
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Soft delete the user from the database
     * 
     * @return bool True if successful, false otherwise
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }
        
        $data = [
            'deleted_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->update('users', $data, 'id = ? AND deleted_at IS NULL', [$this->id]) > 0;
    }
    
    /**
     * Permanently delete the user from the database (for admin use only)
     * 
     * @return bool True if successful, false otherwise
     */
    public function permanentDelete() {
        if (!$this->id) {
            return false;
        }
        
        return $this->db->delete('users', 'id = ?', [$this->id]) > 0;
    }
    
    /**
     * Set the user's password
     * 
     * @param string $password The new password
     * @return bool True if successful, false otherwise
     */
    public function setPassword($password) {
        if (!$this->id) {
            return false;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_HASH_ALGO, PASSWORD_HASH_OPTIONS);
        
        return $this->db->update('users', ['password' => $hashedPassword, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$this->id]) > 0;
    }
    
    /**
     * Verify a password
     * 
     * @param string $password The password to verify
     * @return bool True if the password is correct, false otherwise
     */
    public function verifyPassword($password) {
        $user = $this->db->queryOne("SELECT password FROM users WHERE id = ?", [$this->id]);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
    
    /**
     * Get all users
     * 
     * @param int $limit The maximum number of users to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The users
     */
    public function getAll($limit = null, $offset = null) {
        $query = "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY username ASC";
        $params = [];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count all users
     * 
     * @return int The number of users
     */
    public function countAll() {
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM users WHERE deleted_at IS NULL");
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the user's enrolled courses
     * 
     * @param int $limit The maximum number of courses to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The courses
     */
    public function getEnrolledCourses($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT c.* FROM courses c
                  INNER JOIN enrollments e ON c.id = e.course_id
                  WHERE e.user_id = ?
                  ORDER BY e.enrollment_date DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the user's enrolled courses
     * 
     * @return int The number of enrolled courses
     */
    public function countEnrolledCourses() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM enrollments WHERE user_id = ?", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the user's completed courses
     * 
     * @param int $limit The maximum number of courses to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The completed courses
     */
    public function getCompletedCourses($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT c.* FROM courses c
                  INNER JOIN enrollments e ON c.id = e.course_id
                  WHERE e.user_id = ? AND e.is_completed = 1
                  ORDER BY e.completion_date DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the user's completed courses
     * 
     * @return int The number of completed courses
     */
    public function countCompletedCourses() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM enrollments WHERE user_id = ? AND is_completed = 1", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the user's exam attempts
     * 
     * @param int $limit The maximum number of attempts to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The exam attempts
     */
    public function getExamAttempts($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT ea.*, e.title as exam_title, c.title as course_title
                  FROM exam_attempts ea
                  INNER JOIN exams e ON ea.exam_id = e.id
                  INNER JOIN courses c ON e.course_id = c.id
                  WHERE ea.user_id = ?
                  ORDER BY ea.started_at DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the user's exam attempts
     * 
     * @return int The number of exam attempts
     */
    public function countExamAttempts() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM exam_attempts WHERE user_id = ?", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the user's certificates
     * 
     * @param int $limit The maximum number of certificates to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The certificates
     */
    public function getCertificates($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT cert.*, c.title as course_title
                  FROM certificates cert
                  INNER JOIN courses c ON cert.course_id = c.id
                  WHERE cert.user_id = ?
                  ORDER BY cert.issue_date DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the user's certificates
     * 
     * @return int The number of certificates
     */
    public function countCertificates() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM certificates WHERE user_id = ?", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the user's reviews
     * 
     * @param int $limit The maximum number of reviews to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The reviews
     */
    public function getReviews($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT r.*, c.title as course_title
                  FROM reviews r
                  INNER JOIN courses c ON r.course_id = c.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Count the user's reviews
     * 
     * @return int The number of reviews
     */
    public function countReviews() {
        if (!$this->id) {
            return 0;
        }
        
        $result = $this->db->queryOne("SELECT COUNT(*) as count FROM reviews WHERE user_id = ?", [$this->id]);
        
        return $result ? (int) $result['count'] : 0;
    }
    
    /**
     * Get the leaderboard data
     * 
     * @param string $period The time period (all_time, month, week)
     * @param int $limit The maximum number of users to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The leaderboard data
     */
    public function getLeaderboard($period = 'all_time', $limit = 10, $offset = 0) {
        $query = "SELECT u.id as user_id, u.username, u.profile_image, 
                  COUNT(ea.id) as completed_exams, 
                  IFNULL(SUM(ea.score), 0) as points, 
                  IFNULL(AVG(ea.score), 0) as average_score,
                  @rank := @rank + 1 as rank
                  FROM users u
                  LEFT JOIN exam_attempts ea ON u.id = ea.user_id,
                  (SELECT @rank := 0) r";
        
        $params = [];
        
        // Add time period filter
        if ($period == 'monthly') {
            $query .= " WHERE ea.completed_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($period == 'weekly') {
            $query .= " WHERE ea.completed_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        }
        
        $query .= " GROUP BY u.id
                   ORDER BY points DESC, average_score DESC
                   LIMIT ? OFFSET ?";
        
        $params[] = (int) $limit;
        $params[] = (int) $offset;
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Get a user's rank on the leaderboard
     * 
     * @param int $userId The user ID
     * @param string $period The time period (all_time, month, week)
     * @return array|null The user's rank data or null if not found
     */
    public function getUserRank($userId, $period = 'all_time') {
        // First get all users ranked by points
        $query = "SELECT u.id as user_id,
                  COUNT(ea.id) as completed_exams, 
                  IFNULL(SUM(ea.score), 0) as points, 
                  IFNULL(AVG(ea.score), 0) as average_score,
                  @rank := @rank + 1 as rank
                  FROM users u
                  LEFT JOIN exam_attempts ea ON u.id = ea.user_id,
                  (SELECT @rank := 0) r";
        
        $params = [];
        
        // Add time period filter
        if ($period == 'monthly') {
            $query .= " WHERE ea.completed_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($period == 'weekly') {
            $query .= " WHERE ea.completed_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
        }
        
        $query .= " GROUP BY u.id
                   ORDER BY points DESC, average_score DESC";
        
        $allUsers = $this->db->query($query, $params);
        
        // Find the user's rank
        $userRank = null;
        foreach ($allUsers as $user) {
            if ($user['user_id'] == $userId) {
                $userRank = [
                    'rank' => $user['rank'],
                    'points' => $user['points'] ?: 0,
                    'completed_exams' => $user['completed_exams'] ?: 0,
                    'average_score' => $user['average_score'] ?: 0
                ];
                break;
            }
        }
        
        // If user not found in results, they have no exam attempts
        if (!$userRank) {
            $userRank = [
                'rank' => count($allUsers) + 1,
                'points' => 0,
                'completed_exams' => 0,
                'average_score' => 0
            ];
        }
        
        return $userRank;
    }
    
    /**
     * Get the user's activity log
     * 
     * @param int $limit The maximum number of log entries to return (optional)
     * @param int $offset The offset for pagination (optional)
     * @return array The activity log
     */
    public function getActivityLog($limit = null, $offset = null) {
        if (!$this->id) {
            return [];
        }
        
        $query = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC";
        $params = [$this->id];
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = (int) $limit;
            
            if ($offset) {
                $query .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }
        
        return $this->db->query($query, $params);
    }
    
    /**
     * Log an activity
     * 
     * @param string $action The action performed
     * @param string $description The description of the action (optional)
     * @return bool True if successful, false otherwise
     */
    public function logActivity($action, $description = null) {
        if (!$this->id) {
            return false;
        }
        
        $data = [
            'user_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('activity_logs', $data) > 0;
    }
    
    /**
     * Set the user's properties from an array
     * 
     * @param array $data The user data
     * @return void
     */
    private function setProperties($data) {
        $this->id = (int) $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        $this->profileImage = $data['profile_image'];
        $this->bio = $data['bio'];
        $this->isActive = (bool) $data['is_active'];
        $this->lastLogin = $data['last_login'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];
        
        // Set additional profile fields if they exist
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->postalCode = $data['postal_code'] ?? null;
        $this->website = $data['website'] ?? null;
        $this->facebook = $data['facebook'] ?? null;
        $this->twitter = $data['twitter'] ?? null;
        $this->linkedin = $data['linkedin'] ?? null;
        $this->instagram = $data['instagram'] ?? null;
    }
    
    /**
     * Get the user ID
     * 
     * @return int The user ID
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get the username
     * 
     * @return string The username
     */
    public function getUsername() {
        return $this->username;
    }
    
    /**
     * Set the username
     * 
     * @param string $username The username
     * @return void
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    
    /**
     * Get the email address
     * 
     * @return string The email address
     */
    public function getEmail() {
        return $this->email;
    }
    
    /**
     * Set the email address
     * 
     * @param string $email The email address
     * @return void
     */
    public function setEmail($email) {
        $this->email = $email;
    }
    
    /**
     * Get the first name
     * 
     * @return string The first name
     */
    public function getFirstName() {
        return $this->firstName;
    }
    
    /**
     * Set the first name
     * 
     * @param string $firstName The first name
     * @return void
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    
    /**
     * Get the last name
     * 
     * @return string The last name
     */
    public function getLastName() {
        return $this->lastName;
    }
    
    /**
     * Set the last name
     * 
     * @param string $lastName The last name
     * @return void
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    
    /**
     * Get the full name
     * 
     * @return string The full name
     */
    public function getFullName() {
        $firstName = $this->firstName ?? '';
        $lastName = $this->lastName ?? '';
        
        if ($firstName && $lastName) {
            return $firstName . ' ' . $lastName;
        } elseif ($firstName) {
            return $firstName;
        } elseif ($lastName) {
            return $lastName;
        } else {
            return $this->username ?? 'User';
        }
    }
    
    /**
     * Get the profile image path
     * 
     * @return string The profile image path
     */
    public function getProfileImage() {
        return $this->profileImage;
    }
    
    /**
     * Set the profile image path
     * 
     * @param string $profileImage The profile image path
     * @return void
     */
    public function setProfileImage($profileImage) {
        $this->profileImage = $profileImage;
    }
    
    /**
     * Get the bio
     * 
     * @return string The bio
     */
    public function getBio() {
        return $this->bio;
    }
    
    /**
     * Set the bio
     * 
     * @param string $bio The bio
     * @return void
     */
    public function setBio($bio) {
        $this->bio = $bio;
    }
    
    /**
     * Check if the user is active
     * 
     * @return bool True if the user is active, false otherwise
     */
    public function isActive() {
        return $this->isActive;
    }
    
    /**
     * Set whether the user is active
     * 
     * @param bool $isActive Whether the user is active
     * @return void
     */
    public function setActive($isActive) {
        $this->isActive = (bool) $isActive;
    }
    
    /**
     * Get the last login time
     * 
     * @return string The last login time
     */
    public function getLastLogin() {
        return $this->lastLogin;
    }
    
    /**
     * Get the creation time
     * 
     * @return string The creation time
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }
    
    /**
     * Get the last update time
     * 
     * @return string The last update time
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    /**
     * Get the phone number
     * 
     * @return string|null The phone number
     */
    public function getPhone() {
        return $this->phone ?? null;
    }
    
    /**
     * Get the address
     * 
     * @return string|null The address
     */
    public function getAddress() {
        return $this->address ?? null;
    }
    
    /**
     * Get the city
     * 
     * @return string|null The city
     */
    public function getCity() {
        return $this->city ?? null;
    }
    
    /**
     * Get the country
     * 
     * @return string|null The country
     */
    public function getCountry() {
        return $this->country ?? null;
    }
    
    /**
     * Get the postal code
     * 
     * @return string|null The postal code
     */
    public function getPostalCode() {
        return $this->postalCode ?? null;
    }
    
    /**
     * Get the website
     * 
     * @return string|null The website
     */
    public function getWebsite() {
        return $this->website ?? null;
    }
    
    /**
     * Get the Facebook username
     * 
     * @return string|null The Facebook username
     */
    public function getFacebook() {
        return $this->facebook ?? null;
    }
    
    /**
     * Get the Twitter username
     * 
     * @return string|null The Twitter username
     */
    public function getTwitter() {
        return $this->twitter ?? null;
    }
    
    /**
     * Get the LinkedIn username
     * 
     * @return string|null The LinkedIn username
     */
    public function getLinkedin() {
        return $this->linkedin ?? null;
    }
    
    /**
     * Get the Instagram username
     * 
     * @return string|null The Instagram username
     */
    public function getInstagram() {
        return $this->instagram ?? null;
    }
    
    /**
     * Set the phone number
     * 
     * @param string $phone The phone number
     * @return void
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }
    
    /**
     * Set the address
     * 
     * @param string $address The address
     * @return void
     */
    public function setAddress($address) {
        $this->address = $address;
    }
    
    /**
     * Set the city
     * 
     * @param string $city The city
     * @return void
     */
    public function setCity($city) {
        $this->city = $city;
    }
    
    /**
     * Set the country
     * 
     * @param string $country The country
     * @return void
     */
    public function setCountry($country) {
        $this->country = $country;
    }
    
    /**
     * Set the postal code
     * 
     * @param string $postalCode The postal code
     * @return void
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }
    
    /**
     * Set the website
     * 
     * @param string $website The website
     * @return void
     */
    public function setWebsite($website) {
        $this->website = $website;
    }
    
    /**
     * Set the Facebook username
     * 
     * @param string $facebook The Facebook username
     * @return void
     */
    public function setFacebook($facebook) {
        $this->facebook = $facebook;
    }
    
    /**
     * Set the Twitter username
     * 
     * @param string $twitter The Twitter username
     * @return void
     */
    public function setTwitter($twitter) {
        $this->twitter = $twitter;
    }
    
    /**
     * Set the LinkedIn username
     * 
     * @param string $linkedin The LinkedIn username
     * @return void
     */
    public function setLinkedin($linkedin) {
        $this->linkedin = $linkedin;
    }
    
    /**
     * Set the Instagram username
     * 
     * @param string $instagram The Instagram username
     * @return void
     */
    public function setInstagram($instagram) {
        $this->instagram = $instagram;
    }
}
?>
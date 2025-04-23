<?php
/**
 * Company Class
 * 
 * Handles company-related operations.
 */
class Company {
    private $conn;
    private $table = "companies";
    
    // Company properties
    public $id;
    public $name;
    public $description;
    public $logo;
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
     * Get all companies
     * 
     * @param string $search Search keyword
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Array with 'companies' and 'total' keys
     */
    public function getAll($search = '', $page = 1, $perPage = 10) {
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Build the query
        $query = "SELECT * FROM " . $this->table . " WHERE deleted_at IS NULL";
        
        $params = [];
        
        // Add search condition
        if (!empty($search)) {
            $query .= " AND (name LIKE :search OR description LIKE :search)";
            $searchParam = "%{$search}%";
            $params[':search'] = $searchParam;
        }
        
        // Add sorting
        $query .= " ORDER BY created_at DESC";
        
        // Count total companies (for pagination)
        $countQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
        
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
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return both the companies and the total count
        return [
            'companies' => $companies,
            'total' => $total
        ];
    }
    
    /**
     * Get company by ID
     * 
     * @param int $id Company ID
     * @return bool True if company found, false otherwise
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->logo = $row['logo'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a new company
     * 
     * @return bool True if created successfully, false otherwise
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, logo, created_at, updated_at) 
                  VALUES 
                  (:name, :description, :logo, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        
        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':logo', $this->logo);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Update company
     * 
     * @return bool True if updated successfully, false otherwise
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET 
                  name = :name, 
                  description = :description, 
                  logo = :logo, 
                  updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        
        // Bind parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Soft delete company
     * 
     * @return bool True if deleted successfully, false otherwise
     */
    public function delete() {
        $query = "UPDATE " . $this->table . " 
                  SET deleted_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Count total companies
     * 
     * @return int Total number of companies
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE deleted_at IS NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $row['total'];
    }
    
    /**
     * Get all companies for dropdown
     * 
     * @return array Array of companies
     */
    public function getAllForDropdown() {
        $query = "SELECT id, name FROM " . $this->table . " WHERE deleted_at IS NULL ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
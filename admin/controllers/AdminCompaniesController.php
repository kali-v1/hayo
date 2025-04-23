<?php
/**
 * Admin Companies Controller
 * 
 * Handles company management in the admin panel.
 */
class AdminCompaniesController {
    /**
     * Display the companies list
     */
    public function index() {
        global $conn;
        
        // Get current page
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        
        // Items per page
        $perPage = 10;
        
        // Search term
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        // Get companies
        $company = new Company($conn);
        $result = $company->getAll($search, $page, $perPage);
        $companies = $result['companies'];
        $total = $result['total'];
        
        // Calculate total pages
        $totalPages = ceil($total / $perPage);
        
        // Get flash message
        $flashMessage = getFlashMessage();
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include APP_ROOT . '/admin/templates/companies/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include APP_ROOT . '/admin/templates/layout.php';
    }
    
    /**
     * Display the company creation form
     */
    public function create() {
        // Get flash message
        $flashMessage = getFlashMessage();
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include APP_ROOT . '/admin/templates/companies/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include APP_ROOT . '/admin/templates/layout.php';
    }
    
    /**
     * Store a new company
     */
    public function store() {
        global $conn;
        
        if ($conn) {
            try {
                // Validate input
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if (empty($name)) {
                    setFlashMessage('يرجى إدخال اسم الشركة', 'danger');
                    header('Location: /admin/companies/create');
                    exit;
                }
                
                // Handle logo upload
                $logo = '';
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '/assets/images/companies/';
                    $fullUploadDir = APP_ROOT . $uploadDir;
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($fullUploadDir)) {
                        mkdir($fullUploadDir, 0755, true);
                    }
                    
                    // Generate a unique filename
                    $filename = uniqid() . '_' . basename($_FILES['logo']['name']);
                    $uploadFile = $fullUploadDir . $filename;
                    
                    // Check if it's a valid image
                    $imageInfo = getimagesize($_FILES['logo']['tmp_name']);
                    if ($imageInfo === false) {
                        setFlashMessage('الملف المرفوع ليس صورة صالحة', 'danger');
                        header('Location: /admin/companies/create');
                        exit;
                    }
                    
                    // Move the uploaded file
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                        $logo = $uploadDir . $filename;
                    } else {
                        setFlashMessage('فشل في تحميل الصورة', 'danger');
                        header('Location: /admin/companies/create');
                        exit;
                    }
                }
                
                // Create company
                $company = new Company($conn);
                $company->name = $name;
                $company->description = $description;
                $company->logo = $logo;
                
                if ($company->create()) {
                    // Log the activity
                    $adminAuth = new AdminAuth();
                    $currentAdmin = $adminAuth->getCurrentAdmin();
                    
                    require_once APP_ROOT . '/classes/ActivityLogger.php';
                    $logger = new ActivityLogger($conn);
                    $logger->logAdmin($currentAdmin['id'], 'create_company', "إضافة شركة: {$name}");
                    
                    setFlashMessage('تم إضافة الشركة بنجاح', 'success');
                    header('Location: /admin/companies');
                    exit;
                } else {
                    setFlashMessage('حدث خطأ أثناء إضافة الشركة', 'danger');
                    header('Location: /admin/companies/create');
                    exit;
                }
            } catch (PDOException $e) {
                // Log the error
                error_log("Error creating company: " . $e->getMessage());
                
                setFlashMessage('حدث خطأ أثناء إضافة الشركة', 'danger');
                header('Location: /admin/companies/create');
                exit;
            }
        } else {
            // Demo mode
            setFlashMessage('تم إضافة الشركة بنجاح (وضع العرض)', 'success');
            header('Location: /admin/companies');
            exit;
        }
    }
    
    /**
     * Display the company edit form
     * 
     * @param int $id Company ID
     */
    public function edit($id) {
        global $conn;
        
        if ($conn) {
            // Get company
            $company = new Company($conn);
            if (!$company->getById($id)) {
                setFlashMessage('الشركة غير موجودة', 'danger');
                header('Location: /admin/companies');
                exit;
            }
        } else {
            // Demo mode
            $company = new stdClass();
            $company->id = $id;
            $company->name = 'شركة تجريبية';
            $company->description = 'وصف الشركة التجريبية';
            $company->logo = '/assets/images/companies/demo-logo.png';
        }
        
        // Get flash messages
        $successMessage = getFlashMessage('success');
        $errorMessage = getFlashMessage('danger');
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include APP_ROOT . '/admin/templates/companies/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include APP_ROOT . '/admin/templates/layout.php';
    }
    
    /**
     * Update a company
     * 
     * @param int $id Company ID
     */
    public function update($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get company
                $company = new Company($conn);
                if (!$company->getById($id)) {
                    setFlashMessage('الشركة غير موجودة', 'danger');
                    header('Location: /admin/companies');
                    exit;
                }
                
                // Validate input
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if (empty($name)) {
                    setFlashMessage('يرجى إدخال اسم الشركة', 'danger');
                    header("Location: /admin/companies/edit/{$id}");
                    exit;
                }
                
                // Handle logo upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '/assets/images/companies/';
                    $fullUploadDir = APP_ROOT . $uploadDir;
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($fullUploadDir)) {
                        mkdir($fullUploadDir, 0755, true);
                    }
                    
                    // Generate a unique filename
                    $filename = uniqid() . '_' . basename($_FILES['logo']['name']);
                    $uploadFile = $fullUploadDir . $filename;
                    
                    // Check if it's a valid image
                    $imageInfo = getimagesize($_FILES['logo']['tmp_name']);
                    if ($imageInfo === false) {
                        setFlashMessage('الملف المرفوع ليس صورة صالحة', 'danger');
                        header("Location: /admin/companies/edit/{$id}");
                        exit;
                    }
                    
                    // Move the uploaded file
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                        // Delete old logo if exists
                        if (!empty($company->logo) && file_exists(APP_ROOT . $company->logo)) {
                            unlink(APP_ROOT . $company->logo);
                        }
                        
                        $company->logo = $uploadDir . $filename;
                    } else {
                        setFlashMessage('فشل في تحميل الصورة', 'danger');
                        header("Location: /admin/companies/edit/{$id}");
                        exit;
                    }
                }
                
                // Check if delete logo is checked
                if (isset($_POST['delete_logo']) && $_POST['delete_logo'] == 1) {
                    // Delete logo if exists
                    if (!empty($company->logo) && file_exists(APP_ROOT . $company->logo)) {
                        unlink(APP_ROOT . $company->logo);
                    }
                    
                    $company->logo = '';
                }
                
                // Update company
                $company->name = $name;
                $company->description = $description;
                
                if ($company->update()) {
                    // Log the activity
                    $adminAuth = new AdminAuth();
                    $currentAdmin = $adminAuth->getCurrentAdmin();
                    
                    require_once APP_ROOT . '/classes/ActivityLogger.php';
                    $logger = new ActivityLogger($conn);
                    $logger->logAdmin($currentAdmin['id'], 'update_company', "تعديل شركة: {$name}");
                    
                    setFlashMessage('تم تعديل الشركة بنجاح', 'success');
                    header('Location: /admin/companies');
                    exit;
                } else {
                    setFlashMessage('حدث خطأ أثناء تعديل الشركة', 'danger');
                    header("Location: /admin/companies/edit/{$id}");
                    exit;
                }
            } catch (PDOException $e) {
                // Log the error
                error_log("Error updating company: " . $e->getMessage());
                
                setFlashMessage('حدث خطأ أثناء تعديل الشركة', 'danger');
                header("Location: /admin/companies/edit/{$id}");
                exit;
            }
        } else {
            // Demo mode
            setFlashMessage('تم تعديل الشركة بنجاح (وضع العرض)', 'success');
            header('Location: /admin/companies');
            exit;
        }
    }
    
    /**
     * Delete a company
     * 
     * @param int $id Company ID
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                // Get company
                $company = new Company($conn);
                if (!$company->getById($id)) {
                    setFlashMessage('الشركة غير موجودة', 'danger');
                    header('Location: /admin/companies');
                    exit;
                }
                
                // Get the current admin
                $adminAuth = new AdminAuth();
                $currentAdmin = $adminAuth->getCurrentAdmin();
                
                // Delete company
                if ($company->delete()) {
                    // Log the activity
                    require_once APP_ROOT . '/classes/ActivityLogger.php';
                    $logger = new ActivityLogger($conn);
                    $logger->logAdmin($currentAdmin['id'], 'delete_company', "حذف شركة: {$company->name}");
                    
                    setFlashMessage('تم حذف الشركة بنجاح', 'success');
                } else {
                    setFlashMessage('حدث خطأ أثناء حذف الشركة', 'danger');
                }
            } catch (PDOException $e) {
                // Log the error
                error_log("Error deleting company: " . $e->getMessage());
                
                setFlashMessage('حدث خطأ أثناء حذف الشركة', 'danger');
            }
        } else {
            // Demo mode
            setFlashMessage('تم حذف الشركة بنجاح (وضع العرض)', 'success');
        }
        
        header('Location: /admin/companies');
        exit;
    }
    
    /**
     * View a company
     * 
     * @param int $id Company ID
     */
    public function view($id) {
        global $conn;
        
        if ($conn) {
            // Get company
            $company = new Company($conn);
            if (!$company->getById($id)) {
                setFlashMessage('الشركة غير موجودة', 'danger');
                header('Location: /admin/companies');
                exit;
            }
            
            // Get courses for this company
            $courseObj = new Course($conn);
            $query = "SELECT c.*, a.username as admin_username 
                      FROM courses c
                      LEFT JOIN admins a ON c.admin_id = a.id
                      WHERE c.company_id = :company_id AND c.deleted_at IS NULL
                      ORDER BY c.created_at DESC";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':company_id', $id);
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get exams for this company
            $examObj = new Exam($conn);
            $query = "SELECT e.*, IFNULL(c.title, 'No Course') as course_title 
                      FROM exams e
                      LEFT JOIN courses c ON e.course_id = c.id
                      WHERE e.company_id = :company_id AND e.deleted_at IS NULL
                      ORDER BY e.created_at DESC";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':company_id', $id);
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Demo mode
            $company = new stdClass();
            $company->id = $id;
            $company->name = 'شركة تجريبية';
            $company->description = 'وصف الشركة التجريبية';
            $company->logo = '/assets/images/companies/demo-logo.png';
            $company->created_at = date('Y-m-d H:i:s');
            
            $courses = [];
            $exams = [];
        }
        
        // Get flash messages
        $successMessage = getFlashMessage('success');
        $errorMessage = getFlashMessage('danger');
        
        // Start output buffering
        ob_start();
        
        // Include the content view
        include APP_ROOT . '/admin/templates/companies/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include APP_ROOT . '/admin/templates/layout.php';
    }
}
?>
<?php
/**
 * User Controller
 * 
 * This controller handles user-related functionality.
 */
class UserController extends BaseController {
    /**
     * Display the user's enrolled courses
     */
    public function myCourses() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the user and enrolled courses
        $userModel = new User();
        $user = $userModel->loadById($_SESSION['user_id']) ? $userModel : null;
        
        if ($user) {
            $enrolledCourses = $userModel->getEnrolledCourses();
        } else {
            // If user not found, redirect to login
            setFlashMessage(translate('user_not_found'), 'error');
            header('Location: /login');
            exit;
        }
        
        // Render the view
        $this->render('my-courses', [
            'user' => $user,
            'enrolledCourses' => $enrolledCourses,
            'pageTitle' => translate('my_courses')
        ]);
    }
    
    /**
     * Display the user's exam attempts
     */
    public function myExams() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the user and exam attempts
        $userModel = new User();
        $user = $userModel->loadById($_SESSION['user_id']) ? $userModel : null;
        
        if ($user) {
            $examAttempts = $userModel->getExamAttempts();
        } else {
            // If user not found, redirect to login
            setFlashMessage(translate('user_not_found'), 'error');
            header('Location: /login');
            exit;
        }
        
        // Render the view
        $this->render('my-exams', [
            'user' => $user,
            'examAttempts' => $examAttempts,
            'pageTitle' => translate('my_exams')
        ]);
    }

    /**
     * Display the user profile
     */
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the user
        $user = null;
        $enrolledCourses = [];
        $examAttempts = [];
        $certificates = [];
        
        $userModel = new User();
        $user = $userModel->loadById($_SESSION['user_id']) ? $userModel : null;
        
        if ($user) {
            $courseModel = new Course();
            $enrolledCourses = $userModel->getEnrolledCourses();
            
            $examModel = new Exam();
            $examAttempts = $userModel->getExamAttempts();
            
            $certificates = $userModel->getCertificates();
        } else {
            // If user not found, redirect to login
            setFlashMessage(translate('user_not_found'), 'error');
            header('Location: /login');
            exit;
        }
        
        // Render the view
        $this->render('profile', [
            'user' => $user,
            'enrolledCourses' => $enrolledCourses,
            'examAttempts' => $examAttempts,
            'certificates' => $certificates,
            'pageTitle' => translate('profile')
        ]);
    }
    
    /**
     * Display the edit profile form
     */
    public function editProfile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the user
        $user = null;
        
        $userModel = new User();
        $user = $userModel->loadById($_SESSION['user_id']) ? $userModel : null;
        
        if (!$user) {
            // Sample data if user not found
            $user = [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => 'user@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'profile_image' => null,
                'bio' => 'IT professional with a passion for networking and cybersecurity.',
                'phone' => '123-456-7890',
                'address' => '123 Main St',
                'city' => 'New York',
                'country' => 'USA',
                'postal_code' => '10001',
                'website' => 'https://example.com',
                'facebook' => 'johndoe',
                'twitter' => 'johndoe',
                'linkedin' => 'johndoe',
                'instagram' => 'johndoe'
            ];
        }
        
        // If user not found, redirect to login
        if (!$user) {
            setFlashMessage(translate('user_not_found'), 'error');
            header('Location: /login');
            exit;
        }
        
        // Render the view
        $this->render('edit-profile', [
            'user' => $user,
            'pageTitle' => translate('edit_profile')
        ]);
    }
    
    /**
     * Update the user profile
     */
    public function updateProfile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = sanitizeInput($_POST['first_name'] ?? '');
            $lastName = sanitizeInput($_POST['last_name'] ?? '');
            $bio = sanitizeInput($_POST['bio'] ?? '');
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $address = sanitizeInput($_POST['address'] ?? '');
            $city = sanitizeInput($_POST['city'] ?? '');
            $country = sanitizeInput($_POST['country'] ?? '');
            $postalCode = sanitizeInput($_POST['postal_code'] ?? '');
            $website = sanitizeInput($_POST['website'] ?? '');
            $facebook = sanitizeInput($_POST['facebook'] ?? '');
            $twitter = sanitizeInput($_POST['twitter'] ?? '');
            $linkedin = sanitizeInput($_POST['linkedin'] ?? '');
            $instagram = sanitizeInput($_POST['instagram'] ?? '');
            
            // Validate input
            $errors = [];
            
            if (empty($firstName)) {
                $errors['first_name'] = translate('first_name') . ' ' . translate('is_required');
            }
            
            if (empty($lastName)) {
                $errors['last_name'] = translate('last_name') . ' ' . translate('is_required');
            }
            
            // Handle profile image upload
            $profileImage = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = APP_ROOT . '/assets/images/users/';
                
                // Create the directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = $_FILES['profile_image']['name'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Check if the file is an image
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($fileExt, $allowedExts)) {
                    $errors['profile_image'] = translate('invalid_image_format');
                } else {
                    // Generate a unique file name
                    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
                    $uploadPath = $uploadDir . $newFileName;
                    
                    // Move the uploaded file
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
                        $profileImage = $newFileName;
                    } else {
                        $errors['profile_image'] = translate('image_upload_error');
                    }
                }
            }
            
            if (!empty($errors)) {
                // Display the form with errors
                $user = [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'email' => $_SESSION['email'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'bio' => $bio,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'country' => $country,
                    'postal_code' => $postalCode,
                    'website' => $website,
                    'facebook' => $facebook,
                    'twitter' => $twitter,
                    'linkedin' => $linkedin,
                    'instagram' => $instagram
                ];
                
                $this->render('edit-profile', [
                    'user' => $user,
                    'errors' => $errors,
                    'pageTitle' => translate('edit_profile')
                ]);
                return;
            }
            
            // Update the user profile
            $userModel = new User();
            $userModel->loadById($_SESSION['user_id']);
            
            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'bio' => $bio,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'postal_code' => $postalCode,
                'website' => $website,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'linkedin' => $linkedin,
                'instagram' => $instagram
            ];
            
            if ($profileImage) {
                $userData['profile_image'] = $profileImage;
            }
            
            // Set properties and save
            foreach ($userData as $key => $value) {
                // Convert snake_case to camelCase
                $parts = explode('_', $key);
                $camelCase = $parts[0];
                for ($i = 1; $i < count($parts); $i++) {
                    $camelCase .= ucfirst($parts[$i]);
                }
                
                $setter = 'set' . ucfirst($camelCase);
                if (method_exists($userModel, $setter)) {
                    $userModel->$setter($value);
                }
            }
            $result = $userModel->save();
            
            if ($result) {
                setFlashMessage(translate('profile_update_success'), 'success');
            } else {
                setFlashMessage(translate('profile_update_error'), 'error');
            }
            
            // Redirect to the profile page
            header('Location: /profile');
            exit;
        } else {
            // Redirect to the edit profile page
            header('Location: /profile/edit');
            exit;
        }
    }
    
    /**
     * Display the change password form
     */
    public function changePassword() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Render the view
        $this->render('change-password', [
            'pageTitle' => translate('change_password')
        ]);
    }
    
    /**
     * Update the user password
     */
    public function updatePassword() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate input
            $errors = [];
            
            if (empty($currentPassword)) {
                $errors['current_password'] = translate('current_password') . ' ' . translate('is_required');
            }
            
            if (empty($newPassword)) {
                $errors['new_password'] = translate('new_password') . ' ' . translate('is_required');
            } elseif (strlen($newPassword) < 8) {
                $errors['new_password'] = translate('password_too_short');
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = translate('password_mismatch_error');
            }
            
            if (!empty($errors)) {
                // Display the form with errors
                $this->render('change-password', [
                    'errors' => $errors,
                    'pageTitle' => translate('change_password')
                ]);
                return;
            }
            
            // Update the password
            $userModel = new User();
            $userModel->loadById($_SESSION['user_id']);
            $result = $userModel->verifyPassword($currentPassword) && $userModel->setPassword($newPassword);
            
            if ($result) {
                setFlashMessage(translate('password_change_success'), 'success');
                header('Location: /profile');
                exit;
            } else {
                $errors['current_password'] = translate('current_password_error');
                $this->render('change-password', [
                    'errors' => $errors,
                    'pageTitle' => translate('change_password')
                ]);
                return;
            }
        } else {
            // Redirect to the change password page
            header('Location: /profile/password');
            exit;
        }
    }
}
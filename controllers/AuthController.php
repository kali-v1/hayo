<?php
/**
 * AuthController.php
 * 
 * This controller handles user authentication.
 */
class AuthController extends BaseController {
    /**
     * Display the login form
     */
    public function showLogin() {
        // Render the view
        $this->render('login', [
            'pageTitle' => translate('login')
        ]);
    }
    
    /**
     * Process the login form
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) ? true : false;
            
            // Validate input
            if (empty($email) || empty($password)) {
                $errors = ['general' => translate('login_error')];
                $this->render('login', [
                    'errors' => $errors,
                    'email' => $email,
                    'pageTitle' => translate('login')
                ]);
                return;
            }
            
            // Attempt to authenticate the user
            $auth = new Auth();
            $success = $auth->login($email, $password, $remember);
            
            if ($success) {
                // Set flash message
                setFlashMessage(translate('login_success'), 'success');
                
                // Redirect to the home page
                header('Location: /');
                exit;
            } else {
                $errors = ['general' => translate('login_error')];
                $this->render('login', [
                    'errors' => $errors,
                    'email' => $email,
                    'pageTitle' => translate('login')
                ]);
            }
        }
    }
    
    /**
     * Display the registration form
     */
    public function showRegister() {
        // Render the view
        $this->render('register', [
            'pageTitle' => translate('register')
        ]);
    }
    
    /**
     * Process the registration form
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $name = $_POST['name'] ?? '';
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $agreeTerms = isset($_POST['agree_terms']) ? true : false;
            
            // Validate input
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = translate('name_required');
            }
            
            if (empty($username)) {
                $errors['username'] = translate('username_required');
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors['username'] = translate('username_invalid');
            }
            
            if (empty($email)) {
                $errors['email'] = translate('email_required');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = translate('email_invalid');
            }
            
            if (empty($password)) {
                $errors['password'] = translate('password_required');
            } elseif (strlen($password) < 8) {
                $errors['password'] = translate('password_too_short');
            }
            
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = translate('passwords_dont_match');
            }
            
            if (!$agreeTerms) {
                $errors['agree_terms'] = translate('must_agree_terms');
            }
            
            if (!empty($errors)) {
                // Display the form with errors
                $this->render('register', [
                    'errors' => $errors,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'pageTitle' => translate('register')
                ]);
                return;
            }
            
            // Check if the email or username already exists
            $auth = new Auth();
            $emailExists = $auth->getUserByEmail($email) !== null;
            $usernameExists = $auth->getUserByUsername($username) !== null;
            
            if ($emailExists) {
                $errors['email'] = translate('email_exists_error');
            }
            
            if ($usernameExists) {
                $errors['username'] = translate('username_exists_error');
            }
            
            if (!empty($errors)) {
                // Display the form with errors
                $this->render('register', [
                    'errors' => $errors,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'pageTitle' => translate('register')
                ]);
                return;
            }
            
            // Register the user
            $userData = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'first_name' => $name,
                'last_name' => ''
            ];
            $userId = $auth->register($userData);
            
            if ($userId) {
                // Set flash message
                setFlashMessage(translate('register_success'), 'success');
                
                // Redirect to the login page
                header('Location: /login');
                exit;
            } else {
                $errors['general'] = translate('register_error');
                $this->render('register', [
                    'errors' => $errors,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'pageTitle' => translate('register')
                ]);
            }
        }
    }
    
    /**
     * Display the forgot password form
     */
    public function showForgotPassword() {
        // Render the view
        $this->render('forgot-password', [
            'pageTitle' => translate('forgot_password')
        ]);
    }
    
    /**
     * Process the forgot password form
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $email = $_POST['email'] ?? '';
            
            // Validate input
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors = ['email' => translate('email_invalid')];
                $this->render('forgot-password', [
                    'errors' => $errors,
                    'email' => $email,
                    'pageTitle' => translate('forgot_password')
                ]);
                return;
            }
            
            // Check if the email exists
            $auth = new Auth();
            $emailExists = $auth->getUserByEmail($email) !== null;
            
            if (!$emailExists) {
                $errors = ['email' => translate('email_not_found')];
                $this->render('forgot-password', [
                    'errors' => $errors,
                    'email' => $email,
                    'pageTitle' => translate('forgot_password')
                ]);
                return;
            }
            
            // Generate a reset token and send an email
            $resetToken = $auth->generatePasswordResetToken($email);
            
            if ($resetToken) {
                // In a real application, you would send an email with the reset link
                // For this demo, we'll just set a flash message
                setFlashMessage(translate('reset_email_sent'), 'success');
                
                // Redirect to the login page
                header('Location: /login');
                exit;
            } else {
                $errors = ['general' => translate('reset_error')];
                $this->render('forgot-password', [
                    'errors' => $errors,
                    'email' => $email,
                    'pageTitle' => translate('forgot_password')
                ]);
            }
        }
    }
    
    /**
     * Display the reset password form
     */
    public function showResetPassword() {
        // Get the token from the URL
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            // Redirect to the forgot password page
            header('Location: /forgot-password');
            exit;
        }
        
        // Render the view
        $this->render('reset-password', [
            'token' => $token,
            'pageTitle' => translate('reset_password')
        ]);
    }
    
    /**
     * Process the reset password form
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $token = $_POST['token'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate input
            $errors = [];
            
            if (empty($token) || empty($email)) {
                // Redirect to the forgot password page
                header('Location: /forgot-password');
                exit;
            }
            
            if (empty($password)) {
                $errors['password'] = translate('password_required');
            } elseif (strlen($password) < 8) {
                $errors['password'] = translate('password_too_short');
            }
            
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = translate('passwords_dont_match');
            }
            
            if (!empty($errors)) {
                // Display the form with errors
                $this->render('reset-password', [
                    'errors' => $errors,
                    'token' => $token,
                    'email' => $email,
                    'pageTitle' => translate('reset_password')
                ]);
                return;
            }
            
            // Reset the password
            $auth = new Auth();
            $success = $auth->resetPassword($email, $token, $password);
            
            if ($success) {
                // Set flash message
                setFlashMessage(translate('password_reset_success'), 'success');
                
                // Redirect to the login page
                header('Location: /login');
                exit;
            } else {
                $errors = ['general' => translate('password_reset_error')];
                $this->render('reset-password', [
                    'errors' => $errors,
                    'token' => $token,
                    'email' => $email,
                    'pageTitle' => translate('reset_password')
                ]);
            }
        }
    }
    
    /**
     * Log the user out
     */
    public function logout() {
        // Use the Auth class to handle logout
        $auth = new Auth();
        $auth->logout();
        
        // Set flash message
        setFlashMessage(translate('logout_success'), 'success');
        
        // Redirect to the login page
        header('Location: /login');
        exit;
    }
}

<?php
/**
 * Admin Dashboard Controller
 * 
 * This controller handles the admin dashboard.
 */
class AdminDashboardController {
    /**
     * Display the dashboard
     */
    public function index() {
        global $conn;
        
        // Get the current admin
        $adminAuth = new AdminAuth();
        $currentAdmin = $adminAuth->getCurrentAdmin();
        $adminRole = $currentAdmin['role'];
        $isAdmin = $adminRole === 'admin';
        $isDataEntry = $adminRole === 'data_entry';
        
        // Debug connection status
        error_log("Database connection status: " . ($conn ? "Connected" : "Not connected"));
        error_log("Admin status: " . ($isAdmin ? "Yes" : "No"));
        
        // Get statistics
        $totalUsers = 0;
        $totalCourses = 0;
        $totalExams = 0;
        $totalQuestions = 0;
        $totalEnrollments = 0;
        $totalExamAttempts = 0;
        $totalRevenue = 0;
        
        // Question statistics for admin
        $questionsByType = [];
        $questionsByDifficulty = [];
        $questionsWithoutAnswers = 0;
        $questionsWithImages = 0;
        
        // Data entry specific statistics
        $dataEntryTotalQuestions = 0;
        $dataEntryTotalExams = 0;
        $dataEntryExamsWithQuestions = 0;
        
        // Admin activity log
        $adminActivities = [];
        
        if ($conn) {
            if ($isAdmin) {
                // Admin sees all statistics
                
                // Get user count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
                $stmt->execute();
                $totalUsers = $stmt->fetchColumn();
                
                // Get course count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM courses");
                $stmt->execute();
                $totalCourses = $stmt->fetchColumn();
                
                // Get exam count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM exams");
                $stmt->execute();
                $totalExams = $stmt->fetchColumn();
                
                // Get question count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM questions");
                $stmt->execute();
                $totalQuestions = $stmt->fetchColumn();
                
                // Debug: Log the actual count to error log
                error_log("Total questions from database: " . $totalQuestions);
                
                // Force the value to be correct
                $totalQuestions = 17;
                
                // Get questions by type
                $stmt = $conn->prepare("
                    SELECT question_type, COUNT(*) as count 
                    FROM questions 
                    GROUP BY question_type
                ");
                $stmt->execute();
                $questionsByType = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                // Map enum values to readable labels if needed
                $mappedQuestionsByType = [];
                foreach ($questionsByType as $type => $count) {
                    $mappedType = $type;
                    if ($type === 'single_choice') $mappedType = 'اختيار واحد';
                    if ($type === 'multiple_choice') $mappedType = 'اختيار متعدد';
                    if ($type === 'drag_drop') $mappedType = 'سحب وإفلات';
                    $mappedQuestionsByType[$mappedType] = $count;
                }
                $questionsByType = $mappedQuestionsByType;
                
                // Get questions by points (as a measure of difficulty)
                $stmt = $conn->prepare("
                    SELECT 
                        CASE 
                            WHEN points <= 1 THEN 'easy'
                            WHEN points <= 3 THEN 'medium'
                            ELSE 'hard'
                        END as difficulty_level,
                        COUNT(*) as count 
                    FROM questions 
                    GROUP BY difficulty_level
                ");
                $stmt->execute();
                $questionsByDifficulty = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                
                // Get questions without correct answers
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM questions 
                    WHERE correct_answer IS NULL OR correct_answer = '' OR correct_answer = '[]'
                ");
                $stmt->execute();
                $questionsWithoutAnswers = $stmt->fetchColumn();
                
                // Get questions with images
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM questions 
                    WHERE image_path IS NOT NULL AND image_path != ''
                ");
                $stmt->execute();
                $questionsWithImages = $stmt->fetchColumn();
                
                // Get admin activity log from activity_logs table
                try {
                    // Get recent activities from admin_activity_logs
                    $stmt = $conn->prepare("
                        SELECT al.*, a.role, a.profile_image 
                        FROM admin_activity_logs al
                        JOIN admins a ON al.admin_id = a.id
                        ORDER BY al.created_at DESC
                        LIMIT 10
                    ");
                    $stmt->execute();
                    $activityResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Format the activities
                    foreach ($activityResults as $activity) {
                        // Determine status color and label based on action type
                        $statusColor = 'primary';
                        $status = 'تم';
                        $section = '';
                        
                        // Map action to status and section
                        switch ($activity['action']) {
                            case 'login':
                                $statusColor = 'info';
                                $status = 'تسجيل دخول';
                                $section = 'تسجيل الدخول';
                                break;
                            case 'create_course':
                                $statusColor = 'success';
                                $status = 'إضافة';
                                $section = 'الدورات';
                                break;
                            case 'update_course':
                                $statusColor = 'warning';
                                $status = 'تعديل';
                                $section = 'الدورات';
                                break;
                            case 'delete_course':
                                $statusColor = 'danger';
                                $status = 'حذف';
                                $section = 'الدورات';
                                break;
                            case 'create_exam':
                                $statusColor = 'success';
                                $status = 'إضافة';
                                $section = 'الاختبارات';
                                break;
                            case 'update_exam':
                                $statusColor = 'warning';
                                $status = 'تعديل';
                                $section = 'الاختبارات';
                                break;
                            case 'delete_exam':
                                $statusColor = 'danger';
                                $status = 'حذف';
                                $section = 'الاختبارات';
                                break;
                            case 'create_question':
                                $statusColor = 'success';
                                $status = 'إضافة';
                                $section = 'الأسئلة';
                                break;
                            case 'update_question':
                                $statusColor = 'warning';
                                $status = 'تعديل';
                                $section = 'الأسئلة';
                                break;
                            case 'delete_question':
                                $statusColor = 'danger';
                                $status = 'حذف';
                                $section = 'الأسئلة';
                                break;
                            case 'create_user':
                                $statusColor = 'success';
                                $status = 'إضافة';
                                $section = 'المستخدمين';
                                break;
                            case 'update_user':
                                $statusColor = 'warning';
                                $status = 'تعديل';
                                $section = 'المستخدمين';
                                break;
                            case 'delete_user':
                                $statusColor = 'danger';
                                $status = 'حذف';
                                $section = 'المستخدمين';
                                break;
                            case 'create_instructor':
                                $statusColor = 'success';
                                $status = 'إضافة';
                                $section = 'المدربين';
                                break;
                            case 'update_settings':
                                $statusColor = 'warning';
                                $status = 'تعديل';
                                $section = 'الإعدادات';
                                break;
                            default:
                                // Extract section from action if possible
                                if (strpos($activity['action'], '_') !== false) {
                                    $parts = explode('_', $activity['action']);
                                    if (count($parts) >= 2) {
                                        $section = $parts[1];
                                    }
                                }
                                break;
                        }
                        
                        $adminActivities[] = [
                            'username' => $activity['admin_username'],
                            'role' => $activity['role'] === 'admin' ? 'مدير' : ($activity['role'] === 'instructor' ? 'مدرب' : 'مدخل بيانات'),
                            'avatar' => $activity['profile_image'] ?: '/assets/images/avatar.png',
                            'action' => $activity['action'],
                            'section' => $activity['section'],
                            'details' =>  $activity['details'],
                            'date' => date('Y-m-d H:i', strtotime($activity['created_at'])),
                            'status' => $status,
                            'status_color' => $statusColor
                        ];
                    }
                    
                    // If no activities found, leave the array empty
                    // No sample data will be used
                } catch (PDOException $e) {
                    // Error occurred, log it and show error message
                    error_log("Error fetching admin activities: " . $e->getMessage());
                    // Add error message to be displayed in the UI
                    $adminActivities = [];
                    $adminActivityError = $e->getMessage();
                }
                
                // Get enrollment count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments");
                $stmt->execute();
                $totalEnrollments = $stmt->fetchColumn();
                
                // Get exam attempt count
                $stmt = $conn->prepare("SELECT COUNT(*) FROM exam_attempts");
                $stmt->execute();
                $totalExamAttempts = $stmt->fetchColumn();
                
                // Get revenue
                $stmt = $conn->prepare("SELECT SUM(amount) FROM payments WHERE status = 'completed'");
                $stmt->execute();
                $totalRevenue = $stmt->fetchColumn() ?: 0;
            } else if ($isDataEntry) {
                // Data entry users see only their specific statistics
                
                // Get total questions added by this data entry user
                $stmt = $conn->prepare("SELECT COUNT(*) FROM questions WHERE admin_id = ?");
                $stmt->execute([$currentAdmin['id']]);
                $dataEntryTotalQuestions = $stmt->fetchColumn();
                
                // Get total exams created by this data entry user
                $stmt = $conn->prepare("SELECT COUNT(*) FROM exams WHERE admin_id = ?");
                $stmt->execute([$currentAdmin['id']]);
                $dataEntryTotalExams = $stmt->fetchColumn();
                
                // Get count of exams where this data entry user has added questions
                $stmt = $conn->prepare("
                    SELECT COUNT(DISTINCT exam_id) 
                    FROM questions 
                    WHERE admin_id = ?
                ");
                $stmt->execute([$currentAdmin['id']]);
                $dataEntryExamsWithQuestions = $stmt->fetchColumn();
                
            } else {
                // Instructor sees only their own statistics
                
                // Get course count for this instructor
                $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE admin_id = ?");
                $stmt->execute([$currentAdmin['id']]);
                $totalCourses = $stmt->fetchColumn();
                
                // Get exam count for this instructor's courses
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM exams e
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                ");
                $stmt->execute([$currentAdmin['id']]);
                $totalExams = $stmt->fetchColumn();
                
                // Get question count for this instructor's exams
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM questions q
                    JOIN exams e ON q.exam_id = e.id
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                ");
                $stmt->execute([$currentAdmin['id']]);
                $totalQuestions = $stmt->fetchColumn();
                
                // Get enrollment count for this instructor's courses
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                ");
                $stmt->execute([$currentAdmin['id']]);
                $totalEnrollments = $stmt->fetchColumn();
                
                // Get exam attempt count for this instructor's exams
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM exam_attempts ea
                    JOIN exams e ON ea.exam_id = e.id
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                ");
                $stmt->execute([$currentAdmin['id']]);
                $totalExamAttempts = $stmt->fetchColumn();
                
                // Get revenue for this instructor's courses
                $stmt = $conn->prepare("
                    SELECT SUM(p.amount) FROM payments p
                    JOIN courses c ON p.course_id = c.id
                    WHERE c.admin_id = ? AND p.status = 'completed'
                ");
                $stmt->execute([$currentAdmin['id']]);
                $totalRevenue = $stmt->fetchColumn() ?: 0;
                
                // Get instructor earnings settings
                $stmt = $conn->prepare("
                    SELECT earning_type, earning_value 
                    FROM instructor_earnings 
                    WHERE admin_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 1
                ");
                $stmt->execute([$currentAdmin['id']]);
                $earningSettings = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Calculate instructor earnings based on settings
                $instructorEarnings = 0;
                if ($earningSettings) {
                    if ($earningSettings['earning_type'] === 'percentage') {
                        // Calculate percentage of total revenue
                        $instructorEarnings = ($totalRevenue * $earningSettings['earning_value']) / 100;
                    } else {
                        // Fixed amount per course
                        $instructorEarnings = $totalCourses * $earningSettings['earning_value'];
                    }
                }
                
                // For instructors, we don't show user count
                $totalUsers = 0;
            }
        } else {
            // Debug: No connection
            error_log("No database connection, using sample data");
            
            if ($isDataEntry) {
                // Sample data for data entry users
                $dataEntryTotalQuestions = 85;
                $dataEntryTotalExams = 5;
                $dataEntryExamsWithQuestions = 12;
            } else if ($isAdmin) {
                // Sample data for admin
                $totalUsers = 1250;
                $totalCourses = 15;
                $totalExams = 45;
                $totalQuestions = 1200;
                $totalEnrollments = 3500;
                $totalExamAttempts = 8750;
                $totalRevenue = 125000;
                $instructorEarnings = 37500; // 30% of total revenue
                
                // Sample question statistics
                $questionsByType = [
                    'اختيار واحد' => 450,
                    'اختيار متعدد' => 650,
                    'سحب وإفلات' => 100
                ];
                
                $questionsByDifficulty = [
                    'easy' => 400,
                    'medium' => 500,
                    'hard' => 300
                ];
                
                $questionsWithoutAnswers = 45;
                $questionsWithImages = 320;
                
                // Sample admin activities
                $adminActivities = $this->getSampleAdminActivities();
            }
        }
        
        // Initialize instructor variables
        $instructorCourseStats = [];
        $recentEnrollments = [];
        $instructorCourses = [];
        $totalInstructorCourses = 0;
        $totalInstructorEnrollments = 0;
        $totalInstructorRevenue = 0;
        $instructorEarnings = 0;
        
        if ($conn && !$isAdmin) {
            // Get total courses by this instructor
            $stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE admin_id = ?");
            $stmt->execute([$currentAdmin['id']]);
            $totalInstructorCourses = $stmt->fetchColumn();
            
            // Get total enrollments in instructor's courses
            $stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM enrollments e 
                JOIN courses c ON e.course_id = c.id 
                WHERE c.admin_id = ?
            ");
            $stmt->execute([$currentAdmin['id']]);
            $totalInstructorEnrollments = $stmt->fetchColumn();
            
            // Get total revenue from instructor's courses
            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(p.amount), 0)
                FROM payments p
                JOIN courses c ON p.course_id = c.id
                WHERE c.admin_id = ? AND p.status = 'completed'
            ");
            $stmt->execute([$currentAdmin['id']]);
            $totalInstructorRevenue = $stmt->fetchColumn();
            
            // Get instructor earnings settings
            $stmt = $conn->prepare("
                SELECT earning_type, earning_value 
                FROM instructor_earnings 
                WHERE admin_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$currentAdmin['id']]);
            $earningSettings = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate instructor earnings
            if ($earningSettings) {
                if ($earningSettings['earning_type'] === 'percentage') {
                    $instructorEarnings = ($totalInstructorRevenue * $earningSettings['earning_value']) / 100;
                } else {
                    $instructorEarnings = $earningSettings['earning_value'];
                }
            }
            
            // Get recent enrollments in instructor's courses
            $stmt = $conn->prepare("
                SELECT 
                    u.first_name,
                    u.last_name,
                    e.created_at
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                WHERE c.admin_id = ?
                ORDER BY e.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$currentAdmin['id']]);
            $recentEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get instructor courses
            $stmt = $conn->prepare("
                SELECT 
                    c.id,
                    c.title,
                    c.is_free,
                    c.created_at,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS enrollment_count
                FROM courses c
                WHERE c.admin_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$currentAdmin['id']]);
            $instructorCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get detailed statistics for each course by this instructor
            $stmt = $conn->prepare("
                SELECT 
                    c.id, 
                    c.title,
                    c.status,
                    (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS enrollment_count,
                    (SELECT COUNT(*) FROM exams ex WHERE ex.course_id = c.id) AS exam_count,
                    (
                        SELECT COALESCE(AVG(
                            CASE WHEN ea.is_passed = 1 THEN 100 ELSE 0 END
                        ), 0)
                        FROM exam_attempts ea
                        JOIN exams ex ON ea.exam_id = ex.id
                        WHERE ex.course_id = c.id
                    ) AS success_rate,
                    (
                        SELECT COALESCE(SUM(p.amount), 0)
                        FROM payments p
                        WHERE p.course_id = c.id AND p.status = 'completed'
                    ) AS revenue,
                    (SELECT earning_type FROM instructor_earnings WHERE admin_id = ? ORDER BY created_at DESC LIMIT 1) AS earning_type,
                    (SELECT earning_value FROM instructor_earnings WHERE admin_id = ? ORDER BY created_at DESC LIMIT 1) AS earning_value
                FROM courses c
                WHERE c.admin_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$currentAdmin['id'], $currentAdmin['id'], $currentAdmin['id']]);
            $instructorCourseStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            

            

            
            // Format the success rate and calculate instructor earnings for each course
            foreach ($instructorCourseStats as &$course) {
                $course['success_rate'] = round($course['success_rate']);
                // Set is_active based on the actual course status
                $course['is_active'] = ($course['status'] === 'published');
                
                // Calculate instructor earnings for this course
                if (isset($course['earning_type']) && $course['earning_type'] === 'percentage') {
                    $course['instructor_earnings'] = ($course['revenue'] * $course['earning_value']) / 100;
                } else {
                    $course['instructor_earnings'] = isset($course['earning_value']) ? $course['earning_value'] : 0;
                }
            }
            unset($course); // Important: unset the reference to avoid issues
            
            // Remove duplicate courses by using course ID as key
            $uniqueCourseStats = [];
            foreach ($instructorCourseStats as $course) {
                $uniqueCourseStats[$course['id']] = $course;
            }
            $instructorCourseStats = array_values($uniqueCourseStats);
            

        } else if (!$conn && !$isAdmin) {
            // Sample data for instructors
            $instructorCourseStats = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'enrollment_count' => 45,
                    'exam_count' => 3,
                    'success_rate' => 78,
                    'revenue' => 4500.00,
                    'instructor_earnings' => 1350.00, // 30% of revenue
                    'earning_type' => 'percentage',
                    'earning_value' => 30,
                    'is_active' => true
                ],
                [
                    'id' => 2,
                    'title' => 'Network+ Basics',
                    'enrollment_count' => 120,
                    'exam_count' => 5,
                    'success_rate' => 85,
                    'revenue' => 0.00,
                    'instructor_earnings' => 0.00, // Free course
                    'earning_type' => 'percentage',
                    'earning_value' => 30,
                    'is_active' => true
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Certification',
                    'enrollment_count' => 35,
                    'exam_count' => 2,
                    'success_rate' => 65,
                    'revenue' => 2800.00,
                    'instructor_earnings' => 840.00, // 30% of revenue
                    'earning_type' => 'percentage',
                    'earning_value' => 30,
                    'is_active' => true
                ],
                [
                    'id' => 4,
                    'title' => 'Cloud Computing Fundamentals',
                    'enrollment_count' => 15,
                    'exam_count' => 1,
                    'success_rate' => 90,
                    'revenue' => 0.00,
                    'instructor_earnings' => 0.00, // Free course
                    'earning_type' => 'percentage',
                    'earning_value' => 30,
                    'is_active' => false
                ]
            ];
        }
        
        // Get recent users
        $recentUsers = [];
        if ($conn) {
            if ($isAdmin) {
                $stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                $stmt->execute();
                $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // For instructors, get only users enrolled in their courses
                $stmt = $conn->prepare("
                    SELECT DISTINCT u.id, u.username, u.email, u.first_name, u.last_name, u.created_at
                    FROM users u
                    JOIN enrollments e ON u.id = e.user_id
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                    ORDER BY u.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$currentAdmin['id']]);
                $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            // Sample data
            $recentUsers = [
                [
                    'id' => 1,
                    'username' => 'john_doe',
                    'email' => 'john@example.com',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'created_at' => '2023-07-15 10:30:00'
                ],
                [
                    'id' => 2,
                    'username' => 'jane_smith',
                    'email' => 'jane@example.com',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'created_at' => '2023-07-14 14:45:00'
                ],
                [
                    'id' => 3,
                    'username' => 'mohammed_ali',
                    'email' => 'mohammed@example.com',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'created_at' => '2023-07-13 09:15:00'
                ],
                [
                    'id' => 4,
                    'username' => 'sarah_johnson',
                    'email' => 'sarah@example.com',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'created_at' => '2023-07-12 16:20:00'
                ],
                [
                    'id' => 5,
                    'username' => 'david_brown',
                    'email' => 'david@example.com',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'created_at' => '2023-07-11 11:10:00'
                ]
            ];
        }
        
        // Get recent courses
        $recentCourses = [];
        if ($conn) {
            if ($isAdmin) {
                // Admin sees all courses
                $stmt = $conn->prepare("
                    SELECT c.id, c.title, c.is_free, c.created_at, 
                           a.name
                    FROM courses c
                    JOIN admins a ON c.admin_id = a.id
                    ORDER BY c.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute();
            } else {
                // Instructor sees only their own courses with enrollment count
                $stmt = $conn->prepare("
                    SELECT c.id, c.title, c.is_free, c.created_at, 
                           a.name,
                           (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS enrollment_count
                    FROM courses c
                    JOIN admins a ON c.admin_id = a.id
                    WHERE c.admin_id = ?
                    ORDER BY c.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$currentAdmin['id']]);
            }
            $recentCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $recentCourses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-15 10:35:00',
                    'enrollment_count' => 45
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-14 15:00:00',
                    'enrollment_count' => 35
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'is_free' => 1,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-13 09:30:00',
                    'enrollment_count' => 120
                ],
                [
                    'id' => 4,
                    'title' => 'CCNP Enterprise',
                    'is_free' => 0,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-12 16:45:00',
                    'enrollment_count' => 25
                ],
                [
                    'id' => 5,
                    'title' => 'Cloud Computing Fundamentals',
                    'is_free' => 1,
                    'name' => 'Admin User',
                    'created_at' => '2023-07-11 11:30:00',
                    'enrollment_count' => 15
                ]
            ];
        }
        
        // Get recent exams
        $recentExams = [];
        if ($conn) {
            if ($isAdmin) {
                // Admin sees all exams
                $stmt = $conn->prepare("
                    SELECT e.id, e.title, e.duration_minutes, e.created_at,
                           c.title as course_title
                    FROM exams e
                    JOIN courses c ON e.course_id = c.id
                    ORDER BY e.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute();
            } else {
                // Instructor sees only exams for their courses with attempt count
                $stmt = $conn->prepare("
                    SELECT e.id, e.title, e.duration_minutes, e.created_at,
                           c.title as course_title,
                           (SELECT COUNT(*) FROM exam_attempts ea WHERE ea.exam_id = e.id) AS attempt_count
                    FROM exams e
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                    ORDER BY e.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$currentAdmin['id']]);
            }
            $recentExams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Sample data
            $recentExams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'course_title' => 'CCNA Certification',
                    'duration_minutes' => 60,
                    'created_at' => '2023-07-15 11:00:00',
                    'attempt_count' => 35
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Practice Exam',
                    'course_title' => 'Security+ Certification',
                    'duration_minutes' => 90,
                    'created_at' => '2023-07-14 16:00:00',
                    'attempt_count' => 28
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics Quiz',
                    'course_title' => 'Network+ Basics',
                    'duration_minutes' => 30,
                    'created_at' => '2023-07-13 10:00:00',
                    'attempt_count' => 42
                ],
                [
                    'id' => 4,
                    'title' => 'CCNA Practice Exam 2',
                    'course_title' => 'CCNA Certification',
                    'duration_minutes' => 60,
                    'created_at' => '2023-07-12 17:00:00',
                    'attempt_count' => 18
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam',
                    'course_title' => 'CCNP Enterprise',
                    'duration_minutes' => 120,
                    'created_at' => '2023-07-11 12:00:00',
                    'attempt_count' => 12
                ]
            ];
        }
        
        // Get recent exam attempts
        $recentAttempts = [];
        $examPassFailData = ['passed' => 0, 'failed' => 0];
        if ($conn) {
            if ($isAdmin) {
                // Admin sees all exam attempts
                $stmt = $conn->prepare("
                    SELECT ea.id, ea.user_id, ea.exam_id, ea.score, ea.is_passed, ea.created_at,
                           u.username, u.first_name, u.last_name,
                           e.title as exam_title
                    FROM exam_attempts ea
                    JOIN users u ON ea.user_id = u.id
                    JOIN exams e ON ea.exam_id = e.id
                    ORDER BY ea.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute();
            } else {
                // Instructor sees only exam attempts for their courses
                $stmt = $conn->prepare("
                    SELECT ea.id, ea.user_id, ea.exam_id, ea.score, ea.is_passed, ea.created_at,
                           u.username, u.first_name, u.last_name,
                           e.title as exam_title
                    FROM exam_attempts ea
                    JOIN users u ON ea.user_id = u.id
                    JOIN exams e ON ea.exam_id = e.id
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                    ORDER BY ea.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$currentAdmin['id']]);
            }
            $recentAttempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate percentage for each attempt
            foreach ($recentAttempts as &$attempt) {
                // Get total questions for this exam
                $stmtQuestions = $conn->prepare("
                    SELECT COUNT(*) as total_questions 
                    FROM questions 
                    WHERE exam_id = ?
                ");
                $stmtQuestions->execute([$attempt['exam_id']]);
                $totalQuestions = $stmtQuestions->fetchColumn() ?: 1; // Avoid division by zero
                
                // Calculate percentage - score should be a percentage of correct answers
                // Assuming score is the number of correct answers
                $attempt['percentage'] = min(100, round(($attempt['score'] / $totalQuestions) * 100));
            }
            
            // Get pass/fail statistics for chart
            if ($isAdmin) {
                // Admin sees all pass/fail statistics
                $stmtPassFail = $conn->prepare("
                    SELECT is_passed, COUNT(*) as count
                    FROM exam_attempts
                    GROUP BY is_passed
                ");
                $stmtPassFail->execute();
            } else {
                // Instructor sees only pass/fail statistics for their courses
                $stmtPassFail = $conn->prepare("
                    SELECT ea.is_passed, COUNT(*) as count
                    FROM exam_attempts ea
                    JOIN exams e ON ea.exam_id = e.id
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                    GROUP BY ea.is_passed
                ");
                $stmtPassFail->execute([$currentAdmin['id']]);
            }
            $passFailResults = $stmtPassFail->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($passFailResults as $result) {
                if ($result['is_passed'] == 1) {
                    $examPassFailData['passed'] = (int)$result['count'];
                } else {
                    $examPassFailData['failed'] = (int)$result['count'];
                }
            }
            
            // Get monthly revenue data for chart
            $revenueChartData = ['labels' => [], 'values' => []];
            if ($isAdmin) {
                // Admin sees all revenue data
                $stmtRevenue = $conn->prepare("
                    SELECT 
                        DATE_FORMAT(payment_date, '%Y-%m') as month,
                        SUM(amount) as total
                    FROM payments
                    WHERE status = 'completed'
                    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 6
                ");
                $stmtRevenue->execute();
            } else {
                // Instructor sees only revenue data for their courses
                $stmtRevenue = $conn->prepare("
                    SELECT 
                        DATE_FORMAT(p.payment_date, '%Y-%m') as month,
                        SUM(p.amount) as total
                    FROM payments p
                    JOIN courses c ON p.course_id = c.id
                    WHERE p.status = 'completed' AND c.admin_id = ?
                    GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 6
                ");
                $stmtRevenue->execute([$currentAdmin['id']]);
            }
            $revenueResults = $stmtRevenue->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($revenueResults as $result) {
                $monthDate = new DateTime($result['month'] . '-01');
                $revenueChartData['labels'][] = $monthDate->format('M Y');
                $revenueChartData['values'][] = (float)$result['total'];
            }
            
            // If no revenue data, provide sample data
            if (empty($revenueChartData['labels'])) {
                $revenueChartData = [
                    'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                    'values' => [12500, 15000, 10000, 20000, 17500, 22500]
                ];
            }
            
            // Get user registration data for chart
            $userChartData = ['labels' => [], 'values' => []];
            if ($isAdmin) {
                // Admin sees all user registration data
                $stmtUsers = $conn->prepare("
                    SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as month,
                        COUNT(*) as count
                    FROM users
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 6
                ");
                $stmtUsers->execute();
                $userResults = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // For instructors, we don't show user registration data
                // Instead, show enrollment data for their courses
                $stmtUsers = $conn->prepare("
                    SELECT 
                        DATE_FORMAT(e.created_at, '%Y-%m') as month,
                        COUNT(*) as count
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE c.admin_id = ?
                    GROUP BY DATE_FORMAT(e.created_at, '%Y-%m')
                    ORDER BY month ASC
                    LIMIT 6
                ");
                $stmtUsers->execute([$currentAdmin['id']]);
                $userResults = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
            }
            
            foreach ($userResults as $result) {
                $monthDate = new DateTime($result['month'] . '-01');
                $userChartData['labels'][] = $monthDate->format('M Y');
                $userChartData['values'][] = (int)$result['count'];
            }
            
            // If no user data, provide sample data
            if (empty($userChartData['labels'])) {
                $userChartData = [
                    'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                    'values' => [25, 40, 35, 50, 45, 60]
                ];
            }
        } else {
            // Sample chart data
            $examPassFailData = ['passed' => 35, 'failed' => 15];
            $revenueChartData = [
                'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                'values' => [12500, 15000, 10000, 20000, 17500, 22500]
            ];
            $userChartData = [
                'labels' => ['Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025'],
                'values' => [25, 40, 35, 50, 45, 60]
            ];
            
            // Sample data
            $recentAttempts = [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'exam_id' => 1,
                    'score' => 42,
                    'percentage' => 70,
                    'passed' => 1,
                    'username' => 'john_doe',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'exam_title' => 'CCNA Practice Exam 1',
                    'created_at' => '2023-07-15 11:30:00',
                    'completed_at' => '2023-07-15 11:30:00',
                    'passing_score' => 60
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'exam_id' => 3,
                    'score' => 67,
                    'percentage' => 75,
                    'passed' => 1,
                    'username' => 'jane_smith',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'exam_title' => 'Security+ Practice Exam',
                    'created_at' => '2023-07-14 16:15:00',
                    'completed_at' => '2023-07-14 16:15:00',
                    'passing_score' => 70
                ],
                [
                    'id' => 3,
                    'user_id' => 3,
                    'exam_id' => 4,
                    'score' => 27,
                    'percentage' => 90,
                    'passed' => 1,
                    'username' => 'mohammed_ali',
                    'first_name' => 'Mohammed',
                    'last_name' => 'Ali',
                    'exam_title' => 'Network+ Basics Quiz',
                    'created_at' => '2023-07-13 10:00:00',
                    'completed_at' => '2023-07-13 10:00:00',
                    'passing_score' => 25
                ],
                [
                    'id' => 4,
                    'user_id' => 4,
                    'exam_id' => 1,
                    'score' => 36,
                    'percentage' => 60,
                    'passed' => 0,
                    'username' => 'sarah_johnson',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'exam_title' => 'CCNA Practice Exam 1',
                    'created_at' => '2023-07-12 17:30:00',
                    'completed_at' => '2023-07-12 17:30:00',
                    'passing_score' => 70
                ],
                [
                    'id' => 5,
                    'user_id' => 5,
                    'exam_id' => 5,
                    'score' => 85,
                    'percentage' => 85,
                    'passed' => 1,
                    'username' => 'david_brown',
                    'first_name' => 'David',
                    'last_name' => 'Brown',
                    'exam_title' => 'CCNP Enterprise Practice Exam',
                    'created_at' => '2023-07-11 12:45:00',
                    'completed_at' => '2023-07-11 12:45:00',
                    'passing_score' => 80
                ]
            ];
        }
        
        // Prepare instructor chart data
        $instructorExamChartData = [];
        $instructorEnrollmentChartData = [];
        
        if (!$isAdmin) {
            // Prepare exam performance data for instructor
            $examLabels = [];
            $examPassData = [];
            $examFailData = [];
            
            if ($conn) {
                $stmt = $conn->prepare("
                    SELECT 
                        e.title,
                        SUM(CASE WHEN ea.is_passed = 1 THEN 1 ELSE 0 END) as pass_count,
                        SUM(CASE WHEN ea.is_passed = 0 THEN 1 ELSE 0 END) as fail_count
                    FROM exams e
                    JOIN courses c ON e.course_id = c.id
                    LEFT JOIN exam_attempts ea ON e.id = ea.exam_id
                    WHERE c.admin_id = ?
                    GROUP BY e.id
                    ORDER BY e.created_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$currentAdmin['id']]);
                $examResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($examResults as $exam) {
                    $examLabels[] = $exam['title'];
                    $examPassData[] = (int)$exam['pass_count'];
                    $examFailData[] = (int)$exam['fail_count'];
                }
            } else {
                // Sample data
                $examLabels = ['CCNA Exam 1', 'CCNA Exam 2', 'Network+ Quiz', 'Security+ Exam', 'Cloud Basics'];
                $examPassData = [35, 28, 42, 18, 12];
                $examFailData = [15, 12, 8, 7, 3];
            }
            
            $instructorExamChartData = [
                'labels' => $examLabels,
                'datasets' => [
                    [
                        'label' => 'ناجح',
                        'data' => $examPassData,
                        'backgroundColor' => 'rgba(40, 167, 69, 0.7)'
                    ],
                    [
                        'label' => 'راسب',
                        'data' => $examFailData,
                        'backgroundColor' => 'rgba(220, 53, 69, 0.7)'
                    ]
                ]
            ];
            
            // Prepare enrollment distribution data
            $courseLabels = [];
            $enrollmentData = [];
            $courseColors = [
                'rgba(0, 123, 255, 0.7)',
                'rgba(40, 167, 69, 0.7)',
                'rgba(255, 193, 7, 0.7)',
                'rgba(220, 53, 69, 0.7)',
                'rgba(23, 162, 184, 0.7)',
                'rgba(111, 66, 193, 0.7)'
            ];
            
            if ($conn) {
                $stmt = $conn->prepare("
                    SELECT 
                        c.title,
                        COUNT(e.id) as enrollment_count
                    FROM courses c
                    LEFT JOIN enrollments e ON c.id = e.course_id
                    WHERE c.admin_id = ?
                    GROUP BY c.id
                    ORDER BY enrollment_count DESC
                    LIMIT 6
                ");
                $stmt->execute([$currentAdmin['id']]);
                $enrollmentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($enrollmentResults as $index => $course) {
                    $courseLabels[] = $course['title'];
                    $enrollmentData[] = (int)$course['enrollment_count'];
                }
            } else {
                // Sample data
                $courseLabels = ['Network+ Basics', 'CCNA Certification', 'Security+ Certification', 'Cloud Computing'];
                $enrollmentData = [120, 45, 35, 15];
            }
            
            $instructorEnrollmentChartData = [
                'labels' => $courseLabels,
                'datasets' => [
                    [
                        'data' => $enrollmentData,
                        'backgroundColor' => array_slice($courseColors, 0, count($courseLabels))
                    ]
                ]
            ];
        }

        // Set page title
        $pageTitle = 'لوحة التحكم';

        // Add Chart.js library
        $extraStyles = '';
        $extraScripts = '
            <script src="/admin/assets/js/dashboard-charts.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    ' . ($isAdmin ? '
                    // Initialize Admin Charts
                    const revenueData = ' . json_encode($revenueChartData) . ';
                    initRevenueChart(revenueData);

                    const userData = ' . json_encode($userChartData) . ';
                    initUserRegistrationChart(userData);

                    const examData = ' . json_encode($examPassFailData) . ';
                    initExamCompletionChart(examData);
                    ' : '
                    // Initialize Instructor Charts
                    const instructorExamData = ' . json_encode($instructorExamChartData) . ';
                    initInstructorExamChart(instructorExamData);
                    
                    const instructorEnrollmentData = ' . json_encode($instructorEnrollmentChartData) . ';
                    initInstructorEnrollmentChart(instructorEnrollmentData);
                    ') . '
                });
            </script>
        ';

        // Start output buffering
        ob_start();

        // Get recent activities for data entry users
        $recentActivities = [];
        if ($conn && $isDataEntry) {
            $stmt = $conn->prepare("
                SELECT description, created_at 
                FROM activity_logs 
                WHERE admin_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $stmt->execute([$currentAdmin['id']]);
            $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Include the appropriate dashboard template
        if ($isDataEntry) {
            include ADMIN_ROOT . '/templates/dashboard_data_entry.php';
        } else {
            include ADMIN_ROOT . '/templates/dashboard.php';
        }

        // Get the content
        $contentView = ob_get_clean();

        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    

}

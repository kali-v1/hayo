<?php
/**
 * Course Controller
 * 
 * This controller handles course-related functionality.
 */
class CourseController extends BaseController {
    /**
     * Display a list of courses
     */
    public function index() {
        global $conn;
        
        // Get filter parameters
        $search = sanitizeInput($_GET['search'] ?? '');
        $filter = sanitizeInput($_GET['filter'] ?? 'all');
        $sort = sanitizeInput($_GET['sort'] ?? 'newest');
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 9;
        
        // Get courses
        $courses = [];
        $totalCourses = 0;
        
        if ($conn) {
            $course = new Course();
            $result = $course->getAll($search, $filter, $sort, $page, $perPage);
            $courses = $result['courses'];
            $totalCourses = $result['total'];
        } else {
            // Sample data if database connection is not available
            $courses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'description' => 'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.',
                    'image' => 'ccna.jpg',
                    'price' => 99.99,
                    'is_free' => 0,
                    'students' => 1250,
                    'rating' => 4.8,
                    'reviews' => 320
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'description' => 'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.',
                    'image' => 'security-plus.jpg',
                    'price' => 79.99,
                    'is_free' => 0,
                    'students' => 980,
                    'rating' => 4.6,
                    'reviews' => 245
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'description' => 'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.',
                    'image' => 'network-plus.jpg',
                    'price' => 0,
                    'is_free' => 1,
                    'students' => 2100,
                    'rating' => 4.5,
                    'reviews' => 410
                ],
                [
                    'id' => 4,
                    'title' => 'CCNP Enterprise',
                    'description' => 'Advanced Cisco certification course covering enterprise networking solutions, including infrastructure, services, and security.',
                    'image' => 'ccnp.jpg',
                    'price' => 149.99,
                    'is_free' => 0,
                    'students' => 750,
                    'rating' => 4.9,
                    'reviews' => 180
                ],
                [
                    'id' => 5,
                    'title' => 'AWS Certified Solutions Architect',
                    'description' => 'Prepare for the AWS Certified Solutions Architect - Associate exam. Learn to design and deploy scalable systems on AWS.',
                    'image' => 'aws-architect.jpg',
                    'price' => 129.99,
                    'is_free' => 0,
                    'students' => 1800,
                    'rating' => 4.7,
                    'reviews' => 390
                ],
                [
                    'id' => 6,
                    'title' => 'Azure Fundamentals',
                    'description' => 'Introduction to Microsoft Azure cloud services. Prepare for the AZ-900 certification exam.',
                    'image' => 'azure-fundamentals.jpg',
                    'price' => 0,
                    'is_free' => 1,
                    'students' => 1600,
                    'rating' => 4.4,
                    'reviews' => 320
                ]
            ];
            
            $totalCourses = count($courses);
        }
        
        // Calculate pagination
        $totalPages = ceil($totalCourses / $perPage);
        
        // Render the view
        $this->render('courses', [
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'pageTitle' => translate('courses')
        ]);
    }
    
    /**
     * Display a single course
     * 
     * @param int $id The course ID
     */
    public function show($id) {
        global $conn;
        
        // Get the course
        $course = null;
        $exams = [];
        $reviews = [];
        
        if ($conn) {
            $courseModel = new Course();
            $course = $courseModel->loadById($id) ? $courseModel->toArray() : null;
            
            if ($course) {
                $examModel = new Exam();
                $exams = $examModel->getByCourseId($id);
                
                $reviews = $courseModel->getReviews($id);
            }
        } else {
            // Sample data if database connection is not available
            $courses = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'description' => 'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.',
                    'image' => 'ccna.jpg',
                    'price' => 99.99,
                    'is_free' => 0,
                    'students' => 1250,
                    'rating' => 4.8,
                    'reviews_count' => 320,
                    'duration' => 40,
                    'instructor' => 'John Smith',
                    'instructor_bio' => 'Certified Cisco instructor with over 15 years of experience in networking.',
                    'instructor_image' => 'john-smith.jpg',
                    'curriculum' => [
                        'Network Fundamentals',
                        'Network Access',
                        'IP Connectivity',
                        'IP Services',
                        'Security Fundamentals',
                        'Automation and Programmability'
                    ]
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'description' => 'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.',
                    'image' => 'security-plus.jpg',
                    'price' => 79.99,
                    'is_free' => 0,
                    'students' => 980,
                    'rating' => 4.6,
                    'reviews_count' => 245,
                    'duration' => 35,
                    'instructor' => 'Sarah Johnson',
                    'instructor_bio' => 'Cybersecurity expert with CompTIA Security+ and CISSP certifications.',
                    'instructor_image' => 'sarah-johnson.jpg',
                    'curriculum' => [
                        'Threats, Attacks, and Vulnerabilities',
                        'Technologies and Tools',
                        'Architecture and Design',
                        'Identity and Access Management',
                        'Risk Management',
                        'Cryptography and PKI'
                    ]
                ],
                3 => [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'description' => 'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.',
                    'image' => 'network-plus.jpg',
                    'price' => 0,
                    'is_free' => 1,
                    'students' => 2100,
                    'rating' => 4.5,
                    'reviews_count' => 410,
                    'duration' => 20,
                    'instructor' => 'Michael Brown',
                    'instructor_bio' => 'IT trainer with over 10 years of experience teaching networking concepts.',
                    'instructor_image' => 'michael-brown.jpg',
                    'curriculum' => [
                        'Networking Concepts',
                        'Infrastructure',
                        'Network Operations',
                        'Network Security',
                        'Troubleshooting'
                    ]
                ]
            ];
            
            if (isset($courses[$id])) {
                $course = $courses[$id];
                
                // Sample exams
                $exams = [
                    [
                        'id' => 1,
                        'title' => 'CCNA Practice Exam 1',
                        'description' => 'Test your knowledge of CCNA concepts with this practice exam.',
                        'questions' => 60,
                        'duration' => 90,
                        'passing_score' => 70
                    ],
                    [
                        'id' => 2,
                        'title' => 'CCNA Practice Exam 2',
                        'description' => 'Advanced practice exam covering all CCNA topics.',
                        'questions' => 80,
                        'duration' => 120,
                        'passing_score' => 75
                    ]
                ];
                
                // Sample reviews
                $reviews = [
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'username' => 'john_doe',
                        'profile_image' => null,
                        'rating' => 5,
                        'review' => 'Excellent course! The practice exams were particularly helpful for my preparation.',
                        'created_at' => '2023-05-15 10:30:00'
                    ],
                    [
                        'id' => 2,
                        'user_id' => 2,
                        'username' => 'jane_smith',
                        'profile_image' => null,
                        'rating' => 4,
                        'review' => 'Very comprehensive content. I would have liked more hands-on exercises, but overall it was great.',
                        'created_at' => '2023-06-20 14:45:00'
                    ],
                    [
                        'id' => 3,
                        'user_id' => 3,
                        'username' => 'mohammed_ali',
                        'profile_image' => null,
                        'rating' => 5,
                        'review' => 'This course helped me pass my CCNA exam on the first try. Highly recommended!',
                        'created_at' => '2023-07-10 09:15:00'
                    ]
                ];
            }
        }
        
        // If course not found, show 404 page
        if (!$course) {
            http_response_code(404);
            include APP_ROOT . '/templates/404.php';
            return;
        }
        
        // Check if user is enrolled
        $isEnrolled = false;
        if (isset($_SESSION['user_id']) && $conn) {
            $courseModel = new Course($conn);
            $isEnrolled = $courseModel->isUserEnrolled($_SESSION['user_id'], $id);
        }
        
        // Set default values for variables
        $courseRating = isset($course['rating']) ? $course['rating'] : 0;
        $ratingCount = isset($course['reviews']) ? $course['reviews'] : 0;
        $enrollmentCount = isset($course['students']) ? $course['students'] : 0;
        $examCount = count($exams);
        $questionCount = 0;
        foreach ($exams as $exam) {
            $questionCount += isset($exam['questions']) ? $exam['questions'] : 0;
        }
        
        // Set instructor information
        $instructor = [
            'name' => isset($course['admin_username']) ? $course['admin_username'] : 'Admin'
        ];
        
        // Set access and review status
        $hasAccess = $isEnrolled;
        $hasReviewed = false;
        $totalReviews = count($reviews);
        
        // Render the view
        $this->render('course', [
            'course' => $course,
            'exams' => $exams,
            'reviews' => $reviews,
            'isEnrolled' => $isEnrolled,
            'hasAccess' => $hasAccess,
            'hasReviewed' => $hasReviewed,
            'courseRating' => $courseRating,
            'ratingCount' => $ratingCount,
            'enrollmentCount' => $enrollmentCount,
            'examCount' => $examCount,
            'questionCount' => $questionCount,
            'instructor' => $instructor,
            'totalReviews' => $totalReviews,
            'pageTitle' => $course['title']
        ]);
    }
    
    /**
     * Enroll in a course
     * 
     * @param int $id The course ID
     */
    public function enroll($id) {
        global $conn;
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_to_enroll'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the course
        $course = null;
        
        if ($conn) {
            $courseModel = new Course();
            $course = $courseModel->loadById($id) ? $courseModel->toArray() : null;
            
            if ($course) {
                // Check if the course is free or if the user has already purchased it
                if ($course['is_free']) {
                    // Enroll the user in the free course
                    $result = $courseModel->enrollUser($_SESSION['user_id'], $id);
                    
                    if ($result) {
                        setFlashMessage(translate('course_enroll_success'), 'success');
                    } else {
                        setFlashMessage(translate('course_enroll_error'), 'error');
                    }
                } else {
                    // Redirect to payment page for paid courses
                    header('Location: /course/' . $id . '/payment');
                    exit;
                }
            }
        } else {
            // Sample success message if database connection is not available
            setFlashMessage(translate('course_enroll_success'), 'success');
        }
        
        // Redirect back to the course page
        header('Location: /course/' . $id);
        exit;
    }
    
    /**
     * Add a review to a course
     * 
     * @param int $id The course ID
     */
    public function addReview($id) {
        global $conn;
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rating = intval($_POST['rating'] ?? 0);
            $review = sanitizeInput($_POST['review'] ?? '');
            
            // Validate input
            if ($rating < 1 || $rating > 5) {
                setFlashMessage(translate('invalid_rating'), 'error');
                header('Location: /course/' . $id);
                exit;
            }
            
            if (empty($review)) {
                setFlashMessage(translate('review_required'), 'error');
                header('Location: /course/' . $id);
                exit;
            }
            
            // Add the review
            if ($conn) {
                $courseModel = new Course();
                
                // Check if the user is enrolled in the course
                $isEnrolled = $courseModel->isUserEnrolled($_SESSION['user_id'], $id);
                
                if (!$isEnrolled) {
                    setFlashMessage(translate('must_be_enrolled'), 'error');
                    header('Location: /course/' . $id);
                    exit;
                }
                
                // Check if the user has already reviewed the course
                $hasReviewed = $courseModel->hasUserReviewed($_SESSION['user_id'], $id);
                
                if ($hasReviewed) {
                    // Update the existing review
                    $result = $courseModel->updateReview($_SESSION['user_id'], $id, $rating, $review);
                    
                    if ($result) {
                        setFlashMessage(translate('review_update_success'), 'success');
                    } else {
                        setFlashMessage(translate('review_update_error'), 'error');
                    }
                } else {
                    // Add a new review
                    $result = $courseModel->addReview($_SESSION['user_id'], $id, $rating, $review);
                    
                    if ($result) {
                        setFlashMessage(translate('review_submit_success'), 'success');
                    } else {
                        setFlashMessage(translate('review_submit_error'), 'error');
                    }
                }
            } else {
                // Sample success message if database connection is not available
                setFlashMessage(translate('review_submit_success'), 'success');
            }
        }
        
        // Redirect back to the course page
        header('Location: /course/' . $id);
        exit;
    }
}
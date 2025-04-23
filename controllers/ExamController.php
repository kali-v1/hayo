<?php
/**
 * Exam Controller
 * 
 * This controller handles exam-related functionality.
 */
class ExamController extends BaseController {
    /**
     * Display a list of exams
     */
    public function index() {
        global $conn;
        
        // Get filter parameters
        $search = sanitizeInput($_GET['search'] ?? '');
        $filter = sanitizeInput($_GET['filter'] ?? 'all');
        $sort = sanitizeInput($_GET['sort'] ?? 'newest');
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 9;
        
        // Get exams
        $exams = [];
        $totalExams = 0;
        
        if ($conn) {
            try {
                $exam = new Exam($conn);
                $result = $exam->getAll($search, $filter, $sort, $page, $perPage);
                $exams = $result['exams'];
                $totalExams = $result['total'];
                
                // Debug information
                error_log("Exams found: " . count($exams));
                error_log("Total exams: " . $totalExams);
            } catch (Exception $e) {
                error_log("Error fetching exams: " . $e->getMessage());
                // Fallback to sample data
                $exams = [];
                $totalExams = 0;
            }
        } else {
            // Sample data if database connection is not available
            $exams = [
                [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Test your knowledge of CCNA concepts with this practice exam.',
                    'image' => 'ccna-exam.jpg',
                    'questions' => 60,
                    'duration' => 90,
                    'passing_score' => 70,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'is_free' => 0,
                    'attempts' => 320
                ],
                [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2',
                    'description' => 'Advanced practice exam covering all CCNA topics.',
                    'image' => 'ccna-exam-2.jpg',
                    'questions' => 80,
                    'duration' => 120,
                    'passing_score' => 75,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'is_free' => 0,
                    'attempts' => 280
                ],
                [
                    'id' => 3,
                    'title' => 'Security+ Practice Exam',
                    'description' => 'Comprehensive practice exam for CompTIA Security+ certification.',
                    'image' => 'security-plus-exam.jpg',
                    'questions' => 90,
                    'duration' => 90,
                    'passing_score' => 75,
                    'course_id' => 2,
                    'course_title' => 'Security+ Certification',
                    'is_free' => 0,
                    'attempts' => 210
                ],
                [
                    'id' => 4,
                    'title' => 'Network+ Basics Quiz',
                    'description' => 'Test your understanding of basic networking concepts.',
                    'image' => 'network-plus-quiz.jpg',
                    'questions' => 30,
                    'duration' => 30,
                    'passing_score' => 70,
                    'course_id' => 3,
                    'course_title' => 'Network+ Basics',
                    'is_free' => 1,
                    'attempts' => 450
                ],
                [
                    'id' => 5,
                    'title' => 'CCNP Enterprise Practice Exam',
                    'description' => 'Prepare for the CCNP Enterprise certification with this comprehensive practice exam.',
                    'image' => 'ccnp-exam.jpg',
                    'questions' => 100,
                    'duration' => 120,
                    'passing_score' => 80,
                    'course_id' => 4,
                    'course_title' => 'CCNP Enterprise',
                    'is_free' => 0,
                    'attempts' => 180
                ],
                [
                    'id' => 6,
                    'title' => 'AWS Solutions Architect Practice Exam',
                    'description' => 'Test your knowledge of AWS services and solutions architecture.',
                    'image' => 'aws-exam.jpg',
                    'questions' => 65,
                    'duration' => 130,
                    'passing_score' => 72,
                    'course_id' => 5,
                    'course_title' => 'AWS Certified Solutions Architect',
                    'is_free' => 0,
                    'attempts' => 320
                ]
            ];
            
            $totalExams = count($exams);
        }
        
        // Calculate pagination
        $totalPages = ceil($totalExams / $perPage);
        
        // Render the view
        $this->render('exams', [
            'exams' => $exams,
            'totalExams' => $totalExams,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'pageTitle' => translate('exams')
        ]);
    }
    
    /**
     * Display a single exam
     * 
     * @param int $id The exam ID
     */
    public function show($id) {
        global $conn;
        
        // Get the exam
        $exam = null;
        $userAttempts = [];
        $examStats = null;
        
        if ($conn) {
            $examModel = new Exam($conn);
            $exam = $examModel->loadById($id) ? $examModel->toArray() : null;
            
            if ($exam && isset($_SESSION['user_id'])) {
                $userAttempts = $examModel->getUserAttempts($_SESSION['user_id'], $id);
                $examStats = $examModel->getStats($id);
            }
        } else {
            // Sample data if database connection is not available
            $exams = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Test your knowledge of CCNA concepts with this practice exam. This exam covers networking fundamentals, routing and switching, and network security topics.',
                    'image' => 'ccna-exam.jpg',
                    'questions' => 60,
                    'duration' => 90,
                    'passing_score' => 70,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'is_free' => 0,
                    'attempts' => 320,
                    'question_types' => [
                        'single_choice' => 40,
                        'multiple_choice' => 15,
                        'drag_drop' => 5
                    ],
                    'total_points' => 60
                ],
                2 => [
                    'id' => 2,
                    'title' => 'CCNA Practice Exam 2',
                    'description' => 'Advanced practice exam covering all CCNA topics. This exam is designed to simulate the actual CCNA certification exam.',
                    'image' => 'ccna-exam-2.jpg',
                    'questions' => 80,
                    'duration' => 120,
                    'passing_score' => 75,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'is_free' => 0,
                    'attempts' => 280,
                    'question_types' => [
                        'single_choice' => 50,
                        'multiple_choice' => 20,
                        'drag_drop' => 10
                    ],
                    'total_points' => 80
                ],
                3 => [
                    'id' => 3,
                    'title' => 'Security+ Practice Exam',
                    'description' => 'Comprehensive practice exam for CompTIA Security+ certification. Test your knowledge of cybersecurity concepts, threats, and security controls.',
                    'image' => 'security-plus-exam.jpg',
                    'questions' => 90,
                    'duration' => 90,
                    'passing_score' => 75,
                    'course_id' => 2,
                    'course_title' => 'Security+ Certification',
                    'is_free' => 0,
                    'attempts' => 210,
                    'question_types' => [
                        'single_choice' => 60,
                        'multiple_choice' => 25,
                        'drag_drop' => 5
                    ],
                    'total_points' => 90
                ],
                4 => [
                    'id' => 4,
                    'title' => 'Network+ Basics Quiz',
                    'description' => 'Test your understanding of basic networking concepts. This quiz covers fundamental networking principles and is suitable for beginners.',
                    'image' => 'network-plus-quiz.jpg',
                    'questions' => 30,
                    'duration' => 30,
                    'passing_score' => 70,
                    'course_id' => 3,
                    'course_title' => 'Network+ Basics',
                    'is_free' => 1,
                    'attempts' => 450,
                    'question_types' => [
                        'single_choice' => 25,
                        'multiple_choice' => 5,
                        'drag_drop' => 0
                    ],
                    'total_points' => 30
                ]
            ];
            
            if (isset($exams[$id])) {
                $exam = $exams[$id];
                
                // Sample user attempts
                if (isset($_SESSION['user_id'])) {
                    $userAttempts = [
                        [
                            'id' => 1,
                            'date' => '2023-06-15 10:30:00',
                            'score' => 42,
                            'percentage' => 70,
                            'result' => 'passed',
                            'time_taken' => 75
                        ],
                        [
                            'id' => 2,
                            'date' => '2023-07-20 14:45:00',
                            'score' => 48,
                            'percentage' => 80,
                            'result' => 'passed',
                            'time_taken' => 82
                        ]
                    ];
                    
                    // Sample exam stats
                    $examStats = [
                        'total_attempts' => 2,
                        'average_score' => 75.0,
                        'pass_rate' => 100
                    ];
                }
            }
        }
        
        // If exam not found, show 404 page
        if (!$exam) {
            http_response_code(404);
            include APP_ROOT . '/templates/404.php';
            return;
        }
        
        // Check if user can take the exam
        $canTakeExam = false;
        $isEnrolled = false;
        $hasTaken = false;
        $course = null;
        
        if (isset($_SESSION['user_id'])) {
            if ($exam['is_free']) {
                $canTakeExam = true;
            } else if ($conn) {
                $courseModel = new Course();
                
                // If exam is linked to a course, check enrollment
                if ($exam['course_id']) {
                    $isEnrolled = $courseModel->isUserEnrolled($_SESSION['user_id'], $exam['course_id']);
                    $canTakeExam = $isEnrolled;
                } else {
                    // Exams without courses are accessible to all logged-in users
                    $canTakeExam = true;
                }
                
                // Get course details if course_id is not null
                $course = null;
                if ($exam['course_id']) {
                    $course = $courseModel->loadById($exam['course_id']) ? $courseModel->toArray() : null;
                }
                
                // Check if user has taken the exam before
                if (!empty($userAttempts)) {
                    $hasTaken = true;
                }
            } else {
                // Sample data
                $canTakeExam = true;
                $isEnrolled = true;
                $hasTaken = !empty($userAttempts);
                
                // Sample course data
                $course = [
                    'id' => $exam['course_id'],
                    'title' => $exam['course_title'],
                    'description' => 'Sample course description',
                    'price' => 99.99,
                    'is_free' => 0
                ];
            }
        }
        
        // Get question counts by type
        $questionCount = 0;
        $singleChoiceCount = 0;
        $multipleChoiceCount = 0;
        $dragDropCount = 0;
        $totalPoints = 0;
        
        if ($conn) {
            $questionModel = new Question();
            $questions = $questionModel->getByExamId($exam['id']);
            $questionCount = count($questions);
            
            // Count question types
            foreach ($questions as $question) {
                if ($question['question_type'] === 'single_choice') {
                    $singleChoiceCount++;
                } elseif ($question['question_type'] === 'multiple_choice') {
                    $multipleChoiceCount++;
                } elseif ($question['question_type'] === 'drag_drop') {
                    $dragDropCount++;
                }
                $totalPoints += $question['points'] ?? 1;
            }
        } else {
            // Sample data
            if (isset($exam['question_types'])) {
                $singleChoiceCount = $exam['question_types']['single_choice'] ?? 0;
                $multipleChoiceCount = $exam['question_types']['multiple_choice'] ?? 0;
                $dragDropCount = $exam['question_types']['drag_drop'] ?? 0;
                $questionCount = $singleChoiceCount + $multipleChoiceCount + $dragDropCount;
                $totalPoints = $exam['total_points'] ?? $questionCount;
            }
        }
        
        // Get exam statistics
        $attemptCount = 0;
        $averageScore = 0;
        $passRate = 0;
        
        if ($examStats) {
            $attemptCount = $examStats['total_attempts'] ?? 0;
            $averageScore = $examStats['average_score'] ?? 0;
            $passRate = $examStats['pass_rate'] ?? 0;
        }
        
        // Determine if user has access to the exam
        $hasAccess = false;
        if (isset($_SESSION['user_id'])) {
            if ($exam['is_free']) {
                $hasAccess = true;
            } else if ($course && $isEnrolled) {
                $hasAccess = true;
            } else if (!$course) {
                $hasAccess = true; // Standalone exams are accessible to logged-in users
            }
        }
        
        // Get recommended courses for this exam
        $recommendedCourses = [];
        if ($conn) {
            $recommendedCourses = $examModel->getRecommendedCourses($exam['id']);
        } else {
            // Sample recommended courses data
            $recommendedCourses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'description' => 'Learn the fundamentals of networking with this comprehensive course.'
                ]
            ];
        }
        
        // Render the view
        $this->render('exam', [
            'exam' => $exam,
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'hasTaken' => $hasTaken,
            'canTakeExam' => $canTakeExam,
            'hasAccess' => $hasAccess,
            'userAttempts' => $userAttempts,
            'examStats' => $examStats,
            'questionCount' => $questionCount,
            'singleChoiceCount' => $singleChoiceCount,
            'multipleChoiceCount' => $multipleChoiceCount,
            'dragDropCount' => $dragDropCount,
            'totalPoints' => $totalPoints,
            'attemptCount' => $attemptCount,
            'averageScore' => $averageScore,
            'passRate' => $passRate,
            'recommendedCourses' => $recommendedCourses,
            'pageTitle' => $exam['title']
        ]);
    }
    
    /**
     * Take an exam
     * 
     * @param int $id The exam ID
     */
    public function take($id) {
        global $conn;
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the exam
        $exam = null;
        $questions = [];
        
        if ($conn) {
            $examModel = new Exam($conn);
            $exam = $examModel->loadById($id) ? $examModel->toArray() : null;
            
            if ($exam) {
                // Check if user can take the exam
                if ($exam['is_free']) {
                    $canTakeExam = true;
                } else {
                    $courseModel = new Course();
                    $canTakeExam = $exam['course_id'] ? $courseModel->isUserEnrolled($_SESSION['user_id'], $exam['course_id']) : true;
                }
                
                if (!$canTakeExam) {
                    setFlashMessage(translate('exam_requires_course_purchase'), 'warning');
                    header('Location: /exam/' . $id);
                    exit;
                }
                
                // Get the questions
                $questionModel = new Question($conn);
                $questions = $questionModel->getByExamId($id);
                
                // Start a new exam attempt
                $attemptId = $examModel->startAttempt($_SESSION['user_id'], $id);
                $_SESSION['exam_attempt_id'] = $attemptId;
            }
        } else {
            // Sample data if database connection is not available
            $exams = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Test your knowledge of CCNA concepts with this practice exam.',
                    'image' => 'ccna-exam.jpg',
                    'questions' => 60,
                    'duration' => 90,
                    'passing_score' => 70,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification',
                    'is_free' => 0
                ]
            ];
            
            if (isset($exams[$id])) {
                $exam = $exams[$id];
                
                // Sample questions
                $questions = [
                    [
                        'id' => 1,
                        'question' => 'Which of the following is a valid IPv4 address?',
                        'type' => 'single_choice',
                        'options' => [
                            'A' => '192.168.1.256',
                            'B' => '10.0.0.1',
                            'C' => '172.31.256.1',
                            'D' => '256.256.256.256'
                        ],
                        'points' => 1
                    ],
                    [
                        'id' => 2,
                        'question' => 'Which of the following protocols operate at the Transport layer of the OSI model? (Select all that apply)',
                        'type' => 'multiple_choice',
                        'options' => [
                            'A' => 'TCP',
                            'B' => 'IP',
                            'C' => 'UDP',
                            'D' => 'HTTP',
                            'E' => 'ICMP'
                        ],
                        'points' => 1
                    ],
                    [
                        'id' => 3,
                        'question' => 'Match the following protocols with their default port numbers:',
                        'type' => 'drag_drop',
                        'items' => [
                            'HTTP',
                            'HTTPS',
                            'FTP',
                            'SSH'
                        ],
                        'drop_zones' => [
                            'Port 21',
                            'Port 22',
                            'Port 80',
                            'Port 443'
                        ],
                        'points' => 2
                    ]
                ];
                
                // Sample attempt ID
                $_SESSION['exam_attempt_id'] = 1;
            }
        }
        
        // If exam not found, show 404 page
        if (!$exam) {
            http_response_code(404);
            include APP_ROOT . '/templates/404.php';
            return;
        }
        
        // Get course information
        $courseModel = new Course($conn);
        $course = isset($exam['course_id']) ? $courseModel->getById($exam['course_id']) : null;
        
        // Create exam session data
        $examSession = [
            'attempt_id' => $_SESSION['exam_attempt_id'],
            'start_time' => time(),
            'duration' => $exam['duration'] * 60 // convert minutes to seconds
        ];
        
        // Render the view
        $this->render('take_exam', [
            'exam' => $exam,
            'course' => $course,
            'questions' => $questions,
            'examSession' => $examSession,
            'pageTitle' => translate('taking_exam') . ': ' . $exam['title']
        ]);
    }
    
    /**
     * Submit an exam
     * 
     * @param int $id The exam ID
     */
    public function submit($id) {
        global $conn;
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $answers = $_POST['answers'] ?? [];
            $attemptId = $_SESSION['exam_attempt_id'] ?? null;
            
            if (!$attemptId) {
                setFlashMessage(translate('invalid_attempt'), 'error');
                header('Location: /exam/' . $id);
                exit;
            }
            
            // Process the answers
            if ($conn) {
                $examModel = new Exam($conn);
                $questionModel = new Question();
                
                // Get the exam
                $exam = $examModel->loadById($id) ? $examModel->toArray() : null;
                
                if (!$exam) {
                    setFlashMessage(translate('exam_not_found'), 'error');
                    header('Location: /exams');
                    exit;
                }
                
                // Get the questions
                $questions = $questionModel->getByExamId($id);
                
                // Calculate the score
                $score = 0;
                $totalPoints = 0;
                
                foreach ($questions as $question) {
                    $totalPoints += $question['points'];
                    
                    if (isset($answers[$question['id']])) {
                        $userAnswer = $answers[$question['id']];
                        $isCorrect = $questionModel->checkAnswer($question['id'], $userAnswer);
                        
                        if ($isCorrect) {
                            $score += $question['points'];
                        }
                        
                        // Save the user's answer
                        $examModel->saveAnswer($attemptId, $question['id'], $userAnswer, $isCorrect);
                    }
                }
                
                // Calculate the percentage
                $percentage = ($score / $totalPoints) * 100;
                
                // Determine if the user passed
                $passed = $percentage >= $exam['passing_score'];
                
                // Complete the attempt
                $examModel->completeAttempt($attemptId, $score, $percentage, $passed);
                
                // Redirect to the result page
                header('Location: /exam/' . $id . '/result/' . $attemptId);
                exit;
            } else {
                // Sample data if database connection is not available
                $attemptId = 1;
                
                // Redirect to the result page
                header('Location: /exam/' . $id . '/result/' . $attemptId);
                exit;
            }
        } else {
            // Redirect to the exam page
            header('Location: /exam/' . $id);
            exit;
        }
    }
    
    /**
     * Display the result of an exam attempt
     * 
     * @param int $id The exam ID
     * @param int $attempt_id The attempt ID
     */
    public function result($id, $attempt_id) {
        global $conn;
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage(translate('login_required'), 'warning');
            header('Location: /login');
            exit;
        }
        
        // Get the exam and attempt
        $exam = null;
        $attempt = null;
        $answers = [];
        
        if ($conn) {
            $examModel = new Exam($conn);
            $exam = $examModel->loadById($id) ? $examModel->toArray() : null;
            
            if ($exam) {
                $attempt = $examModel->getAttempt($attempt_id);
                
                if ($attempt && $attempt['user_id'] == $_SESSION['user_id']) {
                    $answers = $examModel->getAttemptAnswers($attempt_id);
                }
            }
        } else {
            // Sample data if database connection is not available
            $exams = [
                1 => [
                    'id' => 1,
                    'title' => 'CCNA Practice Exam 1',
                    'description' => 'Test your knowledge of CCNA concepts with this practice exam.',
                    'image' => 'ccna-exam.jpg',
                    'questions' => 60,
                    'duration' => 90,
                    'passing_score' => 70,
                    'course_id' => 1,
                    'course_title' => 'CCNA Certification'
                ]
            ];
            
            if (isset($exams[$id])) {
                $exam = $exams[$id];
                
                // Sample attempt
                $attempt = [
                    'id' => $attempt_id,
                    'user_id' => $_SESSION['user_id'],
                    'exam_id' => $id,
                    'score' => 48,
                    'percentage' => 80,
                    'passed' => true,
                    'started_at' => '2023-07-20 14:00:00',
                    'completed_at' => '2023-07-20 15:22:00',
                    'time_taken' => 82
                ];
                
                // Sample answers
                $answers = [
                    [
                        'question_id' => 1,
                        'question' => 'Which of the following is a valid IPv4 address?',
                        'type' => 'single_choice',
                        'options' => [
                            'A' => '192.168.1.256',
                            'B' => '10.0.0.1',
                            'C' => '172.31.256.1',
                            'D' => '256.256.256.256'
                        ],
                        'correct_answer' => 'B',
                        'user_answer' => 'B',
                        'is_correct' => true,
                        'explanation' => '10.0.0.1 is a valid IPv4 address in the private address range. The other options contain octets greater than 255, which is invalid for IPv4.'
                    ],
                    [
                        'question_id' => 2,
                        'question' => 'Which of the following protocols operate at the Transport layer of the OSI model? (Select all that apply)',
                        'type' => 'multiple_choice',
                        'options' => [
                            'A' => 'TCP',
                            'B' => 'IP',
                            'C' => 'UDP',
                            'D' => 'HTTP',
                            'E' => 'ICMP'
                        ],
                        'correct_answer' => ['A', 'C'],
                        'user_answer' => ['A', 'C'],
                        'is_correct' => true,
                        'explanation' => 'TCP (Transmission Control Protocol) and UDP (User Datagram Protocol) operate at the Transport layer (Layer 4) of the OSI model. IP operates at the Network layer, HTTP at the Application layer, and ICMP at the Network layer.'
                    ],
                    [
                        'question_id' => 3,
                        'question' => 'Match the following protocols with their default port numbers:',
                        'type' => 'drag_drop',
                        'items' => [
                            'HTTP',
                            'HTTPS',
                            'FTP',
                            'SSH'
                        ],
                        'drop_zones' => [
                            'Port 21',
                            'Port 22',
                            'Port 80',
                            'Port 443'
                        ],
                        'correct_answer' => [
                            'FTP' => 'Port 21',
                            'SSH' => 'Port 22',
                            'HTTP' => 'Port 80',
                            'HTTPS' => 'Port 443'
                        ],
                        'user_answer' => [
                            'FTP' => 'Port 21',
                            'SSH' => 'Port 22',
                            'HTTP' => 'Port 80',
                            'HTTPS' => 'Port 443'
                        ],
                        'is_correct' => true,
                        'explanation' => 'FTP uses port 21, SSH uses port 22, HTTP uses port 80, and HTTPS uses port 443 by default.'
                    ]
                ];
            }
        }
        
        // If exam or attempt not found, or attempt doesn't belong to the user, show 404 page
        if (!$exam || !$attempt || $attempt['user_id'] != $_SESSION['user_id']) {
            http_response_code(404);
            include APP_ROOT . '/templates/404.php';
            return;
        }
        
        // Get recommended courses for this exam
        $recommendedCourses = [];
        if ($conn) {
            $recommendedCourses = $examModel->getRecommendedCourses($exam['id']);
        } else {
            // Sample recommended courses data
            $recommendedCourses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'description' => 'Complete CCNA certification course covering all exam topics.'
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'description' => 'Learn the fundamentals of networking with this comprehensive course.'
                ]
            ];
        }
        
        // Get course if exam is associated with one
        $course = null;
        if ($exam['course_id'] && $conn) {
            $courseModel = new Course();
            $course = $courseModel->loadById($exam['course_id']) ? $courseModel->toArray() : null;
        }
        
        // Render the view
        $this->render('exam-result', [
            'exam' => $exam,
            'course' => $course,
            'result' => $result,
            'questions' => $questions,
            'answers' => $answers,
            'recommendedCourses' => $recommendedCourses,
            'pageTitle' => translate('exam_result') . ': ' . $exam['title']
        ]);
    }
}
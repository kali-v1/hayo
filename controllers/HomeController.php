<?php
/**
 * Home Controller
 * 
 * This controller handles the home page.
 */
require_once APP_ROOT . '/controllers/BaseController.php';

class HomeController extends BaseController {
    /**
     * Display the home page
     */
    public function index() {
        global $conn;
        
        // Get popular courses
        $popularCourses = [];
        if ($conn) {
            $course = new Course();
            $popularCourses = $course->getPopular(3);
        } else {
            // Sample data if database connection is not available
            $popularCourses = [
                [
                    'id' => 1,
                    'title' => 'CCNA Certification',
                    'description' => 'Comprehensive course for Cisco Certified Network Associate certification. Learn networking fundamentals, routing and switching, and network security.',
                    'image' => 'ccna.jpg',
                    'price' => 99.99,
                    'is_free' => 0
                ],
                [
                    'id' => 2,
                    'title' => 'Security+ Certification',
                    'description' => 'Complete preparation for CompTIA Security+ certification. Learn cybersecurity fundamentals, threats, vulnerabilities, and security controls.',
                    'image' => 'security-plus.jpg',
                    'price' => 79.99,
                    'is_free' => 0
                ],
                [
                    'id' => 3,
                    'title' => 'Network+ Basics',
                    'description' => 'Introduction to networking concepts for CompTIA Network+ certification. Free course for beginners.',
                    'image' => 'network-plus.jpg',
                    'price' => 0,
                    'is_free' => 1
                ]
            ];
        }
        
        // Get testimonials
        $testimonials = [
            [
                'name' => 'John Doe',
                'profile_image' => null,
                'course' => 'CCNA Certification',
                'rating' => 5,
                'review' => 'This course helped me pass my CCNA exam on the first try. The practice exams were particularly helpful.'
            ],
            [
                'name' => 'Jane Smith',
                'profile_image' => null,
                'course' => 'Security+ Certification',
                'rating' => 4,
                'review' => 'Great content and well-structured. I would have liked more hands-on exercises, but overall it was excellent.'
            ],
            [
                'name' => 'Mohammed Ali',
                'profile_image' => null,
                'course' => 'Network+ Basics',
                'rating' => 5,
                'review' => 'Perfect introduction to networking concepts. I had no prior knowledge and now I feel confident to move forward.'
            ]
        ];
        
        // Render the view
        $this->render('home', [
            'pageTitle' => translate('home'),
            'popularCourses' => $popularCourses,
            'testimonials' => $testimonials
        ]);
    }
}
?>
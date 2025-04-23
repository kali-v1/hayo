<?php
/**
 * Leaderboard Controller
 * 
 * This controller handles the leaderboard functionality.
 */
class LeaderboardController extends BaseController {
    /**
     * Display the leaderboard
     */
    public function index() {
        global $conn;
        
        // Get filter parameters
        $period = sanitizeInput($_GET['period'] ?? 'all_time');
        
        // Get leaderboard data
        $leaderboard = [];
        $userRank = null;
        
        if ($conn) {
            $userModel = new User(); // User constructor doesn't need $conn
            $leaderboard = $userModel->getLeaderboard($period);
            
            if (isset($_SESSION['user_id'])) {
                $userRank = $userModel->getUserRank($_SESSION['user_id'], $period);
            }
        } else {
            // Sample data if database connection is not available
            $leaderboard = [
                [
                    'rank' => 1,
                    'user_id' => 5,
                    'username' => 'alex_tech',
                    'profile_image' => null,
                    'points' => 1250,
                    'completed_exams' => 42,
                    'average_score' => 85.5
                ],
                [
                    'rank' => 2,
                    'user_id' => 12,
                    'username' => 'sarah_networks',
                    'profile_image' => null,
                    'points' => 1180,
                    'completed_exams' => 38,
                    'average_score' => 82.3
                ],
                [
                    'rank' => 3,
                    'user_id' => 8,
                    'username' => 'mike_security',
                    'profile_image' => null,
                    'points' => 1050,
                    'completed_exams' => 35,
                    'average_score' => 79.8
                ],
                [
                    'rank' => 4,
                    'user_id' => 20,
                    'username' => 'lisa_cloud',
                    'profile_image' => null,
                    'points' => 980,
                    'completed_exams' => 32,
                    'average_score' => 81.2
                ],
                [
                    'rank' => 5,
                    'user_id' => 15,
                    'username' => 'david_cisco',
                    'profile_image' => null,
                    'points' => 920,
                    'completed_exams' => 30,
                    'average_score' => 78.5
                ],
                [
                    'rank' => 6,
                    'user_id' => 25,
                    'username' => 'emma_aws',
                    'profile_image' => null,
                    'points' => 850,
                    'completed_exams' => 28,
                    'average_score' => 76.9
                ],
                [
                    'rank' => 7,
                    'user_id' => 18,
                    'username' => 'james_azure',
                    'profile_image' => null,
                    'points' => 780,
                    'completed_exams' => 25,
                    'average_score' => 74.2
                ],
                [
                    'rank' => 8,
                    'user_id' => 30,
                    'username' => 'olivia_linux',
                    'profile_image' => null,
                    'points' => 720,
                    'completed_exams' => 23,
                    'average_score' => 72.8
                ],
                [
                    'rank' => 9,
                    'user_id' => 22,
                    'username' => 'noah_python',
                    'profile_image' => null,
                    'points' => 680,
                    'completed_exams' => 21,
                    'average_score' => 71.5
                ],
                [
                    'rank' => 10,
                    'user_id' => 35,
                    'username' => 'sophia_devops',
                    'profile_image' => null,
                    'points' => 650,
                    'completed_exams' => 20,
                    'average_score' => 70.2
                ]
            ];
            
            if (isset($_SESSION['user_id'])) {
                if ($_SESSION['user_id'] == 5) {
                    $userRank = [
                        'rank' => 1,
                        'points' => 1250,
                        'completed_exams' => 42,
                        'average_score' => 85.5
                    ];
                } else {
                    $userRank = [
                        'rank' => 15,
                        'points' => 450,
                        'completed_exams' => 12,
                        'average_score' => 68.5
                    ];
                }
            }
        }
        
        // Set default values for pagination variables
        $totalUsers = count($leaderboard);
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $totalPages = ceil($totalUsers / $perPage);
        
        // Render the view
        $this->render('leaderboard', [
            'leaderboard' => $leaderboard,
            'userRank' => $userRank,
            'period' => $period,
            'totalUsers' => $totalUsers,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageTitle' => translate('leaderboard')
        ]);
    }
}
<?php
class AdminQuestionController {
    /**
     * Helper function to remove duplicate options and correct answers
     * 
     * @param array $options The options array
     * @param array $correct_answer The correct answers array
     * @param string $question_type The question type
     * @return array An array containing the unique options and correct answers
     */
    private function removeDuplicates($options, $correct_answer, $question_type) {
        // Debug input
        error_log("removeDuplicates - Input options: " . print_r($options, true));
        error_log("removeDuplicates - Input correct_answer: " . print_r($correct_answer, true));
        
        // Remove duplicate options
        $unique_options = [];
        $seen_options = [];
        
        foreach ($options as $option) {
            $option_key = trim($option);
            if (!isset($seen_options[$option_key])) {
                $unique_options[] = $option;
                $seen_options[$option_key] = true;
            }
        }
        
        // Remove duplicate correct answers
        $unique_correct = [];
        $seen_correct = [];
        
        if ($question_type === 'multiple_choice') {
            foreach ($correct_answer as $answer) {
                $answer_key = trim($answer);
                if (!isset($seen_correct[$answer_key])) {
                    $unique_correct[] = $answer;
                    $seen_correct[$answer_key] = true;
                }
            }
        } else {
            $unique_correct = $correct_answer;
        }
        
        // Debug output
        error_log("removeDuplicates - Output options: " . print_r($unique_options, true));
        error_log("removeDuplicates - Output correct_answer: " . print_r($unique_correct, true));
        
        return [
            'options' => $unique_options,
            'correct_answer' => $unique_correct
        ];
    }
    
    /**
     * Display the questions list
     */
    public function index() {
        global $conn;
        
        // Get exam ID from query string
        $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        
        // Get questions
        $questions = [];
        $exam = null;
        
        if ($conn) {
            try {
                // Get exam details if exam_id is provided
                if ($exam_id > 0) {
                    $stmt = $conn->prepare("
                        SELECT e.*, c.title as course_title
                        FROM exams e
                        LEFT JOIN courses c ON e.course_id = c.id
                        WHERE e.id = ?
                    ");
                    $stmt->execute([$exam_id]);
                    $exam = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                
                // Get questions
                $query = "
                    SELECT q.*, e.title as exam_title, c.title as course_title
                    FROM questions q
                    LEFT JOIN exams e ON q.exam_id = e.id
                    LEFT JOIN courses c ON e.course_id = c.id
                ";
                
                $params = [];
                
                if ($exam_id > 0) {
                    $query .= " WHERE q.exam_id = ?";
                    $params[] = $exam_id;
                }
                
                $query .= " ORDER BY q.created_at DESC";
                
                $stmt = $conn->prepare($query);
                $stmt->execute($params);
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Process questions
                foreach ($questions as &$question) {
                    $question['options'] = json_decode($question['options'], true) ?? [];
                    $question['correct_answer'] = json_decode($question['correct_answer'], true) ?? [];
                }
                unset($question);
                
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching questions: " . $e->getMessage());
            }
        }
        
        // Get exams for filter
        $exams = [];
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    SELECT e.*, c.title as course_title
                    FROM exams e
                    LEFT JOIN courses c ON e.course_id = c.id
                    ORDER BY e.title ASC
                ");
                $stmt->execute();
                $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching exams: " . $e->getMessage());
            }
        }
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/questions/index.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the question creation form
     */
    public function create() {
        global $conn;
        
        // Get exam ID from query string
        $exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
        
        // Get exams for dropdown
        $exams = [];
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    SELECT e.*, c.title as course_title
                    FROM exams e
                    LEFT JOIN courses c ON e.course_id = c.id
                    ORDER BY e.title ASC
                ");
                $stmt->execute();
                $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching exams: " . $e->getMessage());
            }
        }
        
        // Form data
        $form_data = [
            'exam_id' => $exam_id,
            'question_text' => '',
            'question_type' => 'multiple_choice',
            'points' => 1,
            'options' => [],
            'correct_option' => 0,
            'correct_options' => [],
            'drag_items' => [],
            'drop_zones' => []
        ];
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/questions/create.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Store a new question
     */
    public function store() {
        global $conn;
        
        // Validate input
        $exam_id = intval($_POST['exam_id'] ?? 0);
        $question_text = trim($_POST['question_text'] ?? '');
        $question_type = $_POST['question_type'] ?? '';
        $points = intval($_POST['points'] ?? 1);
        
        $errors = [];
        
        if ($exam_id <= 0) {
            $errors[] = 'يجب اختيار اختبار صالح للسؤال';
        }
        
        if (empty($question_text)) {
            $errors[] = 'نص السؤال مطلوب';
        }
        
        if (!in_array($question_type, ['single_choice', 'multiple_choice', 'drag_drop'])) {
            $errors[] = 'نوع السؤال غير صالح';
        }
        
        if ($points <= 0) {
            $errors[] = 'النقاط يجب أن تكون أكبر من صفر';
        }
        
        // Process options and correct answers based on question type
        $options = [];
        $correct_answer = [];
        
        if ($question_type === 'single_choice') {
            $raw_options = $_POST['options'] ?? [];
            $correct_option = isset($_POST['correct_option']) ? intval($_POST['correct_option']) : -1;
            
            // Filter out empty options
            foreach ($raw_options as $option) {
                if (!empty(trim($option))) {
                    $options[] = trim($option);
                }
            }
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if ($correct_option < 0 || $correct_option >= count($raw_options) || empty(trim($raw_options[$correct_option]))) {
                $errors[] = 'يجب تحديد إجابة صحيحة للسؤال';
            } else {
                $correct_answer = trim($raw_options[$correct_option]);
            }
        } else if ($question_type === 'multiple_choice') {
            $raw_options = $_POST['options'] ?? [];
            $correct_options = $_POST['correct_options'] ?? [];
            
            // Debug information
            error_log("Raw options: " . print_r($raw_options, true));
            error_log("Correct options indices: " . print_r($correct_options, true));
            
            // First, collect all valid options
            foreach ($raw_options as $option) {
                if (!empty(trim($option))) {
                    $options[] = trim($option);
                }
            }
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if (empty($correct_options)) {
                $errors[] = 'يجب تحديد إجابة صحيحة واحدة على الأقل';
            } else {
                // Collect correct answers directly from the raw options
                foreach ($correct_options as $index) {
                    $index = intval($index);
                    if (isset($raw_options[$index]) && !empty(trim($raw_options[$index]))) {
                        $correct_answer[] = trim($raw_options[$index]);
                    }
                }
                
                // Debug the correct answers
                error_log("Correct answers collected: " . print_r($correct_answer, true));
            }
        } else if ($question_type === 'drag_drop') {
            $drag_items = $_POST['drag_items'] ?? [];
            $drop_zones = $_POST['drop_zones'] ?? [];
            
            // Filter out empty items
            foreach ($drag_items as $index => $item) {
                if (!empty(trim($item)) && isset($drop_zones[$index]) && !empty(trim($drop_zones[$index]))) {
                    $options[] = trim($item);
                    $correct_answer[] = trim($drop_zones[$index]);
                }
            }
            
            if (empty($options) || empty($correct_answer)) {
                $errors[] = 'يجب إضافة عناصر السحب والإفلات للسؤال';
            }
        }
        
        // Debug before removing duplicates
        error_log("Before removeDuplicates - options: " . print_r($options, true));
        error_log("Before removeDuplicates - correct_answer: " . print_r($correct_answer, true));
        
        // For multiple choice questions, ensure correct answers are in the options list
        if ($question_type === 'multiple_choice' && !empty($correct_answer)) {
            // Make sure all correct answers are in the options list
            foreach ($correct_answer as $answer) {
                if (!in_array($answer, $options)) {
                    $options[] = $answer;
                }
            }
        }
        
        // Remove duplicates from options and correct answers
        $result = $this->removeDuplicates($options, $correct_answer, $question_type);
        $options = $result['options'];
        $correct_answer = $result['correct_answer'];
        
        // Debug after removing duplicates
        error_log("After removeDuplicates - options: " . print_r($options, true));
        error_log("After removeDuplicates - correct_answer: " . print_r($correct_answer, true));
        
        // Create question
        if ($conn && empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO questions (exam_id, question_text, question_type, options, correct_answer, points, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $exam_id,
                    $question_text,
                    $question_type,
                    json_encode($options),
                    json_encode($correct_answer),
                    $points
                ]);
                
                // Set success message
                setFlashMessage('تم إنشاء السؤال بنجاح', 'success');
                
                // Redirect to questions list
                header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
                exit;
            } catch (PDOException $e) {
                // Log error
                error_log("Error creating question: " . $e->getMessage());
                $errors[] = 'حدث خطأ أثناء إنشاء السؤال. يرجى المحاولة مرة أخرى.';
            }
        }
        
        // If there are errors, redirect back with errors and form data
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
            
            // Store form data in session
            $_SESSION['form_data'] = [
                'exam_id' => $exam_id,
                'question_text' => $question_text,
                'question_type' => $question_type,
                'points' => $points,
                'options' => $raw_options ?? [],
                'correct_option' => $correct_option ?? 0,
                'correct_options' => $correct_options ?? [],
                'drag_items' => $drag_items ?? [],
                'drop_zones' => $drop_zones ?? []
            ];
            
            // Redirect back to form
            header('Location: /admin/questions/create' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
            exit;
        }
    }
    
    /**
     * Display a question
     */
    public function view($id) {
        global $conn;
        
        // Get question
        $question = null;
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    SELECT q.*, e.title as exam_title, c.title as course_title
                    FROM questions q
                    LEFT JOIN exams e ON q.exam_id = e.id
                    LEFT JOIN courses c ON e.course_id = c.id
                    WHERE q.id = ?
                ");
                $stmt->execute([$id]);
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($question) {
                    $question['options'] = json_decode($question['options'], true) ?? [];
                    $question['correct_answer'] = json_decode($question['correct_answer'], true) ?? [];
                }
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching question: " . $e->getMessage());
            }
        }
        
        // If question not found, redirect to questions list
        if (!$question) {
            setFlashMessage('السؤال غير موجود', 'error');
            header('Location: /admin/questions');
            exit;
        }
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/questions/view.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Display the question edit form
     */
    public function edit($id) {
        global $conn;
        
        // Get question
        $question = null;
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    SELECT q.*, e.title as exam_title, c.title as course_title
                    FROM questions q
                    LEFT JOIN exams e ON q.exam_id = e.id
                    LEFT JOIN courses c ON e.course_id = c.id
                    WHERE q.id = ?
                ");
                $stmt->execute([$id]);
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($question) {
                    $question['options'] = json_decode($question['options'], true) ?? [];
                    $question['correct_answer'] = json_decode($question['correct_answer'], true) ?? [];
                }
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching question: " . $e->getMessage());
            }
        }
        
        // If question not found, redirect to questions list
        if (!$question) {
            setFlashMessage('السؤال غير موجود', 'error');
            header('Location: /admin/questions');
            exit;
        }
        
        // Get exams for dropdown
        $exams = [];
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("
                    SELECT e.*, c.title as course_title
                    FROM exams e
                    LEFT JOIN courses c ON e.course_id = c.id
                    ORDER BY e.title ASC
                ");
                $stmt->execute();
                $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Log error
                error_log("Error fetching exams: " . $e->getMessage());
            }
        }
        
        // Form data
        $form_data = [
            'id' => $question['id'],
            'exam_id' => $question['exam_id'],
            'question_text' => $question['question_text'],
            'question_type' => $question['question_type'],
            'points' => $question['points'],
            'options' => $question['options'],
            'correct_answer' => $question['correct_answer']
        ];
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include ADMIN_ROOT . '/templates/questions/edit.php';
        
        // Get the content
        $contentView = ob_get_clean();
        
        // Include the layout
        include ADMIN_ROOT . '/templates/layout.php';
    }
    
    /**
     * Update a question
     */
    public function update($id) {
        global $conn;
        
        // Validate input
        $exam_id = intval($_POST['exam_id'] ?? 0);
        $question_text = trim($_POST['question_text'] ?? '');
        $question_type = $_POST['question_type'] ?? '';
        $points = intval($_POST['points'] ?? 1);
        
        $errors = [];
        
        if ($exam_id <= 0) {
            $errors[] = 'يجب اختيار اختبار صالح للسؤال';
        }
        
        if (empty($question_text)) {
            $errors[] = 'نص السؤال مطلوب';
        }
        
        if (!in_array($question_type, ['single_choice', 'multiple_choice', 'drag_drop'])) {
            $errors[] = 'نوع السؤال غير صالح';
        }
        
        if ($points <= 0) {
            $errors[] = 'النقاط يجب أن تكون أكبر من صفر';
        }
        
        // Process options and correct answers based on question type
        $options = [];
        $correct_answer = [];
        
        if ($question_type === 'single_choice') {
            $raw_options = $_POST['options'] ?? [];
            $correct_option = isset($_POST['correct_option']) ? intval($_POST['correct_option']) : -1;
            
            // Filter out empty options
            foreach ($raw_options as $option) {
                if (!empty(trim($option))) {
                    $options[] = trim($option);
                }
            }
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if ($correct_option < 0 || $correct_option >= count($raw_options) || empty(trim($raw_options[$correct_option]))) {
                $errors[] = 'يجب تحديد إجابة صحيحة للسؤال';
            } else {
                $correct_answer = trim($raw_options[$correct_option]);
            }
        } else if ($question_type === 'multiple_choice') {
            $raw_options = $_POST['options'] ?? [];
            $correct_options = $_POST['correct_options'] ?? [];
            
            // Debug information
            error_log("Raw options: " . print_r($raw_options, true));
            error_log("Correct options indices: " . print_r($correct_options, true));
            
            // First, collect all valid options
            foreach ($raw_options as $option) {
                if (!empty(trim($option))) {
                    $options[] = trim($option);
                }
            }
            
            if (empty($options)) {
                $errors[] = 'يجب إضافة خيارات للسؤال';
            }
            
            if (empty($correct_options)) {
                $errors[] = 'يجب تحديد إجابة صحيحة واحدة على الأقل';
            } else {
                // Collect correct answers directly from the raw options
                foreach ($correct_options as $index) {
                    $index = intval($index);
                    if (isset($raw_options[$index]) && !empty(trim($raw_options[$index]))) {
                        $correct_answer[] = trim($raw_options[$index]);
                    }
                }
                
                // Debug the correct answers
                error_log("Correct answers collected: " . print_r($correct_answer, true));
            }
        } else if ($question_type === 'drag_drop') {
            $drag_items = $_POST['drag_items'] ?? [];
            $drop_zones = $_POST['drop_zones'] ?? [];
            
            // Filter out empty items
            foreach ($drag_items as $index => $item) {
                if (!empty(trim($item)) && isset($drop_zones[$index]) && !empty(trim($drop_zones[$index]))) {
                    $options[] = trim($item);
                    $correct_answer[] = trim($drop_zones[$index]);
                }
            }
            
            if (empty($options) || empty($correct_answer)) {
                $errors[] = 'يجب إضافة عناصر السحب والإفلات للسؤال';
            }
        }
        
        // Debug before removing duplicates
        error_log("Before removeDuplicates - options: " . print_r($options, true));
        error_log("Before removeDuplicates - correct_answer: " . print_r($correct_answer, true));
        
        // For multiple choice questions, ensure correct answers are in the options list
        if ($question_type === 'multiple_choice' && !empty($correct_answer)) {
            // Make sure all correct answers are in the options list
            foreach ($correct_answer as $answer) {
                if (!in_array($answer, $options)) {
                    $options[] = $answer;
                }
            }
        }
        
        // Remove duplicates from options and correct answers
        $result = $this->removeDuplicates($options, $correct_answer, $question_type);
        $options = $result['options'];
        $correct_answer = $result['correct_answer'];
        
        // Debug after removing duplicates
        error_log("After removeDuplicates - options: " . print_r($options, true));
        error_log("After removeDuplicates - correct_answer: " . print_r($correct_answer, true));
        
        // Update question
        if ($conn && empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    UPDATE questions
                    SET exam_id = ?, question_text = ?, question_type = ?, options = ?, correct_answer = ?, points = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $exam_id,
                    $question_text,
                    $question_type,
                    json_encode($options),
                    json_encode($correct_answer),
                    $points,
                    $id
                ]);
                
                // Set success message
                setFlashMessage('تم تحديث السؤال بنجاح', 'success');
                
                // Redirect to questions list
                header('Location: /admin/questions' . ($exam_id > 0 ? "?exam_id=$exam_id" : ''));
                exit;
            } catch (PDOException $e) {
                // Log error
                error_log("Error updating question: " . $e->getMessage());
                $errors[] = 'حدث خطأ أثناء تحديث السؤال. يرجى المحاولة مرة أخرى.';
            }
        }
        
        // If there are errors, redirect back with errors and form data
        if (!empty($errors)) {
            setFlashMessage(implode('<br>', $errors), 'error');
            
            // Store form data in session
            $_SESSION['form_data'] = [
                'id' => $id,
                'exam_id' => $exam_id,
                'question_text' => $question_text,
                'question_type' => $question_type,
                'points' => $points,
                'options' => $raw_options ?? [],
                'correct_option' => $correct_option ?? 0,
                'correct_options' => $correct_options ?? [],
                'drag_items' => $drag_items ?? [],
                'drop_zones' => $drop_zones ?? []
            ];
            
            // Redirect back to form
            header('Location: /admin/questions/edit/' . $id);
            exit;
        }
    }
    
    /**
     * Delete a question
     */
    public function delete($id) {
        global $conn;
        
        if ($conn) {
            try {
                $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
                $stmt->execute([$id]);
                
                // Set success message
                setFlashMessage('تم حذف السؤال بنجاح', 'success');
            } catch (PDOException $e) {
                // Log error
                error_log("Error deleting question: " . $e->getMessage());
                
                // Set error message
                setFlashMessage('حدث خطأ أثناء حذف السؤال', 'error');
            }
        }
        
        // Redirect to questions list
        header('Location: /admin/questions');
        exit;
    }
}
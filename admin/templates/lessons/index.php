<?php include_once __DIR__ . '/includes/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إدارة دروس الدورة: <?php echo htmlspecialchars($course['title']); ?></h2>
        <a href="/admin/courses/<?php echo $course['id']; ?>/lessons/create" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> إضافة درس جديد
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card neo-brutalism-card">
                <div class="card-header">
                    <h5 class="mb-0">إحصائيات الدروس</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>إجمالي الدروس:</span>
                        <span class="fw-bold"><?php echo $totalLessons; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الدروس المنشورة:</span>
                        <span class="fw-bold"><?php echo $publishedLessons; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>المدة الإجمالية:</span>
                        <span class="fw-bold"><?php echo $totalDuration; ?> دقيقة</span>
                    </div>
                    <div class="mt-3">
                        <a href="/admin/courses/<?php echo $course['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للدورة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card neo-brutalism-card">
                <div class="card-header">
                    <h5 class="mb-0">قائمة الدروس</h5>
                </div>
                <div class="card-body">
                <?php if (empty($lessons)): ?>
                    <div class="alert alert-info">
                        لا توجد دروس لهذه الدورة. قم بإضافة درس جديد.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="neo-table" id="lessons-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">عنوان الدرس</th>
                                    <th width="15%">المدة</th>
                                    <th width="10%">مجاني</th>
                                    <th width="15%">الحالة</th>
                                    <th width="25%">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-lessons">
                                <?php foreach ($lessons as $index => $lesson): ?>
                                    <tr data-lesson-id="<?php echo $lesson['id']; ?>">
                                        <td><?php echo $lesson['order_number']; ?></td>
                                        <td><?php echo htmlspecialchars($lesson['title']); ?></td>
                                        <td><?php echo $lesson['duration']; ?> دقيقة</td>
                                        <td>
                                            <?php if ($lesson['is_free']): ?>
                                                <span class="neo-badge neo-badge-success">نعم</span>
                                            <?php else: ?>
                                                <span class="neo-badge neo-badge-secondary">لا</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($lesson['status'] === 'published'): ?>
                                                <span class="neo-badge neo-badge-success">منشور</span>
                                            <?php else: ?>
                                                <span class="neo-badge neo-badge-secondary">مسودة</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                                <form action="/admin/courses/<?php echo $course['id']; ?>/lessons/<?php echo $lesson['id']; ?>/delete" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟');">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button id="save-order" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ الترتيب
                        </button>
                        <small class="text-muted ms-2">يمكنك سحب وإفلات الصفوف لتغيير ترتيب الدروس</small>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery UI for drag and drop functionality -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Make the table rows sortable
    $("#sortable-lessons").sortable({
        placeholder: "ui-state-highlight",
        update: function(event, ui) {
            // Update the order numbers visually
            $("#sortable-lessons tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
            });
        }
    });
    
    // Save the new order
    $("#save-order").click(function() {
        var lessonOrder = [];
        
        $("#sortable-lessons tr").each(function() {
            lessonOrder.push($(this).data("lesson-id"));
        });
        
        $.ajax({
            url: "/admin/courses/<?php echo $course['id']; ?>/lessons/reorder",
            method: "POST",
            data: {
                lesson_order: JSON.stringify(lessonOrder)
            },
            success: function(response) {
                var result = JSON.parse(response);
                
                if (result.success) {
                    alert(result.message);
                } else {
                    alert("حدث خطأ: " + result.message);
                }
            },
            error: function() {
                alert("حدث خطأ أثناء حفظ الترتيب");
            }
        });
    });
});
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
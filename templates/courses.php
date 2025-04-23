<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="fw-bold"><?php echo translate('courses'); ?></h1>
        <p class="lead"><?php echo translate('courses_subtitle'); ?></p>
    </div>
    <div class="col-md-4">
        <form action="/courses" method="get" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="<?php echo translate('search_courses'); ?>" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary"><?php echo translate('search'); ?></button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="brutalism-card bg-light p-3 shadow">
            <div class="d-flex flex-wrap">
                <div class="me-3 mb-2">
                    <label class="me-2"><?php echo translate('filter_by'); ?>:</label>
                    <div class="btn-group" role="group">
                        <a href="/courses" class="btn btn-outline-primary <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>"><?php echo translate('all'); ?></a>
                        <a href="/courses?filter=free" class="btn btn-outline-primary <?php echo isset($_GET['filter']) && $_GET['filter'] === 'free' ? 'active' : ''; ?>"><?php echo translate('free'); ?></a>
                        <a href="/courses?filter=paid" class="btn btn-outline-primary <?php echo isset($_GET['filter']) && $_GET['filter'] === 'paid' ? 'active' : ''; ?>"><?php echo translate('paid'); ?></a>
                    </div>
                </div>
                
                <div class="me-3 mb-2">
                    <label class="me-2"><?php echo translate('sort_by'); ?>:</label>
                    <select name="sort" class="form-select form-select-sm d-inline-block w-auto" onchange="window.location.href=this.value">
                        <option value="/courses?sort=newest" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'newest' ? 'selected' : ''; ?>><?php echo translate('newest'); ?></option>
                        <option value="/courses?sort=oldest" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'oldest' ? 'selected' : ''; ?>><?php echo translate('oldest'); ?></option>
                        <option value="/courses?sort=price_low" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'price_low' ? 'selected' : ''; ?>><?php echo translate('price_low_high'); ?></option>
                        <option value="/courses?sort=price_high" <?php echo isset($_GET['sort']) && $_GET['sort'] === 'price_high' ? 'selected' : ''; ?>><?php echo translate('price_high_low'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($courses)): ?>
<div class="brutalism-card bg-white p-5 text-center shadow">
    <h3 class="fw-bold"><?php echo translate('no_courses_found'); ?></h3>
    <p><?php echo translate('no_courses_message'); ?></p>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($courses as $course): ?>
    <div class="col-md-4">
        <div class="brutalism-card bg-white p-0 shadow h-100">
            <div class="course-image">
                <img src="/assets/images/courses/<?php echo $course['image'] ?? 'default-course.jpg'; ?>" alt="<?php echo $course['title']; ?>" class="img-fluid w-100">
                <?php if ($course['is_free']): ?>
                <span class="badge bg-success position-absolute top-0 end-0 m-2"><?php echo translate('free'); ?></span>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <h3 class="fw-bold"><?php echo $course['title']; ?></h3>
                <p class="text-muted"><?php echo substr($course['description'], 0, 100); ?>...</p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <?php if (!$course['is_free']): ?>
                        <span class="fw-bold text-primary"><?php echo formatCurrency($course['price']); ?></span>
                        <?php else: ?>
                        <span class="fw-bold text-success"><?php echo translate('free'); ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="/course/<?php echo $course['id']; ?>" class="btn btn-primary"><?php echo translate('view_course'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-5">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="/courses?page=<?php echo $currentPage - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="/courses?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="/courses?page=<?php echo $currentPage + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<?php endif; ?>
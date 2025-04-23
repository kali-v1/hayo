<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="fw-bold"><?php echo translate('leaderboard'); ?></h1>
        <p class="lead"><?php echo translate('leaderboard_subtitle'); ?></p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="brutalism-card bg-light p-3 shadow">
            <div class="d-flex flex-wrap">
                <div class="me-3 mb-2">
                    <label class="me-2"><?php echo translate('time_period'); ?>:</label>
                    <div class="btn-group" role="group">
                        <a href="/leaderboard?period=all_time" class="btn btn-outline-primary <?php echo $period === 'all_time' ? 'active' : ''; ?>"><?php echo translate('all_time'); ?></a>
                        <a href="/leaderboard?period=monthly" class="btn btn-outline-primary <?php echo $period === 'monthly' ? 'active' : ''; ?>"><?php echo translate('monthly'); ?></a>
                        <a href="/leaderboard?period=weekly" class="btn btn-outline-primary <?php echo $period === 'weekly' ? 'active' : ''; ?>"><?php echo translate('weekly'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($userRank)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="brutalism-card bg-primary text-white p-4 shadow">
            <div class="d-flex align-items-center">
                <div class="me-4">
                    <h2 class="fw-bold mb-0">#<?php echo $userRank['rank']; ?></h2>
                    <p class="mb-0"><?php echo translate('your_rank'); ?></p>
                </div>
                <div class="me-4">
                    <h2 class="fw-bold mb-0"><?php echo $userRank['points']; ?></h2>
                    <p class="mb-0"><?php echo translate('points'); ?></p>
                </div>
                <div class="me-4">
                    <h2 class="fw-bold mb-0"><?php echo $userRank['completed_exams']; ?></h2>
                    <p class="mb-0"><?php echo translate('exams_completed'); ?></p>
                </div>
                <div>
                    <h2 class="fw-bold mb-0"><?php echo $userRank['average_score']; ?>%</h2>
                    <p class="mb-0"><?php echo translate('average_score'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (empty($leaderboard)): ?>
<div class="brutalism-card bg-white p-5 text-center shadow">
    <h3 class="fw-bold"><?php echo translate('no_leaderboard_data'); ?></h3>
    <p><?php echo translate('no_leaderboard_message'); ?></p>
</div>
<?php else: ?>
<div class="row">
    <div class="col-12">
        <div class="brutalism-card bg-white p-0 shadow">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo translate('rank'); ?></th>
                            <th><?php echo translate('user'); ?></th>
                            <th><?php echo translate('points'); ?></th>
                            <th><?php echo translate('exams_completed'); ?></th>
                            <th><?php echo translate('average_score'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboard as $user): ?>
                        <tr class="<?php echo isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['user_id'] ? 'table-primary' : ''; ?>">
                            <td class="fw-bold">
                                <?php if ($user['rank'] == 1): ?>
                                <span class="badge bg-warning me-2">🥇</span>
                                <?php elseif ($user['rank'] == 2): ?>
                                <span class="badge bg-secondary me-2">🥈</span>
                                <?php elseif ($user['rank'] == 3): ?>
                                <span class="badge bg-danger me-2">🥉</span>
                                <?php else: ?>
                                #<?php echo $user['rank']; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2">
                                        <?php if ($user['profile_image']): ?>
                                        <img src="/assets/images/users/<?php echo $user['profile_image']; ?>" alt="<?php echo $user['username']; ?>" class="rounded-circle" width="40" height="40">
                                        <?php else: ?>
                                        <div class="avatar-placeholder rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <?php echo $user['username']; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $user['points']; ?></td>
                            <td><?php echo $user['completed_exams']; ?></td>
                            <td><?php echo $user['average_score']; ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="d-flex justify-content-center mt-5">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="/leaderboard?page=<?php echo $currentPage - 1; ?>&period=<?php echo $period; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="/leaderboard?page=<?php echo $i; ?>&period=<?php echo $period; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="/leaderboard?page=<?php echo $currentPage + 1; ?>&period=<?php echo $period; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<?php endif; ?>
<?php endif; ?>
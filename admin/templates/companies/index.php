
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">الشركات</h5>
                    <a href="/admin/companies/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة شركة جديدة
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($flashMessage)): ?>
                        <div class="alert alert-<?php echo $flashMessage['type']; ?>"><?php echo $flashMessage['message']; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <form action="/admin/companies" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="بحث..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الشعار</th>
                                    <th>الاسم</th>
                                    <th>الوصف</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($companies)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد شركات</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($companies as $index => $company): ?>
                                        <tr>
                                            <td><?php echo ($page - 1) * $perPage + $index + 1; ?></td>
                                            <td>
                                                <?php if (!empty($company['logo'])): ?>
                                                    <img src="<?php echo htmlspecialchars($company['logo']); ?>" alt="<?php echo htmlspecialchars($company['name']); ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">لا يوجد شعار</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($company['name']); ?></td>
                                            <td><?php echo mb_substr(htmlspecialchars($company['description']), 0, 50) . (mb_strlen($company['description']) > 50 ? '...' : ''); ?></td>
                                            <td><?php echo htmlspecialchars($company['created_at']); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/admin/companies/view/<?php echo $company['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="/admin/companies/edit/<?php echo $company['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $company['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                                
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $company['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $company['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $company['id']; ?>">تأكيد الحذف</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                هل أنت متأكد من حذف الشركة: <?php echo htmlspecialchars($company['name']); ?>؟
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                                <a href="/admin/companies/delete/<?php echo $company['id']; ?>" class="btn btn-danger">حذف</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/companies?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="/admin/companies?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="/admin/companies?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

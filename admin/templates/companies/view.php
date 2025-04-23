
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">تفاصيل الشركة</h5>
                    <div>
                        <a href="/admin/companies/edit/<?php echo $company->id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="/admin/companies" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($flashMessage)): ?>
                        <div class="alert alert-<?php echo $flashMessage['type']; ?>"><?php echo $flashMessage['message']; ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">معلومات الشركة</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">الاسم</th>
                                            <td><?php echo htmlspecialchars($company->name); ?></td>
                                        </tr>
                                        <tr>
                                            <th>الوصف</th>
                                            <td><?php echo nl2br(htmlspecialchars($company->description)); ?></td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإنشاء</th>
                                            <td><?php echo htmlspecialchars($company->created_at); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">شعار الشركة</h5>
                                    <?php if (!empty($company->logo)): ?>
                                        <img src="<?php echo htmlspecialchars($company->logo); ?>" alt="<?php echo htmlspecialchars($company->name); ?>" class="img-fluid">
                                    <?php else: ?>
                                        <div class="alert alert-info">لا يوجد شعار للشركة</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">الدورات المرتبطة بالشركة</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($courses)): ?>
                                        <div class="alert alert-info">لا توجد دورات مرتبطة بهذه الشركة</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>العنوان</th>
                                                        <th>السعر</th>
                                                        <th>مجاني</th>
                                                        <th>المدرب</th>
                                                        <th>تاريخ الإنشاء</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($courses as $index => $course): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                                                            <td><?php echo htmlspecialchars($course['price']); ?></td>
                                                            <td>
                                                                <?php if ($course['is_free']): ?>
                                                                    <span class="badge badge-success">نعم</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-danger">لا</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($course['admin_username']); ?></td>
                                                            <td><?php echo htmlspecialchars($course['created_at']); ?></td>
                                                            <td>
                                                                <a href="/admin/courses/view/<?php echo $course['id']; ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">الاختبارات المرتبطة بالشركة</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($exams)): ?>
                                        <div class="alert alert-info">لا توجد اختبارات مرتبطة بهذه الشركة</div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>العنوان</th>
                                                        <th>الدورة</th>
                                                        <th>المدة (دقيقة)</th>
                                                        <th>مجاني</th>
                                                        <th>تاريخ الإنشاء</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($exams as $index => $exam): ?>
                                                        <tr>
                                                            <td><?php echo $index + 1; ?></td>
                                                            <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                                            <td><?php echo htmlspecialchars($exam['course_title']); ?></td>
                                                            <td><?php echo htmlspecialchars($exam['duration_minutes']); ?></td>
                                                            <td>
                                                                <?php if ($exam['is_free']): ?>
                                                                    <span class="badge badge-success">نعم</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-danger">لا</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($exam['created_at']); ?></td>
                                                            <td>
                                                                <a href="/admin/exams/view/<?php echo $exam['id']; ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

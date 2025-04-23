
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">تعديل الشركة</h5>
                    <a href="/admin/companies" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($flashMessage)): ?>
                        <div class="alert alert-<?php echo $flashMessage['type']; ?>"><?php echo $flashMessage['message']; ?></div>
                    <?php endif; ?>
                    
                    <form action="/admin/companies/update/<?php echo $company->id; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">اسم الشركة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($company->name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف الشركة</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($company->description); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">شعار الشركة</label>
                            <?php if (!empty($company->logo)): ?>
                                <div class="mb-2">
                                    <img src="<?php echo htmlspecialchars($company->logo); ?>" alt="<?php echo htmlspecialchars($company->name); ?>" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="delete_logo" name="delete_logo" value="1">
                                    <label class="custom-control-label" for="delete_logo">حذف الشعار الحالي</label>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">الحد الأقصى لحجم الملف: 2 ميجابايت. الصيغ المدعومة: JPG, PNG, GIF.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

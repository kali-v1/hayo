
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">إضافة شركة جديدة</h5>
                    <a href="/admin/companies" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($flashMessage)): ?>
                        <div class="alert alert-<?php echo $flashMessage['type']; ?>"><?php echo $flashMessage['message']; ?></div>
                    <?php endif; ?>
                    
                    <form action="/admin/companies/store" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">اسم الشركة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">وصف الشركة</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">شعار الشركة</label>
                            <input type="file" class="form-control-file" id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">الحد الأقصى لحجم الملف: 2 ميجابايت. الصيغ المدعومة: JPG, PNG, GIF.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

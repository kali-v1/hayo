<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="fw-bold"><?php echo translate('change_password'); ?></h1>
        <p class="lead"><?php echo translate('change_password_subtitle'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="brutalism-card bg-white p-4 shadow">
            <form action="/profile/password" method="post">
                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current_password" class="form-label"><?php echo translate('current_password'); ?> *</label>
                    <input type="password" class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password" required>
                    <?php if (isset($errors['current_password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['current_password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- New Password -->
                <div class="mb-3">
                    <label for="new_password" class="form-label"><?php echo translate('new_password'); ?> *</label>
                    <input type="password" class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" id="new_password" name="new_password" required>
                    <?php if (isset($errors['new_password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['new_password']; ?></div>
                    <?php endif; ?>
                    <div class="form-text"><?php echo translate('password_requirements'); ?></div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label"><?php echo translate('confirm_password'); ?> *</label>
                    <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="/profile" class="btn btn-outline-secondary me-md-2"><?php echo translate('cancel'); ?></a>
                    <button type="submit" class="btn btn-primary"><?php echo translate('update_password'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
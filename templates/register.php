<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="brutalism-card bg-white p-4 p-md-5 shadow">
            <h2 class="fw-bold text-center mb-4"><?php echo translate('register'); ?></h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $field => $error): ?>
                        <p class="mb-0"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="/register" method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label"><?php echo translate('name'); ?></label>
                        <input type="text" class="form-control form-control-lg <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label"><?php echo translate('username'); ?></label>
                        <input type="text" class="form-control form-control-lg <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label"><?php echo translate('email'); ?></label>
                    <input type="email" class="form-control form-control-lg <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label"><?php echo translate('password'); ?></label>
                        <input type="password" class="form-control form-control-lg <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label"><?php echo translate('confirm_password'); ?></label>
                        <input type="password" class="form-control form-control-lg <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input <?php echo isset($errors['agree_terms']) ? 'is-invalid' : ''; ?>" id="agree_terms" name="agree_terms" required>
                    <label class="form-check-label" for="agree_terms">
                        <?php echo translate('agree_terms'); ?> <a href="/terms-of-service"><?php echo translate('terms_of_service'); ?></a>
                    </label>
                    <?php if (isset($errors['agree_terms'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['agree_terms']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><?php echo translate('register'); ?></button>
                </div>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <p><?php echo translate('already_have_account'); ?> <a href="/login"><?php echo translate('login'); ?></a></p>
            </div>
        </div>
    </div>
</div>
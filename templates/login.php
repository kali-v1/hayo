<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="brutalism-card bg-white p-4 p-md-5 shadow">
            <h2 class="fw-bold text-center mb-4"><?php echo translate('login'); ?></h2>
            
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-0"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="/login" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label"><?php echo translate('email'); ?></label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label"><?php echo translate('password'); ?></label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember"><?php echo translate('remember_me'); ?></label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><?php echo translate('login'); ?></button>
                </div>
                
                <div class="text-center mt-3">
                    <a href="/forgot-password"><?php echo translate('forgot_password'); ?></a>
                </div>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <p><?php echo translate('dont_have_account'); ?> <a href="/register"><?php echo translate('register_now'); ?></a></p>
            </div>
        </div>
    </div>
</div>
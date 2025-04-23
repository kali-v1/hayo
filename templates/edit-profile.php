<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="fw-bold"><?php echo translate('edit_profile'); ?></h1>
        <p class="lead"><?php echo translate('edit_profile_subtitle'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="brutalism-card bg-white p-4 shadow">
            <form action="/profile/update" method="post" enctype="multipart/form-data">
                <!-- Profile Image -->
                <div class="mb-4 text-center">
                    <?php if ($user->getProfileImage()): ?>
                        <img src="/assets/images/users/<?php echo $user->getProfileImage(); ?>" alt="<?php echo $user->getFullName(); ?>" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px; font-size: 4rem;">
                            <?php echo strtoupper(substr($user->getFirstName() ?? '', 0, 1) . substr($user->getLastName() ?? '', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label"><?php echo translate('profile_image'); ?></label>
                        <input class="form-control <?php echo isset($errors['profile_image']) ? 'is-invalid' : ''; ?>" type="file" id="profile_image" name="profile_image">
                        <?php if (isset($errors['profile_image'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['profile_image']; ?></div>
                        <?php endif; ?>
                        <div class="form-text"><?php echo translate('profile_image_help'); ?></div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <!-- First Name -->
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label"><?php echo translate('first_name'); ?> *</label>
                        <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" id="first_name" name="first_name" value="<?php echo $user->getFirstName(); ?>" required>
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label"><?php echo translate('last_name'); ?> *</label>
                        <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" id="last_name" name="last_name" value="<?php echo $user->getLastName(); ?>" required>
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Bio -->
                <div class="mb-3">
                    <label for="bio" class="form-label"><?php echo translate('bio'); ?></label>
                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo $user->getBio(); ?></textarea>
                    <div class="form-text"><?php echo translate('bio_help'); ?></div>
                </div>
                
                <!-- Contact Information -->
                <h4 class="mt-4 mb-3"><?php echo translate('contact_information'); ?></h4>
                
                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label"><?php echo translate('phone'); ?></label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user->getPhone(); ?>">
                </div>
                
                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label"><?php echo translate('address'); ?></label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $user->getAddress(); ?>">
                </div>
                
                <div class="row mb-3">
                    <!-- City -->
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label"><?php echo translate('city'); ?></label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $user->getCity(); ?>">
                    </div>
                    
                    <!-- Country -->
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label"><?php echo translate('country'); ?></label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo $user->getCountry(); ?>">
                    </div>
                </div>
                
                <!-- Postal Code -->
                <div class="mb-3">
                    <label for="postal_code" class="form-label"><?php echo translate('postal_code'); ?></label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo $user->getPostalCode(); ?>">
                </div>
                
                <!-- Social Profiles -->
                <h4 class="mt-4 mb-3"><?php echo translate('social_profiles'); ?></h4>
                
                <!-- Website -->
                <div class="mb-3">
                    <label for="website" class="form-label"><?php echo translate('website'); ?></label>
                    <input type="url" class="form-control" id="website" name="website" value="<?php echo $user->getWebsite(); ?>">
                    <div class="form-text"><?php echo translate('website_help'); ?></div>
                </div>
                
                <div class="row mb-3">
                    <!-- Facebook -->
                    <div class="col-md-6 mb-3">
                        <label for="facebook" class="form-label"><?php echo translate('facebook'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">facebook.com/</span>
                            <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo $user->getFacebook(); ?>">
                        </div>
                    </div>
                    
                    <!-- Twitter -->
                    <div class="col-md-6 mb-3">
                        <label for="twitter" class="form-label"><?php echo translate('twitter'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">twitter.com/</span>
                            <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo $user->getTwitter(); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <!-- LinkedIn -->
                    <div class="col-md-6 mb-3">
                        <label for="linkedin" class="form-label"><?php echo translate('linkedin'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">linkedin.com/in/</span>
                            <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo $user->getLinkedin(); ?>">
                        </div>
                    </div>
                    
                    <!-- Instagram -->
                    <div class="col-md-6 mb-3">
                        <label for="instagram" class="form-label"><?php echo translate('instagram'); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">instagram.com/</span>
                            <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo $user->getInstagram(); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="/profile" class="btn btn-outline-secondary me-md-2"><?php echo translate('cancel'); ?></a>
                    <button type="submit" class="btn btn-primary"><?php echo translate('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
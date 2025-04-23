    </main>

    <!-- Footer -->
    <footer class="footer mt-5 py-4 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3 class="footer-title"><?php echo APP_NAME; ?></h3>
                    <p class="footer-description"><?php echo translate('footer_description'); ?></p>
                </div>
                <div class="col-md-2">
                    <h4 class="footer-subtitle"><?php echo translate('quick_links'); ?></h4>
                    <ul class="footer-links list-unstyled">
                        <li><a href="/" class="text-white"><?php echo translate('home'); ?></a></li>
                        <li><a href="/courses" class="text-white"><?php echo translate('courses'); ?></a></li>
                        <li><a href="/exams" class="text-white"><?php echo translate('exams'); ?></a></li>
                        <li><a href="/leaderboard" class="text-white"><?php echo translate('leaderboard'); ?></a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h4 class="footer-subtitle"><?php echo translate('support'); ?></h4>
                    <ul class="footer-links list-unstyled">
                        <li><a href="/contact" class="text-white"><?php echo translate('contact_us'); ?></a></li>
                        <li><a href="/faq" class="text-white"><?php echo translate('faq'); ?></a></li>
                        <li><a href="/privacy" class="text-white"><?php echo translate('privacy_policy'); ?></a></li>
                        <li><a href="/terms" class="text-white"><?php echo translate('terms_of_service'); ?></a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="footer-subtitle"><?php echo translate('newsletter'); ?></h4>
                    <p><?php echo translate('newsletter_description'); ?></p>
                    <form class="footer-newsletter">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="<?php echo translate('email_placeholder'); ?>" required>
                            <button class="btn btn-primary" type="submit"><?php echo translate('subscribe'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="footer-bottom mt-4 pt-3 border-top">
                <div class="row">
                    <div class="col-md-6">
                        <p class="footer-copyright mb-0">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. <?php echo translate('all_rights_reserved'); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <ul class="footer-social list-inline mb-0">
                            <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                            <li class="list-inline-item"><a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
</body>
</html>
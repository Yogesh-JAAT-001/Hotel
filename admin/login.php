<?php
require_once '../config.php';

// Redirect if already logged in
if (isAdmin()) {
    redirect('index.php');
}

$page_title = 'Admin Login';
include '../includes/header.php';
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="card card-luxury shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="luxury-header text-warning"><?php echo APP_NAME; ?></h2>
                            <p class="text-muted">Admin Panel Access</p>
                        </div>
                        
                        <form id="loginForm" onsubmit="handleLogin(event)">
                            <div class="form-outline mb-4">
                                <input type="text" id="identifier" name="identifier" class="form-control" required autocomplete="username">
                                <label class="form-label" for="identifier">Email or Username</label>
                            </div>
                            
                            <div class="form-outline mb-4">
                                <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me" value="1">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            
                            <button type="submit" class="btn btn-luxury w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="../" class="text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-2"></i>Back to Website
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function handleLogin(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const loginData = {
        identifier: formData.get('identifier'),
        password: formData.get('password'),
        user_type: 'admin',
        remember_me: formData.get('remember_me') === '1'
    };
    
    try {
        const response = await apiRequest('../api/auth/login.php', {
            method: 'POST',
            body: JSON.stringify(loginData)
        });
        
        if (response.success) {
            showToast('Login successful! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = response.redirect || 'index.php';
            }, 1000);
        }
    } catch (error) {
        showToast(error.message || 'Invalid email/username or password', 'danger');
    }
}
</script>

<?php include '../includes/footer.php'; ?>

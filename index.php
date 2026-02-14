<?php
require_once 'config.php';
require_once 'includes/media-helper.php';

$page_title = 'Welcome to ' . APP_NAME;
$additional_css = '
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="preload" as="image" href="' . appPath('/IMAGES/live/hero/HOTEL.png') . '">
<link rel="stylesheet" href="' . appPath('/assets/css/hero.css') . '">
';
include 'includes/header.php';

// Get featured rooms
try {
    $stmt = $pdo->query("
        SELECT 
            r.room_id,
            r.room_no,
            r.tier,
            r.rent,
            r.description,
            r.image_path,
            rt.name as room_type_name,
            GROUP_CONCAT(DISTINCT rf.name) as features
        FROM ROOMS r
        JOIN ROOM_TYPE rt ON r.room_type_id = rt.room_type_id
        LEFT JOIN ROOM_FEATURES_MAP rfm ON r.room_id = rfm.room_id
        LEFT JOIN ROOM_FEATURES rf ON rfm.feature_id = rf.room_feature_id
        WHERE r.status = 'Available'
        GROUP BY r.room_id
        ORDER BY r.tier
        LIMIT 6
    ");
    $featured_rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_rooms = [];
}
?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-luxury fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo appPath('/'); ?>"><?php echo APP_NAME; ?></a>
        
        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarNav">
            <i class="fas fa-bars text-white"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo appPath('/index.php'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo appPath('/user/rooms.php'); ?>">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo appPath('/user/dining.php'); ?>">Dining</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo appPath('/user/contact.php'); ?>">Contact</a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-mdb-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="user/dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="user/reservations.php">My Reservations</a></li>
                            <li><a class="dropdown-item" href="user/profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="logout()">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-mdb-toggle="modal" data-mdb-target="#loginModal">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-mdb-toggle="modal" data-mdb-target="#registerModal">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section imperial-hero" id="homeHero">
    <div class="imperial-hero-media"
         style="background-image: url('<?php echo appPath('/IMAGES/live/hero/HOTEL.png'); ?>');"
         aria-hidden="true"></div>
    <div class="imperial-hero-overlay imperial-hero-overlay-base"></div>
    <div class="imperial-hero-overlay imperial-hero-overlay-vignette"></div>

    <div class="container imperial-hero-content">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-9 text-center">
                <h1 class="imperial-title mb-3">THE HEARTLAND ABODE</h1>
                <h2 class="imperial-title-highlight mb-3">AI-ENABLED HOTEL MANAGEMENT SYSTEM</h2>
                <p class="imperial-subtitle mb-4">Experience Luxury, Intelligence, and Precision</p>
                <div class="imperial-cta-group justify-content-center">
                    <a href="<?php echo appPath('/user/rooms.php'); ?>" class="btn imperial-btn imperial-btn-gold">
                        Explore Rooms
                    </a>
                    <a href="<?php echo appPath('/user/dining.php'); ?>" class="btn imperial-btn imperial-btn-glass">
                        Explore Dining
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Booking Section -->
<section class="py-5 bg-light" id="booking">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-luxury shadow-lg">
                    <div class="card-body p-4">
                        <h4 class="luxury-header text-center mb-4">Quick Room Search</h4>
                        <form id="quickSearchForm" onsubmit="searchRooms(event)">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-outline">
                                        <input type="date" id="checkIn" name="check_in" class="form-control" required>
                                        <label class="form-label" for="checkIn">Check-in</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-outline">
                                        <input type="date" id="checkOut" name="check_out" class="form-control" required>
                                        <label class="form-label" for="checkOut">Check-out</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" name="tier">
                                        <option value="">Any Tier</option>
                                        <option value="1">1 TIER (Luxury)</option>
                                        <option value="2">2 TIER (Deluxe)</option>
                                        <option value="3">3 TIER (Standard)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" name="guests">
                                        <option value="1">1 Guest</option>
                                        <option value="2" selected>2 Guests</option>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-luxury w-100">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="luxury-header">Why Choose <?php echo APP_NAME; ?>?</h2>
            <p class="text-muted">Experience the finest in luxury hospitality</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-crown fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">Luxury Accommodations</h5>
                        <p class="text-muted">Premium rooms and suites with world-class amenities and stunning views.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-utensils fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">Fine Dining</h5>
                        <p class="text-muted">Exquisite culinary experiences with both vegetarian and non-vegetarian options.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-concierge-bell fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">24/7 Service</h5>
                        <p class="text-muted">Round-the-clock room service and concierge assistance for all your needs.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-wifi fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">High-Speed WiFi</h5>
                        <p class="text-muted">Complimentary high-speed internet access throughout the property.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-car fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">Valet Parking</h5>
                        <p class="text-muted">Complimentary valet parking service for all our guests.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100 text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-3x text-warning"></i>
                        </div>
                        <h5 class="luxury-header">Secure & Safe</h5>
                        <p class="text-muted">Advanced security systems and safety protocols for your peace of mind.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Rooms Section -->
<section class="py-5 bg-light" id="featured-rooms">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="luxury-header">Featured Rooms</h2>
            <p class="text-muted">Discover our premium accommodations</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_rooms as $room): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card card-luxury h-100">
                    <div class="position-relative">
                        <img src="<?php echo htmlspecialchars(resolveRoomImageUrl($room['image_path'] ?? '', $room['room_type_name'] ?? '', $room['room_no'] ?? null)); ?>" 
                             class="card-img-top" alt="Room <?php echo $room['room_no']; ?>" style="height: 250px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge badge-luxury"><?php echo $room['room_type_name']; ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title luxury-header">Room <?php echo $room['room_no']; ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars(stripUiSeedPrefix((string)($room['description'] ?? ''))); ?></p>
                        
                        <?php if ($room['features']): ?>
                        <div class="mb-3">
                            <small class="text-muted">Features:</small>
                            <div class="mt-1">
                                <?php foreach (explode(',', $room['features']) as $feature): ?>
                                    <span class="badge-feature me-1"><?php echo trim($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-warning mb-0">₹<?php echo number_format($room['rent']); ?></h4>
                                <small class="text-muted">per night</small>
                            </div>
                            <a href="user/room-detail.php?id=<?php echo $room['room_id']; ?>" class="btn btn-luxury">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="user/rooms.php" class="btn btn-luxury btn-lg">
                <i class="fas fa-bed me-2"></i>View All Rooms
            </a>
        </div>
    </div>
</section>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login to Your Account</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-outline mb-3">
                        <input type="email" id="loginEmail" name="email" class="form-control" required>
                        <label class="form-label" for="loginEmail">Email Address</label>
                    </div>
                    <div class="form-outline mb-3">
                        <input type="password" id="loginPassword" name="password" class="form-control" required>
                        <label class="form-label" for="loginPassword">Password</label>
                    </div>
                    <button type="submit" class="btn btn-luxury w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="#" onclick="switchToRegister()">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Account</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="registerForm" onsubmit="handleRegister(event)">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-outline mb-3">
                                <input type="text" id="registerName" name="name" class="form-control" required>
                                <label class="form-label" for="registerName">Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-outline mb-3">
                                <input type="tel" id="registerPhone" name="phone" class="form-control" required minlength="10" maxlength="15" inputmode="numeric">
                                <label class="form-label" for="registerPhone">Phone Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-outline mb-3">
                        <input type="email" id="registerEmail" name="email" class="form-control" required>
                        <label class="form-label" for="registerEmail">Email Address</label>
                    </div>
                    <div class="form-outline mb-3">
                        <input type="password" id="registerPassword" name="password" class="form-control" required>
                        <label class="form-label" for="registerPassword">Password</label>
                    </div>
                    <div class="form-outline mb-3">
                        <input type="password" id="registerConfirmPassword" name="confirm_password" class="form-control" required>
                        <label class="form-label" for="registerConfirmPassword">Confirm Password</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">
                            I agree to the Terms of Service and Privacy Policy
                        </label>
                    </div>
                    <button type="submit" class="btn btn-luxury w-100">Create Account</button>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="#" onclick="switchToLogin()">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set minimum dates for booking form
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('checkIn').min = today;
    document.getElementById('checkOut').min = tomorrowStr;
    
    // Update checkout min date when checkin changes
    document.getElementById('checkIn').addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        checkInDate.setDate(checkInDate.getDate() + 1);
        document.getElementById('checkOut').min = checkInDate.toISOString().split('T')[0];
    });
});

// Search rooms
function searchRooms(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const params = new URLSearchParams(formData);
    window.location.href = `user/rooms.php?${params.toString()}`;
}

// Handle login
async function handleLogin(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const loginData = {
        email: formData.get('email'),
        password: formData.get('password'),
        user_type: 'guest'
    };
    
    try {
        const response = await apiRequest('api/auth/login.php', {
            method: 'POST',
            body: JSON.stringify(loginData)
        });
        
        if (response.success) {
            showToast('Login successful!', 'success');
            setTimeout(() => {
                window.location.href = response.redirect || 'user/dashboard.php';
            }, 600);
        }
    } catch (error) {
        showToast(error.message, 'danger');
    }
}

// Handle registration
async function handleRegister(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const name = String(formData.get('name') || '').trim();
    const email = String(formData.get('email') || '').trim();
    const phone = String(formData.get('phone') || '').trim();
    const password = String(formData.get('password') || '');
    const confirmPassword = String(formData.get('confirm_password') || '');

    if (!name || !email || !phone || !password || !confirmPassword) {
        showToast('Required fields missing: name, email, phone, password, and confirm password are required', 'warning');
        return;
    }
    if (name.length < 2 || name.length > 100) {
        showToast('Name must be between 2 and 100 characters', 'warning');
        return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showToast('Invalid email format', 'warning');
        return;
    }

    const digitsOnlyPhone = phone.replace(/\D+/g, '');
    if (digitsOnlyPhone.length < 10 || digitsOnlyPhone.length > 15) {
        showToast('Phone number must contain 10 to 15 digits', 'warning');
        return;
    }
    if (password.length < 8 || password.length > 72) {
        showToast('Weak password: use 8 to 72 characters', 'warning');
        return;
    }
    if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password) || !/[^A-Za-z0-9]/.test(password)) {
        showToast('Weak password: include uppercase, lowercase, number, and special character', 'warning');
        return;
    }
    if (password !== confirmPassword) {
        showToast('Password and confirm password do not match', 'warning');
        return;
    }

    const registerData = {
        name,
        email,
        phone: digitsOnlyPhone,
        password,
        confirm_password: confirmPassword
    };
    
    try {
        const response = await apiRequest('api/auth/register.php', {
            method: 'POST',
            body: JSON.stringify(registerData)
        });
        
        if (response.success) {
            showToast('Registration successful!', 'success');
            setTimeout(() => {
                window.location.href = response.redirect || 'user/dashboard.php';
            }, 600);
        }
    } catch (error) {
        showToast(error.message, 'danger');
    }
}

// Switch between login and register modals
function switchToRegister() {
    const loginModal = mdb.Modal.getInstance(document.getElementById('loginModal'));
    loginModal.hide();
    setTimeout(() => {
        const registerModal = new mdb.Modal(document.getElementById('registerModal'));
        registerModal.show();
    }, 300);
}

function switchToLogin() {
    const registerModal = mdb.Modal.getInstance(document.getElementById('registerModal'));
    registerModal.hide();
    setTimeout(() => {
        const loginModal = new mdb.Modal(document.getElementById('loginModal'));
        loginModal.show();
    }, 300);
}

// Logout function
async function logout() {
    if (confirm('Are you sure you want to logout?')) {
        try {
            await apiRequest('api/auth/logout.php', { method: 'POST' });
            location.reload();
        } catch (error) {
            showToast('Unable to logout right now. Please try again.', 'danger');
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>

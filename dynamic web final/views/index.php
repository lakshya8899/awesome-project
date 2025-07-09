<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>University LMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to Our Learning Management System</h2>
                <p>Empowering Education for a Better Tomorrow</p>
                <div class="buttons">
                    <a href="../auth/login.php" class="btn-login">Log-in</a>
                    <a href="../auth/signup.php" class="btn-signup">Sign-up</a>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section class="about">
            <h3>About Us</h3>
            <p>Our Learning Management System (LMS) is designed to provide an engaging and effective platform for students and educators. With features such as course management, communication tools, and performance tracking, we aim to empower the future of education.</p>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials">
            <h3>What Our Users Say</h3>
            <div class="testimonial">
                <p>"This LMS has been a game-changer for my online learning experience. It's easy to navigate and has all the tools I need to succeed." - John D.</p>
            </div>
            <div class="testimonial">
                <p>"As an educator, I can easily manage my courses and engage with my students. The LMS is user-friendly and highly effective." - Jane S.</p>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta">
            <h3>Ready to Get Started?</h3>
            <p>Join us today and take your education to the next level.</p>
            <div class="cta-buttons">
                <a href="../auth/signup.php" class="btn-signup">Sign-up</a>
                <a href="../auth/login.php" class="btn-login">Log-in</a>
            </div>
        </section>
        
    </main>

    <?php include '../includes/footer.php'; ?>

</body>
</html>

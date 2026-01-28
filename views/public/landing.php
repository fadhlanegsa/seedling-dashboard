<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Stok Bibit Persemaian Indonesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 2rem;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .hero-content {
            text-align: center;
            color: white;
            z-index: 1;
            max-width: 900px;
        }

        .hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: fadeInUp 0.8s ease;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .btn {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Statistics Section */
        .stats-section {
            padding: 5rem 2rem;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #718096;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(30px);
        }

        .stat-card.visible {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
        }

        .stat-icon.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-icon.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon.pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .stat-icon.teal { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

        .stat-number {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            color: #718096;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }
        }

        /* Scroll indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
            color: white;
            font-size: 2rem;
            opacity: 0.8;
            cursor: pointer;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            40% {
                transform: translateX(-50%) translateY(-10px);
            }
            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        /* Carousel Section */
        .carousel-section {
            padding: 4rem 2rem;
            background: white;
        }

        .carousel-container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .carousel-wrapper {
            position: relative;
            width: 100%;
            height: 500px;
        }

        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 3rem 2rem 2rem;
        }

        .carousel-caption h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .carousel-caption p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.9);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            color: #667eea;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .carousel-btn:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .carousel-btn.prev {
            left: 20px;
        }

        .carousel-btn.next {
            right: 20px;
        }

        .carousel-dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background: white;
            width: 30px;
            border-radius: 6px;
        }

        .dot:hover {
            background: rgba(255,255,255,0.8);
        }

        @media (max-width: 768px) {
            .carousel-wrapper {
                height: 350px;
            }

            .carousel-caption h3 {
                font-size: 1.5rem;
            }

            .carousel-caption p {
                font-size: 0.95rem;
            }

            .carousel-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .carousel-btn.prev {
                left: 10px;
            }

            .carousel-btn.next {
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Dashboard Stok Bibit Persemaian Indonesia</h1>
            <p>Platform terpadu untuk monitoring dan distribusi bibit tanaman di seluruh Indonesia</p>
            <div class="cta-buttons">
                <a href="<?= url('public/request-form') ?>" class="btn btn-primary">
                    <i class="fas fa-seedling"></i>
                    Ajukan Permintaan Bibit
                </a>
                <a href="<?= url('public/stock-search') ?>" class="btn btn-outline">
                    <i class="fas fa-search"></i>
                    Cari Stok Bibit
                </a>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="container">
            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <div class="carousel-slide active">
                        <img src="assets/images/carousel/slide1.jpg" alt="Slide 1">
                        <div class="carousel-caption">
                            <h3>Bibit Berkualitas untuk Indonesia Hijau</h3>
                            <p>Menyediakan berbagai jenis bibit tanaman berkualitas tinggi</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/images/carousel/slide2.jpg" alt="Slide 2">
                        <div class="carousel-caption">
                            <h3>Distribusi ke Seluruh Nusantara</h3>
                            <p>Jangkauan luas untuk mendukung penghijauan nasional</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="assets/images/carousel/slide3.jpg" alt="Slide 3">
                        <div class="carousel-caption">
                            <h3>Kolaborasi dengan BPDAS</h3>
                            <p>Bekerja sama dengan berbagai BPDAS di Indonesia</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Arrows -->
                <button class="carousel-btn prev" onclick="moveSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn next" onclick="moveSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <!-- Dots Indicator -->
                <div class="carousel-dots">
                    <span class="dot active" onclick="currentSlide(0)"></span>
                    <span class="dot" onclick="currentSlide(1)"></span>
                    <span class="dot" onclick="currentSlide(2)"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2>Statistik Nasional</h2>
                <p>Data real-time persediaan dan distribusi bibit di Indonesia</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card" data-delay="0">
                    <div class="stat-icon green">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['total_stock'] ?>">0</div>
                    <div class="stat-label">Total Stok Bibit</div>
                </div>

                <div class="stat-card" data-delay="100">
                    <div class="stat-icon blue">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['total_distributed'] ?>">0</div>
                    <div class="stat-label">Bibit Terdistribusi</div>
                </div>

                <div class="stat-card" data-delay="200">
                    <div class="stat-icon orange">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['total_requests'] ?>">0</div>
                    <div class="stat-label">Total Permintaan</div>
                </div>

                <div class="stat-card" data-delay="300">
                    <div class="stat-icon purple">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['total_bpdas'] ?>">0</div>
                    <div class="stat-label">BPDAS Aktif</div>
                </div>

                <div class="stat-card" data-delay="400">
                    <div class="stat-icon pink">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['total_provinces'] ?>">0</div>
                    <div class="stat-label">Provinsi Terlayani</div>
                </div>

                <div class="stat-card" data-delay="500">
                    <div class="stat-icon teal">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number" data-target="<?= $stats['approved_requests'] ?>">0</div>
                    <div class="stat-label">Permintaan Disetujui</div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Counter Animation
        function animateCounter(element, target, duration = 2000) {
            const start = 0;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target.toLocaleString('id-ID');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString('id-ID');
                }
            }, 16);
        }

        // Intersection Observer for scroll animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const delay = parseInt(card.dataset.delay) || 0;
                    
                    setTimeout(() => {
                        card.classList.add('visible');
                        
                        // Start counter animation
                        const counter = card.querySelector('.stat-number');
                        const target = parseInt(counter.dataset.target);
                        animateCounter(counter, target);
                    }, delay);
                    
                    observer.unobserve(card);
                }
            });
        }, {
            threshold: 0.2
        });

        // Observe all stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            observer.observe(card);
        });

        // Smooth scroll for chevron
        document.querySelector('.scroll-indicator').addEventListener('click', () => {
            document.querySelector('.carousel-section').scrollIntoView({
                behavior: 'smooth'
            });
        });

        // Carousel functionality
        let currentSlideIndex = 0;
        let autoSlideInterval;

        function showSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.dot');
            
            // Wrap around
            if (index >= slides.length) {
                currentSlideIndex = 0;
            } else if (index < 0) {
                currentSlideIndex = slides.length - 1;
            } else {
                currentSlideIndex = index;
            }
            
            // Remove active class from all
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Add active class to current
            slides[currentSlideIndex].classList.add('active');
            dots[currentSlideIndex].classList.add('active');
        }

        function moveSlide(direction) {
            showSlide(currentSlideIndex + direction);
            resetAutoSlide();
        }

        function currentSlide(index) {
            showSlide(index);
            resetAutoSlide();
        }

        function autoSlide() {
            moveSlide(1);
        }

        function resetAutoSlide() {
            clearInterval(autoSlideInterval);
            autoSlideInterval = setInterval(autoSlide, 5000); // Change slide every 5 seconds
        }

        // Start auto-slide
        resetAutoSlide();
    </script>
</body>
</html>

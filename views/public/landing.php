    <!-- Hero Section (Split Layout) -->
    <section class="hero-split">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-text-content">
                    <h1 class="hero-title-large">
                        AYO TANAM POHON
                    </h1>
                    <p class="hero-description">
                        Platform resmi Kementerian Kehutanan untuk akses informasi dan distribusi bibit tanaman hutan gratis bagi masyarakat. Pantau stok, ajukan permintaan, dan berkontribusi untuk lingkungan.
                    </p>
                    <div class="cta-buttons" style="justify-content: flex-start;">
                        <a href="<?= url('public/request-form') ?>" class="btn btn-warning btn-lg shadow-lg">
                            <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                        </a>
                        <a href="<?= url('home/search') ?>" class="btn btn-outline btn-lg" style="border-color: var(--primary-color); color: var(--primary-color);">
                            <i class="fas fa-search"></i> Cari Bibit
                        </a>
                    </div>
                </div>
                <div class="hero-image-wrapper">
                    <!-- Hero Slideshow -->
                    <div class="hero-slideshow">
                        <img src="<?= asset('images/hero-planting.svg') ?>" class="hero-image hero-image-transparent active" alt="Ilustrasi Penanaman">
                        <img src="<?= asset('images/hero-watering.svg') ?>" class="hero-image hero-image-transparent" alt="Ilustrasi Penyiraman">
                        <img src="<?= asset('images/hero-forest.svg') ?>" class="hero-image hero-image-transparent" alt="Ilustrasi Hutan Lestari">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="container">
            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <div class="carousel-slide active">
                        <img src="<?= asset('images/carousel/slide1.jpg') ?>" alt="Slide 1" onerror="this.src='https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?auto=format&fit=crop&w=1200&q=80'">
                        <div class="carousel-caption">
                            <h3>Bibit Berkualitas untuk Indonesia Hijau</h3>
                            <p>Menyediakan berbagai jenis bibit tanaman berkualitas tinggi</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/slide2.jpg') ?>" alt="Slide 2" onerror="this.src='https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=80'">
                        <div class="carousel-caption">
                            <h3>Distribusi ke Seluruh Nusantara</h3>
                            <p>Jangkauan luas untuk mendukung penghijauan nasional</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/slide3.jpg') ?>" alt="Slide 3" onerror="this.src='https://images.unsplash.com/photo-1576085898323-218337e3e43c?auto=format&fit=crop&w=1200&q=80'">
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
            <div class="section-title text-center">
                <h2>Statistik Nasional</h2>
                <p>Transparansi data distribusi bibit untuk Indonesia yang lebih hijau</p>
            </div>
            
            <div class="stats-grid-wide">
                <div class="stat-card-wide" data-delay="0">
                    <div class="stat-icon-wrapper green">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_stock'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Total Stok Bibit</div>
                    </div>
                </div>

                <div class="stat-card-wide" data-delay="100">
                    <div class="stat-icon-wrapper gold">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_distributed'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Bibit Terdistribusi</div>
                    </div>
                </div>

                <div class="stat-card-wide" data-delay="200">
                    <div class="stat-icon-wrapper brown">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_requests'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Permintaan Masuk</div>
                    </div>
                </div>

                <div class="stat-card-wide" data-delay="300">
                    <div class="stat-icon-wrapper green">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_bpdas'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Unit BPDAS</div>
                    </div>
                </div>

                 <div class="stat-card-wide" data-delay="400">
                    <div class="stat-icon-wrapper gold">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_provinces'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Cakupan Provinsi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News / Highlight Section -->
    <section class="news-section">
        <div class="container">
            <div class="section-title text-center">
                <h2>Kabar Kehutanan</h2>
                <p>Program unggulan dan berita terbaru seputar penghijauan</p>
            </div>

            <div class="news-grid">
                <!-- News Item 1 -->
                <div class="news-card">
                    <img src="https://images.unsplash.com/photo-1576085898323-218337e3e43c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60" alt="News 1" class="news-image">
                    <div class="news-content">
                        <div class="news-date">8 Februari 2026</div>
                        <h3 class="news-title">Program 'Satu Juta Pohon' untuk IKN Nusantara</h3>
                        <p class="news-excerpt">Kementerian Kehutanan meluncurkan program percepatan penghijauan di kawasan Ibu Kota Nusantara dengan fokus pada tanaman endemik...</p>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- News Item 2 -->
                <div class="news-card">
                    <img src="https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60" alt="News 2" class="news-image">
                    <div class="news-content">
                        <div class="news-date">5 Februari 2026</div>
                        <h3 class="news-title">Distribusi Bibit Buah Gratis Periode Q1 2026</h3>
                        <p class="news-excerpt">Masyarakat kini dapat mengajukan permintaan bibit buah produktif melalui dashboard BPDAS terdekat mulai bulan ini...</p>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- News Item 3 -->
                <div class="news-card">
                    <img src="https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60" alt="News 3" class="news-image">
                    <div class="news-content">
                        <div class="news-date">28 Januari 2026</div>
                        <h3 class="news-title">Modernisasi Persemaian Rumpin Bogor</h3>
                        <p class="news-excerpt">Peningkatan fasilitas di Persemaian Rumpin diharapkan dapat meningkatkan kapasitas produksi bibit hingga 20% tahun ini...</p>
                        <a href="#" class="news-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
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
                        const counter = card.querySelector('.stat-value');
                        if (counter) {
                            const target = parseInt(counter.dataset.target);
                            animateCounter(counter, target);
                        }
                    }, delay);
                    
                    observer.unobserve(card);
                }
            });
        }, {
            threshold: 0.2
        });

        // Observe all stat cards
        document.querySelectorAll('.stat-card-wide').forEach(card => {
            observer.observe(card);
        });

        // Carousel functionality
        let currentSlideIndex = 0;
        let autoSlideInterval;

        function showSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.dot');
            
            if (slides.length === 0) return;

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
            autoSlideInterval = setInterval(autoSlide, 5000); 
        }

        // Start auto-slide when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            resetAutoSlide();
            // Hero Slideshow Logic
        const heroSlides = document.querySelectorAll('.hero-slideshow .hero-image-transparent');
        if (heroSlides.length > 0) {
            let currentHeroSlide = 0;
            setInterval(() => {
                // Remove active class from current
                heroSlides[currentHeroSlide].classList.remove('active');
                
                // Move to next
                currentHeroSlide = (currentHeroSlide + 1) % heroSlides.length;
                
                // Add active class to next
                heroSlides[currentHeroSlide].classList.add('active');
            }, 4000); // Change every 4 seconds
        }
    });
    </script>

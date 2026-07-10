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
                    
                    <!-- Search Barcode Section -->
                    <div class="barcode-search-box mt-4 p-3 rounded-lg shadow-sm" style="background: rgba(255, 255, 255, 0.95); border: 1.5px solid var(--primary-color); max-width: 480px; box-shadow: 0 6px 20px rgba(45, 80, 22, 0.1) !important; border-radius: 8px;">
                        <form action="<?= url('public/trace') ?>" method="GET" class="d-flex align-items-center justify-content-between" style="gap: 8px; margin-bottom: 0;">
                            <div class="position-relative flex-grow-1" style="width: 100%;">
                                <span class="position-absolute" style="left: 12px; top: 12px; color: var(--primary-color); z-index: 10;">
                                    <i class="fas fa-qrcode"></i>
                                </span>
                                <input type="text" name="code" class="form-control" placeholder="Masukkan Kode Barcode Bibit..." required style="padding-left: 36px; border-radius: 6px; border: 1px solid #ced4da; height: 42px; font-size: 0.9rem; width: 100%; box-shadow: none;">
                            </div>
                            <button type="submit" class="btn btn-success font-weight-bold" style="background-color: var(--primary-color); border-color: var(--primary-color); height: 42px; border-radius: 6px; white-space: nowrap; font-size: 0.9rem; padding: 0 16px;">
                                <i class="fas fa-search mr-1"></i> Lacak
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2 mb-0" style="font-size: 0.75rem; font-style: italic; line-height: 1.2;">
                            <i class="fas fa-info-circle mr-1"></i> Contoh: <strong>PE-45-3-12-7-42-260415-88</strong>
                        </small>
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
                        <img src="<?= asset('images/carousel/1.jpeg') ?>" alt="Slide 1" onerror="this.src='<?= asset('images/carousel/slide1.svg') ?>'">
                        <div class="carousel-caption">
                            <h3>Bibit Berkualitas untuk Indonesia Hijau</h3>
                            <p>Menyediakan berbagai jenis bibit tanaman berkualitas tinggi</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/2.jpeg') ?>" alt="Slide 2" onerror="this.src='<?= asset('images/carousel/slide2.svg') ?>'">
                        <div class="carousel-caption">
                            <h3>Distribusi ke Seluruh Nusantara</h3>
                            <p>Jangkauan luas untuk mendukung penghijauan nasional</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/3.jpeg') ?>" alt="Slide 3" onerror="this.src='<?= asset('images/carousel/slide3.svg') ?>'">
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

                <div class="stat-card-wide" data-delay="500">
                    <div class="stat-icon-wrapper brown">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value" data-target="<?= $stats['total_nurseries'] ?? 0 ?>">0</div>
                        <div class="stat-desc">Persemaian Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni Pilihan Section -->
    <section class="news-section testimonial-section">
        <div class="container">
            <div class="section-title text-center" style="margin-bottom: 1.5rem;">
                <h2>Testimoni Pilihan</h2>
                <p>Apa kata masyarakat yang telah menerima bibit gratis dari kami</p>
            </div>

            <?php
                $avgRating = $surveyStats['average'] ?? 0;
                $totalSurveys = $surveyStats['total'] ?? 0;
                $fullStars = (int)floor($avgRating);
            ?>
            <div class="testimonial-score text-center">
                <div class="testimonial-score-value"><?= number_format($avgRating, 1) ?></div>
                <div class="testimonial-score-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa<?= $i <= $fullStars ? 's' : 'r' ?> fa-star"></i>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-score-desc">Rata-rata penilaian dari <?= formatNumber($totalSurveys) ?> ulasan pengguna layanan</p>
            </div>

            <?php if (!empty($testimonials)): ?>
                <div class="testimonial-carousel-wrapper">
                    <button type="button" class="testimonial-nav testimonial-nav-prev" id="testiPrev" aria-label="Sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="testimonial-track" id="testiTrack">
                        <?php foreach ($testimonials as $t): ?>
                            <div class="testimonial-card">
                                <div class="testimonial-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?= $i <= (int)$t['rating'] ? 's' : 'r' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-comment">"<?= htmlspecialchars($t['comment']) ?>"</p>

                                <table class="testimonial-detail-table">
                                    <tbody>
                                        <tr>
                                            <td><i class="fas fa-hashtag"></i> No. Permintaan</td>
                                            <td><?= htmlspecialchars($t['request_number'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-water"></i> BPDAS</td>
                                            <td><?= htmlspecialchars($t['bpdas_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-seedling"></i> Jenis Bibit</td>
                                            <td><?= htmlspecialchars($t['seedling_name'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-truck"></i> Serah Terima</td>
                                            <td><?= !empty($t['delivery_date']) ? formatDate($t['delivery_date'], 'j M Y') : '-' ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="testimonial-author">
                                    <i class="fas fa-user-circle"></i>
                                    <div>
                                        <strong><?= htmlspecialchars($t['full_name'] ?? 'Warga') ?></strong>
                                        <div class="testimonial-date">Ulasan diberikan <?= formatDate($t['created_at'], 'j M Y') ?></div>
                                    </div>
                                    <span class="testimonial-verified-badge" title="Permintaan terverifikasi di sistem">
                                        <i class="fas fa-check-circle"></i> Terverifikasi
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="testimonial-nav testimonial-nav-next" id="testiNext" aria-label="Berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php else: ?>
                <div class="news-empty-landing">
                    <i class="fas fa-comment-dots"></i>
                    <p>Belum ada ulasan yang dipublikasikan.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <style>
        .testimonial-score { margin-bottom: 2rem; }
        .testimonial-score-value { font-size: 2.5rem; font-weight: 700; color: var(--primary-dark, #1b5e20); line-height: 1; }
        .testimonial-score-stars { color: #ffc107; font-size: 1.25rem; margin: 0.5rem 0; }
        .testimonial-score-desc { color: var(--text-light, #666); }
        .testimonial-carousel-wrapper { display: flex; align-items: center; gap: 0.75rem; }
        .testimonial-track { display: flex; gap: 1.25rem; overflow-x: auto; scroll-behavior: smooth; flex: 1; padding: 0.5rem 0.25rem 1rem; scrollbar-width: thin; }
        .testimonial-card {
            flex: 0 0 340px;
            background: var(--white, #fff);
            border-radius: 12px;
            box-shadow: var(--shadow, 0 2px 10px rgba(0,0,0,0.08));
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .testimonial-stars { color: #ffc107; margin-bottom: 0.75rem; }
        .testimonial-comment { font-style: italic; color: #333; margin-bottom: 1rem; min-height: 4.5em; }
        .testimonial-detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            background: var(--light-bg, #f7f9f7);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .testimonial-detail-table td {
            padding: 0.4rem 0.6rem;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            vertical-align: top;
        }
        .testimonial-detail-table tr:last-child td { border-bottom: none; }
        .testimonial-detail-table td:first-child {
            color: var(--text-light, #777);
            white-space: nowrap;
            width: 40%;
        }
        .testimonial-detail-table td:first-child i {
            width: 14px;
            color: var(--primary-color, #2e7d32);
            margin-right: 4px;
        }
        .testimonial-detail-table td:last-child { font-weight: 600; color: #222; text-align: right; }
        .testimonial-author { display: flex; align-items: center; gap: 0.6rem; margin-top: auto; flex-wrap: wrap; }
        .testimonial-author i { font-size: 1.8rem; color: var(--primary-color, #2e7d32); }
        .testimonial-date { font-size: 0.75rem; color: var(--text-light, #888); }
        .testimonial-verified-badge {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 600;
            color: #1b5e20;
            background: #e6f4ea;
            padding: 3px 8px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .testimonial-nav {
            flex-shrink: 0;
            width: 40px; height: 40px;
            border-radius: 50%;
            border: none;
            background: var(--primary-color, #2e7d32);
            color: #fff;
            cursor: pointer;
        }
        .testimonial-nav:hover { opacity: 0.9; }
        @media (max-width: 576px) {
            .testimonial-card { flex-basis: 280px; }
            .testimonial-detail-table { font-size: 0.75rem; }
        }
    </style>
    <script nonce="<?= cspNonce() ?>">
    (function() {
        var track = document.getElementById('testiTrack');
        var prev  = document.getElementById('testiPrev');
        var next  = document.getElementById('testiNext');
        if (!track) return;

        function scrollByCard(direction) {
            var card = track.querySelector('.testimonial-card');
            var amount = card ? (card.offsetWidth + 20) * direction : 300 * direction;
            track.scrollBy({ left: amount, behavior: 'smooth' });
        }

        if (prev) prev.addEventListener('click', function() { scrollByCard(-1); });
        if (next) next.addEventListener('click', function() { scrollByCard(1); });
    })();
    </script>

    <script nonce="<?= cspNonce() ?>">
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

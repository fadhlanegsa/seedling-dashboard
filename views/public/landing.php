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
                        <img src="<?= asset('images/carousel/slide1.svg') ?>" alt="Slide 1">
                        <div class="carousel-caption">
                            <h3>Bibit Berkualitas untuk Indonesia Hijau</h3>
                            <p>Menyediakan berbagai jenis bibit tanaman berkualitas tinggi</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/slide2.svg') ?>" alt="Slide 2">
                        <div class="carousel-caption">
                            <h3>Distribusi ke Seluruh Nusantara</h3>
                            <p>Jangkauan luas untuk mendukung penghijauan nasional</p>
                        </div>
                    </div>
                    <div class="carousel-slide">
                        <img src="<?= asset('images/carousel/slide3.svg') ?>" alt="Slide 3">
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

    <!-- News / Highlight Section -->
    <section class="news-section">
        <div class="container">
            <div class="section-title text-center" style="margin-bottom: 1.5rem;">
                <h2>Kabar Kehutanan</h2>
                <p>Informasi dan berita terbaru seputar penghijauan dari Pusat, BPDAS, dan BPTH</p>
            </div>

            <!-- Tab Filter -->
            <div class="nl-tabs" id="nlTabs">
                <button class="nl-tab active" data-filter="all">
                    <i class="fas fa-list"></i> Semua
                    <span class="nl-tab-count" id="cnt-all"><?= count($latestNews ?? []) ?></span>
                </button>
                <button class="nl-tab" data-filter="pusat">
                    <i class="fas fa-landmark"></i> Pusat
                    <span class="nl-tab-count" id="cnt-pusat">0</span>
                </button>
                <button class="nl-tab" data-filter="bpdas">
                    <i class="fas fa-water"></i> BPDAS
                    <span class="nl-tab-count" id="cnt-bpdas">0</span>
                </button>
                <button class="nl-tab" data-filter="bpth">
                    <i class="fas fa-tree"></i> BPTH
                    <span class="nl-tab-count" id="cnt-bpth">0</span>
                </button>
            </div>

            <?php if (!empty($latestNews)): ?>
                <div class="nl-grid" id="nlGrid">
                    <?php foreach ($latestNews as $news): ?>
                        <?php
                            $src = htmlspecialchars($news['source_type']);
                            $bpdasName = htmlspecialchars($news['bpdas_name'] ?? '');
                        ?>
                        <div class="news-card" data-source="<?= $src ?>">
                            <?php if (!empty($news['image_filename'])): ?>
                                <img src="<?= asset('uploads/news/' . htmlspecialchars($news['image_filename'])) ?>"
                                     alt="<?= htmlspecialchars($news['title']) ?>"
                                     class="news-image"
                                     onerror="this.outerHTML='<div class=\'news-image-placeholder\'><i class=\'fas fa-leaf\'></i></div>'">
                            <?php else: ?>
                                <div class="news-image-placeholder"><i class="fas fa-leaf"></i></div>
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-meta-row">
                                    <?php if ($news['source_type'] === 'pusat'): ?>
                                        <span class="news-source-pill news-source-pusat"><i class="fas fa-landmark"></i> Pusat</span>
                                    <?php elseif ($news['source_type'] === 'bpth'): ?>
                                        <span class="news-source-pill news-source-bpth"><i class="fas fa-tree"></i> <?= $bpdasName ?: 'BPTH' ?></span>
                                    <?php else: ?>
                                        <span class="news-source-pill news-source-bpdas"><i class="fas fa-water"></i> <?= $bpdasName ?: 'BPDAS' ?></span>
                                    <?php endif; ?>
                                    <div class="news-date"><?= formatDate($news['published_at'], 'j M Y') ?></div>
                                </div>
                                <h3 class="news-title"><?= htmlspecialchars($news['title']) ?></h3>
                                <p class="news-excerpt"><?= htmlspecialchars(mb_strimwidth(strip_tags($news['content']), 0, 110, '...')) ?></p>
                                <a href="<?= url('public/kabar-kehutanan') ?>" class="news-link">
                                    Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="nl-empty" id="nlEmpty" style="display:none">
                    <i class="fas fa-newspaper"></i>
                    <p>Belum ada berita dari kategori ini.</p>
                </div>
                <div class="text-center" style="margin-top: 2.5rem;">
                    <a href="<?= url('public/kabar-kehutanan') ?>" class="btn btn-outline btn-lg">
                        <i class="fas fa-newspaper"></i> Lihat Semua Berita
                    </a>
                </div>
            <?php else: ?>
                <div class="news-empty-landing">
                    <i class="fas fa-newspaper"></i>
                    <p>Belum ada berita yang dipublikasikan.</p>
                    <a href="<?= url('public/kabar-kehutanan') ?>" class="btn btn-outline">
                        <i class="fas fa-newspaper"></i> Buka Kabar Kehutanan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <script>
    (function() {
        var tabs   = document.querySelectorAll('#nlTabs .nl-tab');
        var cards  = document.querySelectorAll('#nlGrid .news-card');
        var empty  = document.getElementById('nlEmpty');

        // Count per tab
        var counts = {pusat: 0, bpdas: 0, bpth: 0};
        cards.forEach(function(c) {
            var s = c.dataset.source;
            if (counts[s] !== undefined) counts[s]++;
        });
        document.getElementById('cnt-pusat').textContent = counts.pusat;
        document.getElementById('cnt-bpdas').textContent = counts.bpdas;
        document.getElementById('cnt-bpth').textContent  = counts.bpth;

        function filterCards(filter) {
            var visible = 0;
            cards.forEach(function(c) {
                var show = filter === 'all' || c.dataset.source === filter;
                c.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
        }

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                tabs.forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                filterCards(tab.dataset.filter);
            });
        });
    })();
    </script>

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

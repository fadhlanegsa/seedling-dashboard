// API Base URL - Update this when deploying to production
// Default local fallback assumes running from inside xampp
const API_URL = window.location.hostname.includes('localhost') 
    ? 'http://localhost/seedling-dashboard/seedling-dashboard/public/api-landing-data'
    : 'https://bibitgratis.com/public/api-landing-data'; // Production URL

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize UI Elements
    initNavbar();
    renderApp();
    
    // 2. Fetch Data
    fetchDashboardData();
});

function initNavbar() {
    const navbar = document.getElementById('navbar');
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileLinks = document.querySelectorAll('.mobile-link');

    // Scroll effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            navbar.classList.add('bg-white/90', 'backdrop-blur-md', 'shadow-sm');
            navbar.classList.remove('bg-transparent');
        } else {
            navbar.classList.remove('bg-white/90', 'backdrop-blur-md', 'shadow-sm');
            navbar.classList.add('bg-transparent');
        }
    });

    // Mobile menu toggle
    mobileBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    });
}

function renderApp() {
    const app = document.getElementById('app');
    
    app.innerHTML = `
        <!-- Hero Section -->
        <section id="beranda" class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
            <!-- Decorative Blobs -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-sage-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-mint-200 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    
                    <!-- Hero Text -->
                    <div class="text-left animate-on-scroll">
                        <span class="inline-block py-1 px-3 rounded-full bg-sage-100 text-sage-800 font-semibold text-sm mb-6 border border-sage-200">
                            Pusat Distribusi Bibit Nasional
                        </span>
                        <h1 class="text-5xl lg:text-7xl font-serif font-bold text-sage-900 leading-tight mb-6">
                            Ayo Tanam <br>
                            <span class="text-sage-600">Pohon</span>
                        </h1>
                        <p class="text-lg text-sage-700 mb-8 max-w-lg leading-relaxed">
                            Platform resmi Kementerian Kehutanan untuk akses informasi dan distribusi bibit tanaman hutan gratis bagi masyarakat. Bersama menghijaukan Indonesia.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="https://bibitgratis.com/public/request-form" class="bg-sage-600 text-white px-8 py-4 rounded-full font-medium hover:bg-sage-700 transition-all shadow-xl shadow-sage-600/30 text-center text-lg flex items-center justify-center gap-2 group">
                                <span>Ajukan Permintaan</span>
                                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#cara-kerja" class="bg-white text-sage-700 border-2 border-sage-200 px-8 py-4 rounded-full font-medium hover:bg-sage-50 transition-colors text-center text-lg">
                                Lihat Cara Kerja
                            </a>
                        </div>
                    </div>

                    <!-- Hero Animation (Seedling to Tree) -->
                    <div class="relative h-[500px] flex justify-center items-center animate-on-scroll delay-200">
                        <div class="w-full max-w-md h-full relative glass rounded-3xl p-8 flex justify-center items-end pb-12 shadow-2xl shadow-sage-900/10">
                            <!-- SVG Canvas -->
                            <svg viewBox="0 0 400 400" class="w-full h-full overflow-visible" id="tree-svg">
                                <!-- Pot -->
                                <path d="M 150 350 L 250 350 L 270 280 L 130 280 Z" fill="#8b5a2b" />
                                <path d="M 120 280 L 280 280 L 280 260 L 120 260 Z" fill="#a0522d" />
                                
                                <!-- Soil -->
                                <ellipse cx="200" cy="260" rx="70" ry="10" fill="#3e2723" />

                                <!-- Seedling (State 1) -->
                                <g id="anim-seedling" class="transition-opacity duration-1000">
                                    <path d="M 200 260 Q 200 220 200 200" stroke="#4e8c4e" stroke-width="6" fill="none" stroke-linecap="round" />
                                    <path d="M 200 230 Q 170 230 180 200" fill="#6ea96e" />
                                    <path d="M 200 220 Q 230 220 220 190" fill="#6ea96e" />
                                </g>

                                <!-- Majestic Tree (State 2) -->
                                <g id="anim-tree" class="opacity-0 transition-opacity duration-1000">
                                    <!-- Trunk -->
                                    <path d="M 190 260 L 210 260 L 205 100 L 195 100 Z" fill="#5d4037" />
                                    <!-- Branches -->
                                    <path d="M 200 180 Q 150 150 140 100" stroke="#5d4037" stroke-width="8" fill="none" stroke-linecap="round" />
                                    <path d="M 200 160 Q 250 130 260 90" stroke="#5d4037" stroke-width="8" fill="none" stroke-linecap="round" />
                                    
                                    <!-- Canopy (Animated Scale) -->
                                    <g class="tree-canopy" id="tree-canopy-group">
                                        <circle cx="200" cy="90" r="80" fill="#3d703d" class="leaf-float" style="animation-delay: 0s;" />
                                        <circle cx="150" cy="120" r="60" fill="#4e8c4e" class="leaf-float" style="animation-delay: 0.5s;" />
                                        <circle cx="250" cy="110" r="65" fill="#4a7f4a" class="leaf-float" style="animation-delay: 1s;" />
                                        <circle cx="200" cy="40" r="70" fill="#5a8f5a" class="leaf-float" style="animation-delay: 1.5s;" />
                                        <circle cx="130" cy="80" r="50" fill="#6ea96e" class="leaf-float" style="animation-delay: 2s;" />
                                        <circle cx="270" cy="70" r="55" fill="#335833" class="leaf-float" style="animation-delay: 2.5s;" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="statistik" class="py-16 bg-sage-800 text-sage-50 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center" id="stats-container">
                    <!-- Loading Stats -->
                    <div class="col-span-4 text-sage-300">Memuat data statistik...</div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="layanan" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 animate-on-scroll">
                    <h2 class="text-4xl font-serif font-bold text-sage-900 mb-4">Layanan Kami</h2>
                    <p class="text-sage-600 max-w-2xl mx-auto text-lg">Platform terpadu untuk mendukung program penghijauan nasional.</p>
                </div>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Service 1 -->
                    <div class="bg-sage-50 rounded-2xl p-8 border border-sage-100 hover:shadow-xl hover:shadow-sage-200/50 transition-all duration-300 group animate-on-scroll">
                        <div class="w-14 h-14 bg-sage-200 rounded-xl flex items-center justify-center text-sage-700 text-2xl mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h3 class="text-xl font-bold text-sage-900 mb-3">Distribusi Bibit</h3>
                        <p class="text-sage-600 leading-relaxed">Dapatkan bibit berkualitas unggul untuk penanaman di lahan kritis maupun lahan produktif.</p>
                    </div>
                    <!-- Service 2 -->
                    <div class="bg-sage-50 rounded-2xl p-8 border border-sage-100 hover:shadow-xl hover:shadow-sage-200/50 transition-all duration-300 group animate-on-scroll delay-100">
                        <div class="w-14 h-14 bg-sage-200 rounded-xl flex items-center justify-center text-sage-700 text-2xl mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3 class="text-xl font-bold text-sage-900 mb-3">Cek Stok BPDAS</h3>
                        <p class="text-sage-600 leading-relaxed">Pantau ketersediaan stok bibit secara realtime di seluruh persemaian BPDAS di Indonesia.</p>
                    </div>
                    <!-- Service 3 -->
                    <div class="bg-sage-50 rounded-2xl p-8 border border-sage-100 hover:shadow-xl hover:shadow-sage-200/50 transition-all duration-300 group animate-on-scroll delay-200">
                        <div class="w-14 h-14 bg-sage-200 rounded-xl flex items-center justify-center text-sage-700 text-2xl mb-6 group-hover:scale-110 transition-transform">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="text-xl font-bold text-sage-900 mb-3">Direktori Sumber Benih</h3>
                        <p class="text-sage-600 leading-relaxed">Akses informasi lengkap sumber benih bersertifikat di seluruh wilayah Nusantara.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section id="berita" class="py-24 bg-sage-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-12 animate-on-scroll">
                    <div>
                        <h2 class="text-4xl font-serif font-bold text-sage-900 mb-4">Kabar Kehutanan</h2>
                        <p class="text-sage-600 text-lg">Informasi dan berita terbaru seputar penghijauan.</p>
                    </div>
                    <a href="https://bibitgratis.com/public/kabar-kehutanan" class="hidden md:inline-flex items-center gap-2 text-sage-700 font-medium hover:text-sage-900 transition-colors">
                        Lihat Semua Berita <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="grid md:grid-cols-3 gap-8" id="news-container">
                    <!-- Loading News -->
                    <div class="col-span-3 text-center text-sage-500 py-12">
                        <i class="fas fa-spinner fa-spin text-3xl mb-4"></i>
                        <p>Memuat berita terbaru...</p>
                    </div>
                </div>
                
                <div class="mt-8 text-center md:hidden">
                    <a href="https://bibitgratis.com/public/kabar-kehutanan" class="inline-flex items-center gap-2 text-sage-700 font-medium hover:text-sage-900 transition-colors">
                        Lihat Semua Berita <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-sage-950 text-sage-200 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <i class="fas fa-leaf text-sage-400 text-2xl"></i>
                        <span class="font-serif font-bold text-2xl text-white">Bibit Gratis</span>
                    </div>
                    <p class="text-sage-400 mb-6 max-w-md">
                        Platform resmi distribusi bibit tanaman hutan untuk mendukung program penghijauan dan rehabilitasi hutan dan lahan di Indonesia.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-sage-800 flex items-center justify-center hover:bg-sage-600 transition-colors"><i class="fab fa-facebook-f text-white"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-sage-800 flex items-center justify-center hover:bg-sage-600 transition-colors"><i class="fab fa-twitter text-white"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-sage-800 flex items-center justify-center hover:bg-sage-600 transition-colors"><i class="fab fa-instagram text-white"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Tautan Cepat</h4>
                    <ul class="space-y-4">
                        <li><a href="#beranda" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="https://bibitgratis.com/public/stock-search" class="hover:text-white transition-colors">Cari Stok</a></li>
                        <li><a href="https://bibitgratis.com/public/seed-source-directory" class="hover:text-white transition-colors">Sumber Benih</a></li>
                        <li><a href="https://bibitgratis.com/public/kabar-kehutanan" class="hover:text-white transition-colors">Berita</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-6">Kontak</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-sage-500"></i>
                            <span>Gedung Manggala Wanabakti, Jakarta, Indonesia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-sage-500"></i>
                            <span>info@bibitgratis.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-sage-500"></i>
                            <span>(021) 123-4567</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pt-8 border-t border-sage-800 text-center text-sage-500 text-sm">
                &copy; ${new Date().getFullYear()} Kementerian Lingkungan Hidup dan Kehutanan. Hak Cipta Dilindungi.
            </div>
        </footer>
    `;

    // Start Hero Animation Loop
    startHeroAnimation();
    
    // Initialize Scroll Animations
    initScrollAnimations();
}

function startHeroAnimation() {
    const seedling = document.getElementById('anim-seedling');
    const tree = document.getElementById('anim-tree');
    const canopy = document.getElementById('tree-canopy-group');

    // Sequence:
    // 0s: Seedling visible
    // 2s: Seedling fades out, Tree Trunk appears
    // 3s: Canopy grows
    // 8s: Reset

    setInterval(() => {
        // Step 1: Hide seedling, show trunk
        setTimeout(() => {
            seedling.classList.add('opacity-0');
            tree.classList.remove('opacity-0');
        }, 2000);

        // Step 2: Grow Canopy
        setTimeout(() => {
            canopy.classList.add('show');
        }, 3000);

        // Step 3: Reset
        setTimeout(() => {
            canopy.classList.remove('show');
            tree.classList.add('opacity-0');
            seedling.classList.remove('opacity-0');
        }, 8000);

    }, 9000); // Repeat every 9s
}

function initScrollAnimations() {
    const elements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => observer.observe(el));
}

async function fetchDashboardData() {
    try {
        const response = await fetch(API_URL);
        if (!response.ok) throw new Error('Network response was not ok');
        const result = await response.json();
        
        if (result.success && result.data) {
            renderStats(result.data.stats);
            renderNews(result.data.news);
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        document.getElementById('stats-container').innerHTML = `<div class="col-span-4 text-red-400">Gagal memuat data statistik. Pastikan backend API berjalan.</div>`;
        document.getElementById('news-container').innerHTML = `<div class="col-span-3 text-red-400 text-center">Gagal memuat berita.</div>`;
    }
}

function renderStats(stats) {
    const container = document.getElementById('stats-container');
    
    const statItems = [
        { label: 'Total Stok Bibit', value: stats.total_stock, icon: 'fa-seedling' },
        { label: 'Bibit Terdistribusi', value: stats.total_distributed, icon: 'fa-truck-loading' },
        { label: 'Permintaan Masuk', value: stats.total_requests, icon: 'fa-file-contract' },
        { label: 'Persemaian Aktif', value: stats.total_nurseries, icon: 'fa-warehouse' }
    ];

    container.innerHTML = statItems.map((item, index) => `
        <div class="p-6 rounded-2xl bg-sage-700/50 border border-sage-600 backdrop-blur-sm animate-on-scroll delay-${index * 100}">
            <i class="fas ${item.icon} text-3xl text-sage-300 mb-4 opacity-80"></i>
            <div class="text-4xl font-bold text-white mb-2">${formatNumber(item.value)}</div>
            <div class="text-sage-300 font-medium">${item.label}</div>
        </div>
    `).join('');
    
    // Re-init observer for new elements
    initScrollAnimations();
}

function renderNews(news) {
    const container = document.getElementById('news-container');
    
    if (!news || news.length === 0) {
        container.innerHTML = `<div class="col-span-3 text-center text-sage-500 py-8">Belum ada berita yang dipublikasikan.</div>`;
        return;
    }

    // Take top 3
    const topNews = news.slice(0, 3);
    
    container.innerHTML = topNews.map((item, index) => {
        // Handle images
        let imgUrl = 'https://via.placeholder.com/600x400/c5e0c5/3d703d?text=Kehutanan';
        if (item.image_filename) {
            // Adjust based on your backend upload path
            imgUrl = `http://localhost/seedling-dashboard/seedling-dashboard/public/uploads/news/${item.image_filename}`;
        }
        
        // Truncate content
        let excerpt = item.content ? item.content.replace(/<[^>]*>?/gm, '') : '';
        if (excerpt.length > 100) excerpt = excerpt.substring(0, 100) + '...';

        // Date format
        const dateObj = new Date(item.published_at);
        const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

        return `
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg shadow-sage-200/40 border border-sage-100 hover:shadow-xl transition-all duration-300 group animate-on-scroll delay-${index * 100} flex flex-col">
                <div class="h-52 overflow-hidden relative">
                    <img src="${imgUrl}" alt="${item.title}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://via.placeholder.com/600x400/c5e0c5/3d703d?text=Image+Not+Found'">
                    <div class="absolute top-4 left-4 bg-sage-600 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        ${item.source_type}
                    </div>
                </div>
                <div class="p-6 flex-grow flex flex-col">
                    <div class="text-sm text-sage-500 mb-3 flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i> ${dateStr}
                    </div>
                    <h3 class="text-xl font-bold text-sage-900 mb-3 leading-snug group-hover:text-sage-600 transition-colors">
                        ${item.title}
                    </h3>
                    <p class="text-sage-600 mb-6 flex-grow">
                        ${excerpt}
                    </p>
                    <a href="https://bibitgratis.com/public/kabar-kehutanan" class="inline-flex items-center gap-2 text-sage-700 font-bold hover:text-sage-900 transition-colors mt-auto">
                        Baca Selengkapnya <i class="fas fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        `;
    }).join('');
    
    // Re-init observer
    initScrollAnimations();
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

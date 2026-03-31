<?php
/**
 * Kabar Kehutanan - Public View
 * Displays news from Pusat (default), BPDAS, and BPTH with tab toggle
 */
?>

<!-- Hero Section -->
<section class="kk-hero">
    <div class="container">
        <div class="kk-hero-content">
            <div class="kk-hero-icon"><i class="fas fa-newspaper"></i></div>
            <h1 class="kk-hero-title">Kabar Kehutanan</h1>
            <p class="kk-hero-subtitle">Informasi terkini penghijauan dan persemaian dari Pusat, BPDAS, dan BPTH di seluruh Indonesia</p>
        </div>
    </div>
</section>

<!-- Source Toggle -->
<section class="kk-toggle-section">
    <div class="container">
        <div class="kk-toggle-wrapper">
            <a href="<?= url('public/kabar-kehutanan?source=pusat') ?>"
               class="kk-toggle-btn <?= $activeSource === 'pusat' ? 'active' : '' ?>">
                <i class="fas fa-landmark"></i> <span>Pusat</span>
                <span class="kk-count-badge"><?= $countPusat ?></span>
            </a>
            <a href="<?= url('public/kabar-kehutanan?source=bpdas') ?>"
               class="kk-toggle-btn <?= $activeSource === 'bpdas' ? 'active' : '' ?>">
                <i class="fas fa-water"></i> <span>BPDAS</span>
                <span class="kk-count-badge"><?= $countBPDAS ?></span>
            </a>
            <a href="<?= url('public/kabar-kehutanan?source=bpth') ?>"
               class="kk-toggle-btn <?= $activeSource === 'bpth' ? 'active' : '' ?>">
                <i class="fas fa-tree"></i> <span>BPTH</span>
                <span class="kk-count-badge"><?= $countBPTH ?></span>
            </a>
        </div>
        <p class="kk-toggle-label">
            Menampilkan berita dari:
            <strong>
                <?php if ($activeSource === 'pusat'): ?>Direktorat Pusat
                <?php elseif ($activeSource === 'bpth'): ?>BPTH (Balai Perbenihan Tanaman Hutan)
                <?php else: ?>BPDAS (Balai Pengelolaan DAS)
                <?php endif; ?>
            </strong>
        </p>
    </div>
</section>

<!-- News Grid -->
<section class="kk-grid-section">
    <div class="container">
        <?php if (empty($newsList)): ?>
            <div class="kk-empty-state">
                <div class="kk-empty-icon"><i class="fas fa-newspaper"></i></div>
                <h3>Belum Ada Berita</h3>
                <p>Belum ada berita dari kategori ini saat ini.<br>Silakan kembali lagi nanti.</p>
            </div>
        <?php else: ?>
            <div class="kk-grid">
                <?php foreach ($newsList as $news): ?>
                    <article class="kk-card">
                        <?php if ($news['image_filename']): ?>
                            <div class="kk-card-img">
                                <img src="<?= asset('uploads/news/' . htmlspecialchars($news['image_filename'])) ?>"
                                     alt="<?= htmlspecialchars($news['title']) ?>"
                                     onerror="this.parentElement.innerHTML='<i class=\'fas fa-leaf\'></i>'">
                            </div>
                        <?php else: ?>
                            <div class="kk-card-img kk-card-img--ph"><i class="fas fa-leaf"></i></div>
                        <?php endif; ?>
                        <div class="kk-card-body">
                            <div class="kk-meta">
                                <?php if ($news['source_type'] === 'pusat'): ?>
                                    <span class="kk-tag kk-tag-pusat"><i class="fas fa-landmark"></i> Direktorat Pusat</span>
                                <?php elseif ($news['source_type'] === 'bpth'): ?>
                                    <span class="kk-tag kk-tag-bpth"><i class="fas fa-tree"></i> <?= htmlspecialchars($news['bpdas_name'] ?? 'BPTH') ?></span>
                                <?php else: ?>
                                    <span class="kk-tag kk-tag-bpdas"><i class="fas fa-water"></i> <?= htmlspecialchars($news['bpdas_name'] ?? 'BPDAS') ?></span>
                                <?php endif; ?>
                                <span class="kk-date"><i class="fas fa-calendar-alt"></i> <?= formatDate($news['published_at'], 'd M Y') ?></span>
                            </div>
                            <h2 class="kk-card-title"><?= htmlspecialchars($news['title']) ?></h2>
                            <p class="kk-card-text"><?= htmlspecialchars(mb_strimwidth(strip_tags($news['content']), 0, 180, '...')) ?></p>
                            <div class="kk-card-foot">
                                <span class="kk-author"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($news['author_name']) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* ===== KABAR KEHUTANAN – PUBLIC (kk- prefix) ===== */
.kk-hero {
    writing-mode: horizontal-tb !important;
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 55%, #388e3c 100%);
    padding: 4rem 0 3rem; color: #fff; text-align: center; position: relative; overflow: hidden;
}
.kk-hero::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.06) 0%, transparent 60%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.04) 0%, transparent 40%);
}
.kk-hero-content {
    position: relative; z-index: 1;
    display: flex; flex-direction: column; align-items: center;
    writing-mode: horizontal-tb !important;
}
.kk-hero-icon {
    width: 80px; height: 80px; background: rgba(255,255,255,0.15); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem; font-size: 2rem; border: 2px solid rgba(255,255,255,0.3);
}
.kk-hero-title {
    font-size: 2.5rem !important; font-weight: 700 !important; margin: 0 0 0.75rem !important;
    color: #fff !important; writing-mode: horizontal-tb !important; text-orientation: mixed !important;
    letter-spacing: 0 !important;
}
.kk-hero-subtitle {
    font-size: 1.05rem; opacity: 0.9; max-width: 600px; margin: 0 auto; color: #fff;
    writing-mode: horizontal-tb !important;
}

/* ---- Toggle ---- */
.kk-toggle-section { padding: 2rem 0 0.5rem; }
.kk-toggle-wrapper {
    display: flex; justify-content: center; gap: 0.5rem;
    background: #f5f5f5; padding: 0.4rem; border-radius: 50px;
    width: -moz-fit-content; width: fit-content; margin: 0 auto 0.75rem;
    box-shadow: inset 0 2px 6px rgba(0,0,0,0.08);
}
.kk-toggle-btn {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.55rem 1.4rem; border-radius: 50px; text-decoration: none;
    color: #555; font-weight: 600; font-size: 0.9rem; transition: all 0.2s;
}
.kk-toggle-btn:hover { color: #2e7d32; text-decoration: none; }
.kk-toggle-btn.active { background: #2e7d32; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.kk-count-badge {
    background: rgba(255,255,255,0.2); color: inherit;
    border-radius: 20px; font-size: 0.72rem; font-weight: 700; padding: 0.1rem 0.5rem;
    min-width: 22px; text-align: center;
}
.kk-toggle-btn:not(.active) .kk-count-badge { background: #e0e0e0; color: #666; }
.kk-toggle-label { text-align: center; color: #666; font-size: 0.88rem; }

/* ---- Grid ---- */
.kk-grid-section { padding: 1.5rem 0 4rem; }
.kk-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.75rem; }
.kk-card {
    background: #fff; border-radius: 12px; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex; flex-direction: column; writing-mode: horizontal-tb !important;
}
.kk-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.13); }
.kk-card-img {
    height: 200px; overflow: hidden;
    background: #e8f5e9; display: flex; align-items: center; justify-content: center;
}
.kk-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.kk-card:hover .kk-card-img img { transform: scale(1.05); }
.kk-card-img--ph { font-size: 3rem; color: #a5d6a7; }
.kk-card-body {
    padding: 1.25rem; flex: 1; display: flex; flex-direction: column; gap: 0.6rem;
    writing-mode: horizontal-tb !important;
}
.kk-meta { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.kk-tag {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.72rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.kk-tag-pusat { background: #e3f2fd; color: #1565c0; }
.kk-tag-bpdas  { background: #e8f5e9; color: #1b5e20; }
.kk-tag-bpth   { background: #f1f8e9; color: #33691e; }
.kk-date { font-size: 0.78rem; color: #888; display: flex; align-items: center; gap: 0.3rem; }
.kk-card-title {
    font-size: 1.05rem !important; font-weight: 700 !important; color: #1a1a2e !important;
    margin: 0 !important; line-height: 1.4 !important; writing-mode: horizontal-tb !important;
}
.kk-card-text { font-size: 0.875rem; color: #666; line-height: 1.6; flex: 1; margin: 0; }
.kk-card-foot { border-top: 1px solid #f0f0f0; padding-top: 0.6rem; }
.kk-author { font-size: 0.8rem; color: #888; display: flex; align-items: center; gap: 0.3rem; }

/* ---- Empty state ---- */
.kk-empty-state {
    text-align: center; padding: 5rem 2rem;
    background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.kk-empty-icon { font-size: 4rem; color: #c8e6c9; margin-bottom: 1.5rem; }
.kk-empty-state h3 { color: #333; margin-bottom: 0.5rem; }
.kk-empty-state p  { color: #888; }

@media (max-width: 768px) {
    .kk-hero-title { font-size: 1.75rem !important; }
    .kk-grid { grid-template-columns: 1fr; }
    .kk-toggle-btn { padding: 0.45rem 1rem; font-size: 0.825rem; }
    .kk-toggle-wrapper { flex-wrap: wrap; border-radius: 12px; }
}
</style>

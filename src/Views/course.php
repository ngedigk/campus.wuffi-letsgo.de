<?php
/** @var array $viewModel */
?>

<header id="content-header">
    <section id="hero" aria-hidden="true">
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-06.webp'); --hero-focus: 84% 44%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-03.webp'); --hero-focus: 62% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/AHT-2_edit.webp'); --hero-focus: 50% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-04.webp'); --hero-focus: 28% 46%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/esa-hero-01.webp'); --hero-focus: 50% 42%;"></div>
        <div class="hero-image" style="--hero-image: url('/assets/images/hero/aht-hero-01.webp'); --hero-focus: 48% 48%;"></div>
    </section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <p>Campus für fachtheoretische Wissensvermittlung</p>
                <h1>Entdecken. Verstehen. Wissen testen.</h1>

                <ul id="breadcrumb">
                    <?php foreach ($viewModel['breadcrumb'] ?? [] as $index => $breadcrumb): ?>
                        <?php if ($breadcrumb['url'] ?? false): ?>
                        <li>
                            <a href="<?= $breadcrumb['url'] ?>">
                                <?= $breadcrumb['title'] ?>
                            </a>
                        </li>
                        <li aria-hidden="true">></li>
                        <?php else: ?>
                        <li><?= $breadcrumb['title'] ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<?php require __DIR__ . '/course/content.php'; ?>

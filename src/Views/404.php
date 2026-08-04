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
                <p>Seite nicht gefunden</p>
                <h1>404</h1>

                <ul id="breadcrumb">
                    <li>Startseite</li>
                    <li aria-hidden="true">></li>
                    <li>Campus</li>
                </ul>
            </div>
        </div>
    </div>
</header>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>404</h2>
                <p><?= htmlspecialchars($viewModel['message']) ?></p>
            </div>
        </div>
    </div>
</section>
<?php /** @var array $viewModel */ ?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <?php require_once __DIR__ . '/partials/general-messages.php'; ?>

                <div class="general-card">

                    <h2><?= $viewModel['headline'] ?></h2>
                    <div>
                        <a href="index.php">Zur Anmeldung</a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
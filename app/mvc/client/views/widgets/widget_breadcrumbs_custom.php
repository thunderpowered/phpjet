<?php if ($path): ?>

    <div class="breadcrumbs">
        <ul class="breadcrumbs__list o-flex-container">

            <?php foreach ($path as $item): ?>
                <li class="breadcrumbs__item">
                    <a href="<?= $item["url"] ?>" class="breadcrumbs__link">
                        <?= $item["title"]; ?>
                    </a>
                </li>
            <?php endforeach; ?>

        </ul>
    </div>

<?php endif; ?>
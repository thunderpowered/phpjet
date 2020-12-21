<div class="menu-tiles">
    <ul class="menu-tiles__list o-flex-container">
        <?php
        /**
         * @var array $menu
         */
        foreach ($menu as $key => $value): ?>
            <li class="menu-tiles__item">
                <a href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/" . $value["url"] ?>" class="menu-tiles__link o-block p-text-align--center">
                    <div class="menu-tiles__image-wrapper">
                        <?= \CloudStore\CloudStore::$app->tool->utils->getImage($value["image"], "menu-tiles__image o-block-center", $value["title"]) ?>
                    </div>
                    <span class="menu-tiles__text p-text-align--center">
                        <?= $value["title"] ?>
                    </span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
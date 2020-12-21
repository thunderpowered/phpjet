<?php
/**
 * @var array $path
 */
if ($path): ?>

<div class="side__navigation menu-tree">

    <?php if ($path["parent"]): ?>
        <div class="navigation__back-wrapper">
            <span class="navigation__back">
                <a class="navigation__back-link" href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$path["parent"]["url"]}" ?>"><?= $path["parent"]["title"] ?></a>
            </span>
        </div>
    <?php endif; ?>

    <?php if ($path["current"]): ?>
    <div class="navigation__current-wrapper">
        <span class="navigation__current"><a class="navigation_current_link" href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$path["current"]["url"]}"?>"><?= $path["current"]["title"] ?></a></span>
    </div>
    <?php endif; ?>

    <?php if ($path["children"]): ?>
    <ul class="navigation__list menu-tiles__list">
        <?php foreach ($path["children"] as $child): ?>

        <li class="navigation__item menu-tree__item">
            <?php if ($child["current"]): ?>
                <a href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$child["url"]}" ?>" class="navigation__link menu-tree__link"><b><?= $child["title"] ?></b></a>
            <?php else: ?>
                <a href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$child["url"]}" ?>" class="navigation__link menu-tree__link"><?= $child["title"] ?></a>
            <?php endif; ?>
            <?php if ($child["children"]): ?>
                <a data-toggle='collapse' class="menu-tree__toggle" href="#list-<?= $child["id"]; ?>">+</a>
                <ul id="list-<?= $child["id"]; ?>" class="menu-tree__list list-group list-group-root <?= ($child["has_current"] ? "" : "collapse") ?> menu-tree__list--lower">
            <?php foreach ($child["children"] as $_child): ?>
                <?php if ($_child["current"]): ?>
                    <li><a href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$_child["url"]}" ?>" class="navigation__link menu-tree__link menu-tree__link--lower"><b><?= $_child["title"] ?></b></a></li>
                <?php else: ?>
                    <li><a href="<?= \CloudStore\CloudStore::$app->router->getHost() . "/catalog/{$_child["url"]}" ?>" class="navigation__link menu-tree__link menu-tree__link--lower"><?= $_child["title"] ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>

        <?php endforeach; ?>

    </ul>
    <?php endif;?>

</div>

<?php endif;?>

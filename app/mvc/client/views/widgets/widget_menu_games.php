<?php
/**
 * @var \CloudStore\App\Engine\ActiveRecord\Tables\Games[] $games
 */
?>
<ul class="d-flex pb-3 flex-column p-0 js-plugin_niceScroll theme__border-color theme__border-bottom">
    <?php if ($games): ?>
    <?php foreach ($games as $game): ?>
            <li class="header__search-list__item d-flex flex-row p-4 pb-0">
                <div class="header__search-list__item-icon"></div>
                <a href="<?php echo $game->url?>" class="theme__link-color theme__link-color--hover fw-regular fs-10 d-block"><?php echo $game->name?></a>
            </li>
<!--            <li class="d-block p-0 pt-2">-->
<!--                <a class="theme__link-color theme__link-color--hover fw-regular fs-9" href="--><?php //echo $game->url?><!--">--><?php //echo $game->name?><!--</a>-->
<!--            </li>-->
    <?php endforeach;?>
    <?php else: ?>
        <span class="ext-left fs-8">Ничего нет :(</span>
    <?php endif; ?>
</ul>
<div class="p3-3">
    <span class="text-left">Чего-то не хватает?</span>
    <a class="d-block theme__link-color theme__link-color--hover theme__link-color--accent fw-bold" href="">Напишите нам!</a>
</div>

<?php
/**
 * @var \Jet\App\Engine\ActiveRecord\Tables\Soft[] $games
 */
?>
<ul class="d-flex pb-3 m-0 flex-column p-0 js-plugin_niceScroll theme__border-color--accent theme__border-bottom theme__border--thicker theme__background-color2">
    <?php if ($games): ?>
    <?php foreach ($games as $game): ?>
            <li class="header__search-list__item d-flex flex-row p-3 pb-0">
                <div style="background-image: url('<?php echo $game->icon ?>')" class="header__search-list__item-icon"></div>
                <a href="<?php echo $game->url?>" class="theme__link-color theme__link-color--hover fw-regular fs-10 d-block"><?php echo $game->name?></a>
            </li>
    <?php endforeach;?>
    <?php else: ?>
        <span class="ext-left fs-8">Ничего нет :(</span>
    <?php endif; ?>
</ul>
<div class="p-3">
    <span class="text-left">Чего-то не хватает?</span>
    <a class="d-block theme__link-color theme__link-color--hover theme__link-color--accent fw-bold" href="">Напишите нам!</a>
</div>

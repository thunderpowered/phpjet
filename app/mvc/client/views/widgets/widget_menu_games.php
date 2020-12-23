<?php
/**
 * @var \CloudStore\App\Engine\ActiveRecord\Tables\Games[] $games
 */
?>
<ul class="d-flex flex-column p-0 js-plugin_niceScroll">
    <?php if ($games): ?>
    <?php foreach ($games as $game): ?>
            <li class="d-block p-0 pt-2">
                <a class="theme__link-color theme__link-color--hover fw-regular fs-9" href="<?php echo $game->url?>"><?php echo $game->name?></a>
            </li>
    <?php endforeach;?>
    <?php else: ?>
        <span class="ext-left fs-8">Ничего нет :(</span>
    <?php endif; ?>
</ul>

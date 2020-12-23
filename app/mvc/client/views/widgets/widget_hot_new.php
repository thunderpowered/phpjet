<?php
/**
 * @var \CloudStore\App\Engine\ActiveRecord\Tables\Mods[] $mods
 */
?>
<ul class="d-flex flex-column p-0">
    <?php if ($mods): ?>
        <?php foreach ($mods as $mod): ?>
            <li class="d-block p-0 pt-4">
                <a class="theme__link-color theme__link-color--hover" href="<?= $mod->url?>">
                    <?php echo ($mod->new ? '<b class="theme__link-color--accent">[NEW!]</b>' : ''); ?> [<?php echo $mod->games_name; ?>] <?php echo $mod->name; ?>
                </a> <b><a class="theme__link-color theme__link-color--hover" href="<?php echo $mod->user_url ?>">by <?php echo $mod->username; ?></a></b>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <span class="ext-left fs-8">Ничего нет :(</span>
    <?php endif; ?>
</ul>

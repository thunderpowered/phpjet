<?php
/**
 * @var array $menu
 */
?>
<nav class="nav d-flex justify-content-start header__menu-nav">
    <?php foreach ($menu as $key => $item): ?>
        <a href="<?php echo $item['url']; ?>" class="p-4 theme__link-color theme__link-color--hover <?php echo $item['class']; ?>"><?php echo $item['title']; ?></a>
    <?php endforeach; ?>
</nav>

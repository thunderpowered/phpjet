<li class="js-menu--catalog"></li>

<?php for ($i = 0; $i < (count($menu) < 15 ? count($menu) : 15); $i++): ?>

    <li class="main__main_menu-item">
        <a class="js-mouseover_menu" data-menu-id="<?= $i ?>">

            <div style="<?php if ($menu[$i]['menu_logo']): ?>
                    background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($menu[$i]['menu_logo']); ?>)
            <?php endif; ?>" class="menu_logo"></div>

            <span class="menu_title-span"><?php echo $menu[$i]['menu_name']; ?></span>
        </a>
        <div data-menu-block="<?= $i ?>" class="main__main_menu-sub">

            <?php if (isset($menu[$i]['columns']) AND $menu[$i]['columns']): ?>

                <?php foreach ($menu[$i]['columns'] as $column): ?>

                    <div class="main__main_menu-sub-item">
                        <ul class="main__main-menu-sub-item-list">

                            <?php if (isset($column['items']) AND $column['items']): ?>

                                <?php foreach ($column['items'] as $item): ?>

                                    <!-- WOW -->

                                    <li class="<?= $item['item_type'] == 11 ? 'main__main_menu-sub-item-list-title' : 'main__main_menu-sub-item-list-item' ?>"><?= $item['item_type'] == 10 ? '<a href="' . \CloudStore\App\Engine\Core\Router::getHost() . '/catalog/' . $item['category_handle'] . '">' : '' ?><?= $item['item_type'] == 01 ? '<a class="" style="cursor:pointer" href="' . $item['item_link'] . '">' : '' ?><?= ($item['item_type'] == 11 OR $item['item_type'] == 1 OR ($item['item_type'] == 10 AND $item['item_title'])) ? '<span class="link_span">' . $item['item_title'] . '</span>' : '<span class="link_span">' . $item['name'] . '</span>' ?><?= $item['item_type'] == 10 ? '<span class="num_indicator">' . $item['counter'] . '</span></a>' : '' ?><?= $item['item_type'] == 01 ? '</a>' : '' ?>
                                        <span class="menu__description"><?= $item['item_description'] ?></span>
                                    </li>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </ul>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

            <?php if (!empty($menu[$i]['menu_cover'])): ?>

                <div class="main__main_menu-sub-item menu_cover_block">
                    <div style="" class="menu_cover">

                        <img src="<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($menu[$i]['menu_cover']); ?>"
                             alt="menu_cover"/>

                        <?php if ($menu[$i]['menu_cover_link']): ?>

                            <a href="<?php echo $menu[$i]['menu_cover_link']; ?>" class="menu_link"></a>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endif; ?>
        </div>
    </li>
<?php endfor; ?>  
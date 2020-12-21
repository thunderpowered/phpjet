<li class="js-menu--catalog"></li>

<?php for ($i = 0; $i < (count($menu) < 15 ? count($menu) : 15); $i++): ?>

    <li class="js-toggle-menu main__main_menu-item correct-menu-item">

        <a class="" data-menu-id="<?php echo $i; ?>">

            <div style="<?php if ($menu[$i]['menu_logo']): ?>
                    background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($menu[$i]['menu_logo']); ?>)
            <?php endif; ?>" class="menu_logo"></div>

            <span class="menu_title-span"><?php echo $menu[$i]['menu_name']; ?></span>

        </a>
        <div data-menu-count="<?php echo isset($menu[$i]['columns']) ? count($menu[$i]['columns']) : ''; ?>"
             data-menu-block="<?php echo $i; ?>" class="main__main_menu-sub-i correct-menu-small">

            <?php if (isset($menu[$i]['columns']) AND $menu[$i]['columns']): ?>

                <?php foreach ($menu[$i]['columns'] as $column): ?>

                    <div class="main__main_menu-sub-item">
                        <ul class="main__main-menu-sub-item-list correct-menu-list">

                            <?php if (isset($column['items']) AND $column['items']): ?>

                                <?php foreach ($column['items'] as $item): ?>

                                    <!-- COMPLICATED ANS SCARY -->

                                    <li class="correct-menu-li <?= $item['item_type'] == 11 ? 'main__main_menu-sub-item-list-title' : 'main__main_menu-sub-item-list-item' ?>"><?= $item['item_type'] == 10 ? '<a href="' . \CloudStore\App\Engine\Core\Router::getHost() . '/catalog/' . $item['category_handle'] . '">' : '' ?><?= $item['item_type'] == 01 ? '<a style="cursor:pointer" href="' . $item['item_link'] . '">' : '' ?><?= ($item['item_type'] == 11 OR $item['item_type'] == 1) ? $item['item_title'] : $item['name'] ?><?= $item['item_type'] == 10 ? '</a><span class="num_indicator">' . $item['counter'] . '</span>' : '' ?><?= $item['item_type'] == 01 ? '</a>' : '' ?>
                                        <span class="menu__description"><?= $item['item_description'] ?></span>
                                    </li>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </ul>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>

    </li>

<?php endfor; ?>  
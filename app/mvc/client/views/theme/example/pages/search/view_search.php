<header class="grid medium-up--grid--table section-header small--text-center">
    <div class="grid__item medium-up--two-thirds section-header__item">
        <h1 class="section-header__title">Результаты поиска</h1>

        <!--<p class="section-header__subtext"><?= !empty($search_count) ? $search_count : "0"; ?> товаров соответствуют запросу
            <strong><?= \CloudStore\App\Engine\Components\Request::getSession('query') ?></strong></p>-->

        <?php if(!$search_products): ?>

            <p class="section-header__subtext">По данному запросу товары не найдены</p>

        <?php else: ?>

            <p class="section-header__subtext">Найдены товары по запросу <strong><?= \CloudStore\App\Engine\Components\Request::getSession('query') ?></strong></p>

        <?php endif; ?>

    </div>
    <!-- <div class="grid__item medium-up--one-third section-header__item">
        <form action="/search/" method="get" class="input-group" role="search" style="position: relative;">

            <input type="search" name="q" value="<?= \CloudStore\App\Engine\Components\Request::getSession('query') ?>"
                   placeholder="Поиск"
                   aria-label="Поиск" class="input-group__field input--content-color" autocomplete="off"
                   data-old-term="Щетка ополаскиватель">

            <div class="input-group__btn">
                <button type="submit" class="btn btn--narrow">
                    <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32"
                         class="icon icon-arrow-right">
                        <path fill="#444"
                              d="M7.667 3.795L9.464 2.11 24.334 16 9.464 29.89l-1.797-1.676L20.73 16z"></path>
                    </svg>
                    <span class="icon__fallback-text">Поиск</span>
                </button>
            </div>
        </form>
    </div> -->
</header>

<?= $this->widget->widgetSorting->getWidget() ?>

<?php if ($search_products): ?>
    <div class="search_list grid grid--no-gutters grid--uniform">

        <?php $this->products = $search_products; ?>

        <h2>Товары</h2>

        <?php $this->includePart("product-list-5"); ?>

    </div>
    <div class="pagination">
        <?php echo $this->controller->getPagination(); ?>
    </div>
<?php endif; ?>

<?php if ($categories): ?>

    <div class="search_list" style="margin:25px 0;">
        <h2>Категории</h2>

        <ul style="display: block;margin-top:20px">

            <?php foreach ($categories as $category): ?>

                <li style="display:block;margin:15px 0;"><a style="font-size:18px;"
                                                            href="<?php echo CloudStore\App\Engine\Core\Router::getHost(); ?>/catalog/<?php echo $category["category_handle"]; ?>"><?php echo $category["name"]; ?></a>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>

<?php if ($pages): ?>

    <div class="search_list" style="margin:25px 0;">
        <h2>Страницы</h2>

        <ul style="display: block;margin-top:20px">

            <?php foreach ($pages as $page): ?>

                <li style="display:block;margin:10px 0;"><a style="font-size:18px;"
                                                            href="<?php echo CloudStore\App\Engine\Core\Router::getHost(); ?>/pages/<?php echo $page["pages_handle"]; ?>"><?php echo $page["pages_title"]; ?></a>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>


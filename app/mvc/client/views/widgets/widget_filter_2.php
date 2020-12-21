<div class="preloaded catalog--filter-column">
    <form id="filter_form" action="/filter/" method="get">
        <div class="filter_general">
            <div class="filter_flex">
                <div class="filter_item_block">
                    <label>Ключевые слова</label>
                    <input name="filter_keys" type="text" placeholder="Слова через запятую" value="<?= $keys ?>"/>
                </div>
            </div>

            <div class="filter_flex">
                <div class="filter_item_block">
                    <label data-for="filter_custom_price" class="js-filter-block-label filter-block-title--active">Цена,
                        руб.: </label>

                    <div id="ul-filter_custom_price">
                        <div class="filter-block-list--active" id="slider-range-filter_custom_price"></div>

                        <span class="input-range" id="amount-filter_custom_price" style="border:0">
                            <input id="amount-input-left-filter_custom_price" name="price[min]" class="input-range-left"
                                   type="text" value="<?= $price['min'] ?>"/>
                            <input id="amount-input-right-filter_custom_price" name="price[max]"
                                   class="input-range-right" type="text" value="<?= $price['max'] ?>"/>
                            <div style="clear:both"></div>
                        </span>


                        <script>

                            var price_min = <?php echo $price_min; ?>,
                                price_max = <?php echo $price_max; ?>,
                                current_min = <?php echo $price['min']; ?>,
                                current_max = <?php echo $price['max']; ?>;

                        </script>

                    </div>
                </div>
                <div class="filter_item_block">

                </div>
            </div>

            <div class="filter_flex">
                <div class="filter_item_block">
                    <label data-for="filter_inventory" class="js-filter-block-label filter-block-title--active"
                           for="filter_custom_price">Наличие: </label>

                    <ul id="ul-filter_inventory" class="">
                        <?php if (isset($avail["quantity_available"])): ?>
                            <li>
                                <input <?php echo (is_array(\CloudStore\App\Engine\Components\Request::get("inventory")) && in_array("available", \CloudStore\App\Engine\Components\Request::get("inventory"))) ? "checked" : ""; ?>
                                        id="available" type="checkbox" name="inventory[]" value="available" id="">
                                <label style="margin-left:15px;" for="available">Прямо сейчас</label>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($avail["quantity_stock"])): ?>
                            <li>
                                <input <?php echo (is_array(\CloudStore\App\Engine\Components\Request::get("inventory")) && in_array("shipping", \CloudStore\App\Engine\Components\Request::get("inventory"))) ? "checked" : ""; ?>
                                        id="shipping" type="checkbox" name="inventory[]" value="shipping" id="">
                                <label style="margin-left:15px;" for="shipping">В наличии</label>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($avail["quantity_possible"])): ?>
                            <li>
                                <input <?php echo (is_array(\CloudStore\App\Engine\Components\Request::get("inventory")) && in_array("possible", \CloudStore\App\Engine\Components\Request::get("inventory"))) ? "checked" : ""; ?>
                                        id="possible" type="checkbox" name="inventory[]" value="possible" id="">
                                <label style="margin-left:15px;" for="possible">Скоро в продаже</label>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($avail["none"])): ?>
                            <li>
                                <input <?php echo (is_array(\CloudStore\App\Engine\Components\Request::get("inventory")) && in_array("none", \CloudStore\App\Engine\Components\Request::get("inventory"))) ? "checked" : ""; ?>
                                        id="none" type="checkbox" name="inventory[]" value="none" id="">
                                <label style="margin-left:15px;" for="none">Временно отсутствует</label>
                            </li>
                        <?php endif; ?>
                    </ul>

                </div>
            </div>

        </div>

        <script>

            var args = [];

        </script>

        <?php
        if (!empty($filter)):

            $array = $filter

            ?>

            <div class="filter_custom">

                <?php if (!empty($brands)): ?>

                <div class="filter_flex">
                    <div class="filter_item_block">
                        <label data-for="statis-brands"
                               class="js-filter-block-title <?= (!empty(\CloudStore\App\Engine\Components\Request::get('static')['brands'])) ? 'filter-block-title--active' : '' ?>"
                               for="filter_brand">Производитель</label>
                        <ul id="ul-statis-brands"
                            class="js-filter-block-list <?= (!empty(\CloudStore\App\Engine\Components\Request::get('static')['brands'])) ? 'filter-block-list--active' : '' ?>">
                                <?php foreach ($brands as $brand): ?>
                                <?php if (!$brand['brand']) continue ?>
                                    <li>
                                        <input <?php echo in_array($brand['brand'], (isset(\CloudStore\App\Engine\Components\Request::get('static')['brands']) ? \CloudStore\App\Engine\Components\Request::get('static')['brands'] : [])) ? "checked='checked'" : "" ?>
                                                style="width:auto" type="checkbox" name="static[brands][]"
                                                value="<?= $brand['brand'] ?>"
                                                id="<?= \CloudStore\App\Engine\Components\Utils::makeHandle($brand['brand']) ?>">
                                        <label style="margin-left:15px;"
                                               for="<?= \CloudStore\App\Engine\Components\Utils::makeHandle($brand['brand']) ?>"><?= $brand['brand'] ?></label>
                                    </li>
                                <?php endforeach; ?>
                        </ul>

                    </div>
                </div>

                <?php endif; ?>

                <?php for ($i = 0; $i < count($array); $i++): ?>

                    <?php if ($array[$i]['info']['filter_type'] === "1"): ?>
                        <div <?= ($i >= $category['category_filters_per_page']) ? "style=\"display:none\"" : "" ?>
                                class="js-filter-block filter_flex">
                            <?php if (!empty($array[$i]['values'])): ?>
                                <div class="filter_item_block">
                                    <label data-for="<?= $array[$i]['info']['filter_id'] ?>"
                                           class="js-filter-block-title <?= (isset(\CloudStore\App\Engine\Components\Request::get('custom')[$array[$i]['info']['attribute_name']])) ? 'filter-block-title--active' : '' ?>"
                                           for="filter_brand"><?= $array[$i]['info']['attribute_name'] ?></label>
                                    <?php if (!empty($array[$i]['values'])): ?>
                                        <ul id="ul-<?= $array[$i]['info']['filter_id'] ?>"
                                            class="js-filter-block-list <?= (isset(\CloudStore\App\Engine\Components\Request::get('custom')[$array[$i]['info']['attribute_name']])) ? 'filter-block-list--active' : '' ?>">
                                            <?php foreach ($array[$i]['values'] as $cur): ?>
                                                <li>
                                                    <input <?php echo in_array($cur, (isset(\CloudStore\App\Engine\Components\Request::get('custom')[$array[$i]['info']['attribute_name']]) ? \CloudStore\App\Engine\Components\Request::get('custom')[$array[$i]['info']['attribute_name']] : [])) ? "checked='checked'" : "" ?>
                                                            style="width:auto" type="checkbox"
                                                            name="custom[<?= $array[$i]['info']['attribute_name'] ?>][]"
                                                            value="<?= htmlspecialchars($cur) ?>"
                                                            id="<?= \CloudStore\App\Engine\Components\Utils::makeHandle($cur) ?>"/>
                                                    <label style="margin-left:15px;"
                                                           for="<?= \CloudStore\App\Engine\Components\Utils::makeHandle($cur) ?>"><?= $cur ?></label>
                                                </li>

                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif ($array[$i]['info']['filter_type'] === "2"):

                        ?>
                        <div <?= ($i >= $category['category_filters_per_page']) ? "style=\"display:none\"" : "" ?>
                                class="js-filter-block filter_flex">
                            <div class="filter_item_block single-block">
                                <input <?= (isset(\CloudStore\App\Engine\Components\Request::get("single")[$array[$i]['info']['attribute_name']]) AND \CloudStore\App\Engine\Components\Request::get("single")[$array[$i]['info']['attribute_name']] === $array[$i]['info']['attribute_name']) ? "checked" : "" ?>
                                        class="single_input" style="width:auto" type="checkbox"
                                        name="single[<?= $array[$i]['info']['attribute_name'] ?>]"
                                        value="<?= htmlspecialchars($array[$i]['info']['attribute_name']) ?>"
                                        id="single-<?= \CloudStore\App\Engine\Components\Utils::makeHandle($array[$i]['info']['filter_id']) ?>"/>
                                <label class="single-label" style="margin-left:15px;"
                                       for="single-<?= \CloudStore\App\Engine\Components\Utils::makeHandle($array[$i]['info']['filter_id']) ?>"><?= $array[$i]['info']['attribute_name'] ?></label>
                                <div style="clear:both"></div>
                            </div>
                        </div>
                    <?php elseif ($array[$i]['info']['filter_type'] === "3"):

                        ?>

                        <div <?= ($i >= $category['category_filters_per_page']) ? "style=\"display:none\"" : "" ?>
                                class="js-filter-block filter_flex">

                            <div class="filter_item_block">

                                <label data-for="<?= $array[$i]['info']['filter_id'] ?>"
                                       class="js-filter-block-title <?= (true) ? 'filter-block-title--active' : '' ?>"
                                       for="amount-<?= $array[$i]['info']['filter_id'] ?>"><?= $array[$i]['info']['attribute_name'] ?>
                                    : </label>

                                <div class="js-filter-block-list <?= (true) ? 'filter-block-list--active' : '' ?>"
                                     id="ul-<?= $array[$i]['info']['filter_id'] ?>">
                                    <div class="temp-js-attribute-range" id="slider-range-<?= $array[$i]['info']['filter_id'] ?>"></div>

                                    <span class="input-range" id="amount-<?= $array[$i]['info']['filter_id'] ?>"
                                          style="border:0">
                                        <input data-id="<?= $array[$i]['info']['filter_id'] ?>" id="amount-input-left-<?= $array[$i]['info']['filter_id'] ?>"
                                               name="range[<?= $array[$i]['info']['attribute_name'] ?>][left]"
                                               class="input-range-left js-range-input" type="text"
                                               value="<?= $array[$i]['info']['filter_range_min'] ?>"/>
                                        <input data-id="<?= $array[$i]['info']['filter_id'] ?>" id="amount-input-right-<?= $array[$i]['info']['filter_id'] ?>"
                                               name="range[<?= $array[$i]['info']['attribute_name'] ?>][right]"
                                               class="input-range-right js-range-input" type="text"
                                               value="<?= $array[$i]['info']['filter_range_max'] ?>"/>
                                        <input id="range-checked-<?= $array[$i]['info']['filter_id'] ?>" type="hidden" name="range[<?= $array[$i]['info']['attribute_name'] ?>][checked]" value="<?= $array[$i]['info']['checked'] ?>" >
                                        <div style="clear:both"></div>
                                    </span>


                                    <script>


                                        args[args.length] = {

                                            filter_id: <?= $array[$i]['info']['filter_id'] ?>,
                                            filter_range_min: <?php echo floor($array[$i]['info']['filter_range_min']); ?>,
                                            filter_range_max: <?php echo floor($array[$i]['info']['filter_range_max']); ?>,
                                            step: <?php echo(((float)$array[$i]['info']['filter_range_max'] - (float)$array[$i]['info']['filter_range_min']) > 10 ? 1 : 0.1); ?>

                                        };


                                    </script>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>

                <?php endfor; ?>

            </div>
        <?php endif; ?>
        <?php if (!empty($array) AND count($array) > $category['category_filters_per_page'] AND $category['category_filters_per_page'] !== "0"): ?>
            <div class="filter_footer filter-correct1">
                <span data-blocks="<?= $category['category_filters_per_page'] ?>"
                      class="filter-footer__show-more js-init-filters-more"><a>Загрузить еще</a></span>
            </div>
        <?php endif; ?>
        <div class="filter_footer filter-correct1">
            <span class="filter-footer__show-more js-init-filters-more"><a
                        href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/catalog/<?php echo $category['category_handle']; ?>">Сбросить фильтры</a></span>
        </div>
        <div class="filter_footer">
            <input type="submit" name="filter_submit" class="button" value="Поиск"/>
        </div>
        <input type="hidden" name="category_name" value="<?php echo $category["category_handle"]; ?>"/>
        <input type="hidden" name="category_id" value="<?php echo $category["category_id"] ?>"/>
        <input type="hidden" name="range_check" value="0"/>
        <input type="hidden" name="csrf" value="<?= \CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
    </form>
</div>
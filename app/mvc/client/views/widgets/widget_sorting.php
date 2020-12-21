<div class="product-catalog__sorting">
    <div class="product-catalog__sorting-mobile-left">
        <label for="sorting"></label>
        <select id="sorting" class="js-sorting-init product-catalog__sorting-mobile-select">
            <option selected disabled>Сортировать</option>
            <?php
            /**
             * @var array $sortingRules
             */
            foreach ($sortingRules as $key => $value): ?>
                <option <?= isset($value['current']) ? 'selected' : '' ?> value="<?= $value['url'] ?>"><?= $value['display_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="product-catalog__sorting-mobile-right">
        <button class="button product-catalog__sorting-mobile-filter-init js-mobile-filter-init">Фильтры</button>
    </div>
    <div style="clear: both"></div>
</div>
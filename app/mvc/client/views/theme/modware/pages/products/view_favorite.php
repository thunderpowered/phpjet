<header class="grid medium-up--grid--table section-header small--text-center">
    <div class="grid__item medium-up--two-thirds section-header__item">
        <h1 class="section-header__title">Ваши избранные товары</h1>
    </div>
</header>

<!--
<?php //echo $this->widget->widgetSorting->getWidget(); ?>
-->

<?php if ($products): ?>

    <div class="search_list grid grid--no-gutters grid--uniform">

        <?php $this->products = $products; ?>
        <?php $this->includePart("product-list-5"); ?>

    </div>
    <div class="pagination">
        <?php echo $this->controller->getPagination(); ?>
    </div>

<?php endif; ?>



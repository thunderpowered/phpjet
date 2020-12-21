<div class="row">
    <!--<div class="">
    <?php
    /**
     * @var string $category_handle
     */
    $this->widget->widgetFilter->preparedFilters($category_handle); ?></div>-->
    <div class="col-md-3 col-12">
        <div class="side">
            <!-- navigation -->
            <?= $this->widget->widgetMenu->getNavigation(); ?>
            <?= $this->widget->widgetFilter->catalog($category_handle, false); ?>
        </div>
    </div>
    <div class="col-md-9 col-12">
        <!-- bread crumbs -->
        <?= $this->widget->widgetMenu->getBreadCrumbs(); ?>
        <?= $this->widget->widgetSorting->getWidget(); ?>
        <?php
        /**
         * @var int $category_id
         */
        $this->category_id = $category_id; ?>
        <?php
        /**
         * @var array $products
         */
        $this->products = $products; ?>
        <?php $this->includePart("product-list-5"); ?>

        <div class="pagination">
            <?= $this->controller->getPagination() ?>
        </div>
    </div>
</div>


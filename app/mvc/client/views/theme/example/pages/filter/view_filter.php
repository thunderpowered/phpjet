<div class="row">
    <!--<div class=""><?php $this->widget->widgetFilter->preparedFilters($category_handle); ?></div>-->
    <div class="col-md-3 col-12">
        <div class="side">
            <!-- navigation -->
            <?php $this->widget->widgetMenu->getNavigation(); ?>

            <?php $this->widget->widgetFilter->filter(); ?>
        </div>
    </div>
    <div class="col-md-9 col-12">
        <!-- bread crumbs -->
        <?php $this->widget->widgetMenu->getBreadCrumbs(); ?>
        <?php $this->widget->widgetSorting->getWidget(); ?>

        <?php $this->products = $filter_products; ?>
        <?php $this->includePart("product-list-5"); ?>

        <div class="pagination">
            <?= $this->controller->getPagination() ?>
        </div>
    </div>
</div>


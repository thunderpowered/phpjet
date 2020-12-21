<!-- EYE STOPPER -->
<div class="container">
    <div class="row">
        <!-- LEFT COLUMN -->
        <div class="col-12 col-lg-3">
            <?= $this->widget->widgetMenu->getMenuTree(); ?>
        </div>
        <!-- SLIDER -->
        <div class="col-12 col-lg-9">
            <div class="main__column-right loading">
                <?= $this->widget->widgetSlider->getWidget(); ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container">
    <div class="row">
        <!-- MENU TILES -->
        <div class="col-12 col-md-12">
            <?= $this->widget->widgetMenu->getMenuTiles(); ?>
        </div>
    </div>
</div>





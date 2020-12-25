<!-- BANNER -->
<div class="header__upper">
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-lg p-0">
                <?php echo $this->widget->widgetBanner->getWidget(); ?>
            </div>
        </div>
    </div>
</div>
<!-- ACTUAL HEADER -->
<div class="header__middle">
    <div class="container w-100">
        <div class="row flex-nowrap justify-content-between align-items-center d-flex">
            <div class="col-3 header__logotype" data-background="<?php echo $this->widget->widgetLogotype->getLogotype(); ?>">
                <div class="p-3 header__logotype-wrapper">
                    <div style="background-image: url('<?php echo $this->widget->widgetLogotype->getLogotype(); ?>')" class="header__logotype-image"></div>
                    <a title="<?php echo \CloudStore\App\Engine\Config\Config::$config['site_name']; ?>" href="<?php echo \CloudStore\CloudStore::$app->router->getHost(); ?>" class="header__logotype-link"></a>
                </div>
            </div>
            <div class="col-6 flex-nowrap justify-content-start align-items-baseline header__menu-wrapper">
                <?php echo $this->widget->widgetMenu->getHeaderMenu(); ?>
            </div>
            <div class="col-3 flex-nowrap justify-content-end align-baseline header__search p-0">
                <div class="header__search-wrapper p-0 pt-3 pb-3" id="Search">
                    <!-- SEARCH -->
                </div>
            </div>
        </div>
    </div>
</div>

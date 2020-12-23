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
                <nav class="nav d-flex justify-content-start header__menu-nav">
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Игры</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Моды</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Обзоры</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--developers">Разработчикам</a>
                </nav>
            </div>
            <div class="col-3 flex-nowrap justify-content-end align-baseline header__search">
                <div class="header__search-wrapper p-3" id="Search">
                    <!-- SEARCH -->
                </div>
            </div>
        </div>
    </div>
</div>

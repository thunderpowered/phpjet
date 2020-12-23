<!-- BANNER -->
<div class="header__upper">
    <div class="container">
        <div class="row">
            <div class="col-lg">
                <?php echo $this->widget->widgetBanner->getWidget(); ?>
            </div>
        </div>
    </div>
</div>
<!-- ACTUAL HEADER -->
<div class="header__middle">
    <div class="container">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-3 header__logotype" data-background="<?php echo $this->widget->widgetLogotype->getLogotype(); ?>">
                <div class="p-3 header__logotype-wrapper">
                    <div style="background-image: url('<?php echo $this->widget->widgetLogotype->getLogotype(); ?>')" class="header__logotype-image"></div>
                    <a title="<?php echo \CloudStore\App\Engine\Config\Config::$config['site_name']; ?>" href="<?php echo \CloudStore\CloudStore::$app->router->getHost(); ?>" class="header__logotype-link"></a>
                </div>
            </div>
            <div class="col-5 flex-nowrap justify-content-start align-items-baseline header__menu-wrapper">
                <nav class="nav d-flex justify-content-center header__menu-nav">
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Игры</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Моды</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--gamers">Обзоры</a>
                    <a class="p-3 theme__link-color theme__link-color--hover header__menu-item--developers">Разработчикам</a>
                </nav>
            </div>
            <div class="col-4 flex-nowrap justify-content-end align-baseline header__search">
                <div class="header__search-wrapper" id="Search">
                    <!-- SEARCH -->
                </div>
            </div>
        </div>
    </div>
</div>

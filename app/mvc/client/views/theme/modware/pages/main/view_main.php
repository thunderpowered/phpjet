<div class="main w-100">
    <div class="container w-100 theme__background-color3">
        <div class="row flex-nowrap justify-content-between align-items-start theme__text-color">
            <!-- THREE COLUMNS -->
            <div class="col-3 main__left-sidebar theme__border-color theme__border-right">
                <div class="p-3 pt-4 pb-2 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Список игр</span>
                </div>
                <div class="p-3 pt-6 pb-2 theme__border-color">
                    <?php echo $this->widget->widgetMenu->getGameList(); ?>
                </div>
            </div>
            <div class="col-6 main__central">
                <div class="p-3 pt-4 pb-2 flex-nowrap justify-content-start flex-row d-flex">
<!--                    <span class="text-left fs-6 mb-0 d-block"><i class="fas fa-fire"></i></span>-->
                    <h1 class="text-left fs-5 fw-bold mb-0 d-block">Лучшее за месяц &#x1F525; &#x1F525; &#x1F525;</h1>
<!--                    <span class="text-left fs-6 mb-0 d-block"><i class="fas fa-fire"></i></span>-->
                </div>
            </div>
            <div class="col-3 main__right-sidebar theme__border-color theme__background-color2">
                <div class="p-3 pt-4 pb-2 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Новинки</span>
                </div>
                <div class="p-3 pt-6 pb-2 theme__border-color theme__border-bottom">
                    <?php echo $this->widget->widgetHot->getNewMods(); ?>
                </div>
                <div class="p-3 pt-4 pb-2 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Новые пользователи</span>
                </div>
                <div class="p-3 pt-6 pb-2 theme__border-color theme__border-bottom">
                    <!-- LIST GOES HERE -->
                </div>

            </div>
        </div>
    </div>
</div>





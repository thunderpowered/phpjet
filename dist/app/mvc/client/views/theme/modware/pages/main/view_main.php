<?php
/**
 * @var array $bestLastMods
 */
?>
<div class="main w-100">
    <div class="container w-100 theme__background-color3">
        <div class="row flex-nowrap justify-content-between align-items-start theme__text-color">
            <!-- THREE COLUMNS -->
            <div class="col-3 main__left-sidebar theme__border-color theme__border-right p-0">
                <div class="p-4 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Список игр</span>
                </div>
                <div class="p-0 theme__border-color">
                    <?php echo $this->widget->widgetMenu->getGameList(); ?>
                </div>
            </div>
            <div class="col-6 main__central">

                <!-- MAIN PAGE BEST LAST MODS -->
                <?php if ($bestLastMods): ?>
                <div class="p-4 flex-nowrap justify-content-start flex-row d-flex">
                    <h1 class="text-left fs-5 fw-bold mb-0 d-block">Лучшее за месяц &#x1F525;</h1>
                </div>

                <div class="p-4 main__feed-section">
                    <ul class="m-0 p-0 list-unstyled d-flex flex-column justify-content-start align-items-baseline main__feed-list">
                        <?php foreach ($bestLastMods as $key => $mod): ?>
                            <li class="main__feed-item">
                                <div class="feed-item__header">
                                    <span class="feed-item__category"><?php echo $mod['game_name'] ?></span>
                                </div>
                                <div class="feed-item__main">
                                    <h2 class="feed-item__title"><a href="<?php echo $mod['mod_url']; ?>"><?php echo $mod['mod_name'] ?></a></h2>
                                    <div class="feed-item__description d-flex flex-nowrap justify-content-between align-items-baseline">
                                        <?php if ($mod['mod_icon']): ?>
                                        <div class="feed-item__thumbnail">
                                            <img src="<?php echo $mod['mod_icon'] ?>" alt="<?php echo $mod['mod_name'] ?>">
                                        </div>
                                        <?php endif; ?>
                                        <div class="feed-item__text"><?php echo $mod['description'] ?></div>
                                    </div>
                                </div>
                                <div class="feed-item__footer">

                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
            <div class="col-3 main__right-sidebar theme__border-color theme__border-left p-0">
                <div class="p-4 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Новинки</span>
                </div>
                <div class="p-3 pt-0 pb-0 theme__border-color--accent theme__border-bottom theme__border--thicker theme__background-color2">
                    <?php echo $this->widget->widgetHot->getNewMods(); ?>
                </div>
                <div class="p-4 theme__border-color theme__border-bottom">
                    <span class="text-left fs-6 fw-bold">Новые пользователи</span>
                </div>
                <div class="p-3 pt-6 pb-2 theme__border-color theme__border-bottom theme__background-color2">
                    <!-- LIST GOES HERE -->
                </div>
            </div>
        </div>
    </div>
</div>





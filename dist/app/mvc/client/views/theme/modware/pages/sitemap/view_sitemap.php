<?php $this->widget->widgetMenu->getBreadCrumbsCustom([["Карта сайта" => \Jet\App\Engine\Core\Router::getURL()]]); ?>

<h1 class="small--text-center">Карта сайта</h1>

<div class="grid">
    <div class="grid__item">
        <div class="row">
            <?php if ($sitemap): ?>

                <div class="col-12 col-md-4">
                    <?php if ($sitemap["site"]): ?>
                    <div class="map-tree">
                        <?= $sitemap["site"] ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-4">
                    <?php if (!empty($sitemap["categories"]["left"])): ?>
                        <div class="map-tree">
                            <?= $sitemap["categories"]["left"] ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-4">
                    <?php if (!empty($sitemap["categories"]["right"])): ?>
                        <div class="map-tree">
                            <?= $sitemap["categories"]["right"] ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>
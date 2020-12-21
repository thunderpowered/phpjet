<?php
/**
 * @var array $slider
 */
if ($slider): ?>

    <!--<div class="owl-nav--custom owl-prev"><div class="owl-prev-trigger"><i class="fa fa-angle-left" aria-hidden="true"></i></div></div>
    <div class="owl-nav--custom owl-next"><div class="owl-next-trigger"><i class="fa fa-angle-right" aria-hidden="true"></i></div></div>-->

    <div class="slider-blocker"></div>

    <div class="slider-wrapper slider-wrapper--classic owl-carousel owl-carousel2">

        <?php foreach ($slider as $item): ?>

            <div class="section--slider__slider-item section--slider__slider-item__image">

                <div style="background-image:url(<?php echo \CloudStore\CloudStore::$app->tool->utils->getImageLink($item['slider_image']); ?>)"
                     class="slider__item">

                    <a data-id="<?=$item['slider_id'] ?>" class="slider-item__description-wrapper slider-item__description-link item__link link--big"
                       href="<?= $item['slider_link'] ?>">
                        <!--                        <span>--><?php //echo $item['slider_title']; ?><!--</span>-->
                    </a>
                </div>

                <!--
                <div style="background-image:url( <?php echo \CloudStore\CloudStore::$app->tool->utils->getImageLink($item['slider_image']); ?> )" class="section--slider__slider-item__image"></div>
                <div class="section--slider__slider-item__description">

                    <div class="slider-item__description-wrapper">
                        <h2 class="slider-item__description-title"><?php echo $item['slider_title']; ?></h2>
                        <span class="slider-item__description-text"></span>
                        <a href="<?php echo $item['slider_link']; ?>" class="slider-item__description-link button">Перейти</a>
                    </div>

                </div>
                -->

            </div>

        <?php endforeach; ?>

    </div>
    <!-- @todo JAVASCRIPT -->
    <div class="slider__navigation-panel">
        <ul class="slider__navigation-list">
            <?php for ($i = 0, $l = count($slider); $i < $l; $i++): ?>
                <li data-order="<?= $i ?>" id="owlitem-<?= $slider[$i]['slider_id'] ?>" class="slider__navigation-item"><a href="<?= $slider[$i]['slider_link'] ?>" class="slider__navigation-link o-table-like"><span class="slider__navigation-text o-table-cell-like "><?= $slider[$i]['slider_title'] ?></span></a></li>
            <?php endfor; ?>
        </ul>
    </div>

<?php endif; ?>
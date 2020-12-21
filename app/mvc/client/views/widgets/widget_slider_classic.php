<?php
/**
 * @var array $slider
 */
if ($slider): ?>

    <!--<div class="owl-nav--custom owl-prev"><div class="owl-prev-trigger"><i class="fa fa-angle-left" aria-hidden="true"></i></div></div>
    <div class="owl-nav--custom owl-next"><div class="owl-next-trigger"><i class="fa fa-angle-right" aria-hidden="true"></i></div></div>-->

    <div class="slider-blocker"></div>

    <div class="slider-wrapper owl-carousel owl-carousel2">

        <?php foreach ($slider as $item): ?>

            <div class="section--slider__slider-item section--slider__slider-item__image">

                <div style="background-image:url(<?php echo \CloudStore\CloudStore::$app->tool->utils->getImageLink($item['slider_image']); ?>)"
                     class="slider__item">

                    <a class="slider-item__description-wrapper slider-item__description-link item__link link--big"
                       href="<?= $item['slider_link'] ?>">
                        <?php echo $item['slider_title']; ?>
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

<style>
    .angle-left {

        background-image: url("/common/a-right.png");
        background-size: 100%;
        background-position: center;
        background-repeat: no-repeat;
        width: 14px;
        height: 28px;

        margin: 0 !important;
        padding: 0 !important;

        transform: rotateZ(180deg);

        display: block !important;

    }

    .angle-right {

        background-image: url("/common/a-right.png");
        background-size: 100%;
        background-position: center;
        background-repeat: no-repeat;
        width: 14px;
        height: 28px;
        margin: 0 !important;
        padding: 0 !important;

        display: block !important;

    }
</style>

<script>

    $(document).ready(function() {

        var owl_1 = $(".slider-wrapper");

        owl_1.owlCarousel({
            items: 1,
            nav: true,
            dots: true,
            mouseDrag: false,
            autoplay: true,
            autoplayTimeout: 10000,
            loop: true,
            navSpeed: 1500,
            dotsSpeed: 1500,
            autoplaySpeed: 1500,
            navText: ['<div class="angle-left"></div>', '<div class="angle-right"></div>']
        });

        owl_1.on('resized.owl.carousel', function (event) {
            $(window).trigger('redraw');
        });

        owl_1.on('translate.owl.carousel', function (event) {

            $(".slider-blocker").css("z-index", 999);
            $(".slider-item__description-wrapper").css('opacity', 0);
            $(".owl-nav").css('opacity', 0);

        });

        owl_1.on('translated.owl.carousel', function (event) {

            $(".slider-blocker").css("z-index", -1);
            $(".slider-item__description-wrapper").css('opacity', 1);
            $(".owl-nav").css('opacity', 1);

        });

        $(".owl-item.active .slider-item__description-wrapper").animate({'opacity': '1'});

        $(".owl-nav").animate({'opacity': '1'});

        $('.owl-next-trigger').click(function () {

            /*$('.owl-carousel').trigger('stop.owl.autoplay');

             owl_1.trigger('next.owl.carousel', [1500]);

             $('.owl-carousel').trigger('refresh.owl.carousel');*/
        });

        $('.owl-prev-trigger').click(function () {

            /*$('.owl-carousel').trigger('stop.owl.autoplay');

             owl_1.trigger('prev.owl.carousel', [1500]);

             /*$('.owl-carousel').trigger('refresh.owl.carousel');*/

        });

    });

</script>
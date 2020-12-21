<div class="slider__column-right">
    <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[1]['slider_image']); ?>)"
         class="slider__item slider__item_small">
        <!--            <img class="slider-image" src="<?= \CloudStore\App\Engine\Core\Router::getHost() ?>/<?= $slider[1]['slider_image'] ?>" alt="slide_1" />-->
        <a class="item__link link--small"
           href="<?= $slider[1]['slider_link'] ?>"><span><?= $slider[1]['slider_title'] ?></span></a>
    </div>
    <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[2]['slider_image']); ?>)"
         class="slider__item slider__item_small">
        <!--            <img class="slider-image" src="<?= \CloudStore\App\Engine\Core\Router::getHost() ?>/<?= $slider[2]['slider_image'] ?>" alt="slide_1" />-->
        <a class="item__link link--small"
           href="<?= $slider[2]['slider_link'] ?>"><span><?= $slider[2]['slider_title'] ?></span></a>
    </div>
</div>
<div class="slider__column-left">

    <!-- FOR MOBILE DEVICES -->
    <div class="slider-wrapper owl-carousel">

        <div class="section--slider__slider-item">

            <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[0]['slider_image']); ?>)"
                 class="slider__item slider__item_big">
                <a class="item__link link--big"
                   href="<?= $slider[0]['slider_link'] ?>"><span><?php echo $slider[0]['slider_title']; ?></span></a>
            </div>

        </div>

        <div class="section--slider__slider-item">

            <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[1]['slider_image']); ?>)"
                 class="slider__item slider__item_big">
                <a class="item__link link--big"
                   href="<?= $slider[1]['slider_link'] ?>"><span><?php echo $slider[1]['slider_title']; ?></span></a>
            </div>

        </div>

        <div class="section--slider__slider-item">

            <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[2]['slider_image']); ?>)"
                 class="slider__item slider__item_big">
                <a class="item__link link--big"
                   href="<?= $slider[2]['slider_link'] ?>"><span><?php echo $slider[2]['slider_title']; ?></span></a>
            </div>

        </div>

    </div>

    <div style="background-image:url(<?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($slider[0]['slider_image']); ?>)"
         class="slider--center hidden-xs hideen-sm slider__item slider__item_big">
        <!--            <img class="slider-image" src="<?= \CloudStore\App\Engine\Core\Router::getHost() ?>/<?= $slider[0]['slider_image'] ?>" alt="slide_1" />-->
        <a class="item__link link--big"
           href="<?= $slider[0]['slider_link'] ?>"><span><?php echo $slider[0]['slider_title']; ?></span></a>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.slider-image').liCover({
            parent: $('.slider__item'),
            veticalAlign: 'middle',
            align: 'center'
        });
    });
</script>

<!-- OWL CAROUSEL -->
<script>
    $(document).ready(function () {

        var owl_1 = $(".owl-carousel");

        owl_1.owlCarousel({
            items: 1,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 10000,
            loop: true,
            navSpeed: 1000,
            dotsSpeed: 1000,
            autoplaySpeed: 1000
        });

    });
</script>
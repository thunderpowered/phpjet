<div class="slider-wrapper owl-carousel">

    <?php if ($slider): ?>

        <?php foreach ($slider as $item): ?>

            <div class="section--slider__slider-item">

                <div style="background-image:url( <?php echo \CloudStore\App\Engine\Components\Utils::getImageLink($item['slider_image']); ?> )"
                     class="section--slider__slider-item__image"></div>
                <div class="section--slider__slider-item__description">

                    <div class="slider-item__description-wrapper">
                        <h2 class="slider-item__description-title"><?php echo $item['slider_title']; ?></h2>
                        <span class="slider-item__description-text"></span>
                        <a href="<?php echo $item['slider_link']; ?>" class="slider-item__description-link button">Перейти</a>
                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
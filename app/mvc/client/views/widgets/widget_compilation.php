<div class="section--compilation__slider-wrapper owl-carousel">

    <?php if ($products): ?>

        <?php foreach ($products as $product): ?>

            <div class="section--compilation__slider-item">
                <a class="section--compilation__slider-link"
                   href="<?php echo \CloudStore\App\Engine\Core\Router::getHost() . '/products/' . $product['handle']; ?>">
                    <div class="section--compilation__slider-item-image">
                        <?php echo \CloudStore\App\Engine\Components\Utils::getThumbnail($product["image_link"]); ?>
                    </div>
                    <div class="section--compilation__slider-item-title">
                        <?php echo $product["title"]; ?>
                    </div>
                </a>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
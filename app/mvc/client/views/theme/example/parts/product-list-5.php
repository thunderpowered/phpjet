<?php
if ($this->products): ?>
    <ul class="main__product-list o-flex-container">
        <?php foreach ($this->products as $product): ?>
            <li class="main__product-item o-wrapper--regular">
                <?php if (!empty($this->category)): ?>
                <a href="/products/<?= $product->url ?>?category_id=<?= $this->category ?>"
                   class="main__product-link">
                    <?php else: ?>
                    <a href="/products/<?= $product->url ?>" class="main__product-link">
                        <?php endif; ?>
                        <div class="main__product-image-wrapper">
                            <?= $product->thumbnail ?>
                        </div>
                        <div class="main__product-vendor text-center"><?= $product->brand ?></div>
                        <div class="main__product-title text-center font-weight-bold"><?= $product->title ?></div>
                        <div class="main__product-rating">
                            <ul data-my-rating="<?= $product->my_rating ?>" data-rating="<?= $product->rating ?>"
                                id="rating-<?= $product->id ?>" data-id="<?= $product->id ?>"
                                class="main__product-rating-list js-rating-list o-flex-container">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <li class="main__product-rating-item">
                                        <a data-i="<?= $i ?>" href="" class="main__product-rating-link js-rating-item">
                                            <i class="fa fa-star"></i>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                        <div class="main__product-code text-right">Арт. <?= $product->sku ?></div>
                    </a>
                    <div class="main__product-cart-wrapper o-flex-container">
                        <div class="main__product-price font-weight-bold"><?= $product->price ?></div>
                        <div class="main__product-cart">
                            <div id="add-<?= $product->url ?>" data-handler="<?= $product->url ?>"
                                 data-id="<?= $product->id ?>"
                                 class="main__product-cart-add w-50 js-cart-add">
                                <span class="">+</span>
                            </div>
                            <div class="main__product-cart-amount o-flex-container js-cart-amount-box">
                                <div class="amount__button-wrapper">
                                    <button class="amount__button js-amount-minus">-</button>
                                </div>
                                <div class="amount__input-wrapper">
                                    <label class="d-none" for="amount-<?= $product->id ?>"></label>
                                    <input disabled id="amount-<?= $product->id ?>" type="text"
                                           class="amount__input js-amount-input">
                                </div>
                                <div class="amount__button-wrapper">
                                    <button class="amount__button js-amount-plus">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button data-id="<?= $product->id ?>"
                            class="main__product-favorite js-favorite <?= $product->favorite ? "is-favorite" : "" ?>">
                        <span class="main__product-favorite-indicator o-block-center"></span>
                    </button>
            </li>

        <?php endforeach; ?>

    </ul>

<?php else: ?>

    <p class="no-products">По данному запросу товары не найдены</p>

<?php endif; ?>
<h1 class="small--text-center">Корзина покупателя</h1>
<form action="/cart" method="post" novalidate class="cart">
    <table class="responsive-table cart-table">
        <thead class="cart__row visually-hidden">
        <th colspan="2">Товар</th>
        <th>Количество</th>
        <th>Всего</th>
        </thead>
        <tbody id="CartProducts">

        <?php if ($cart): ?>

            <?php foreach ($cart as $cur): ?>

                <tr class="cart__row responsive-table__row">
                    <td class="cart__cell--image text-center">
                        <a href="/products/<?= $cur['handle'] ?>" class="cart__image">
                            <?php echo CloudStore\App\Engine\Components\Utils::getThumbnail($cur['image']); ?>
                        </a>
                    </td>
                    <td>
                        <p class="cart-vendor-title"><?= $cur['brand'] ?></p>
                        <a href="/products/<?= $cur['handle'] ?>" class="h5">
                            <?= $cur['title'] ?>
                        </a>
                        <span class="cart-vendor-title">Возможность доставки и самовывоза: <span><?= $cur['status']['shipping_date'] ?></span></span>
                    </td>
                    <td class="cart__cell--quantity">
                        <label for="CartUpdate" class="cart__quantity-label medium-up--hide">Количество</label>
                        <div class="js-qty">
                            <input data-temp-id="<?= $cur['cart_id'] ?>"
                                   data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" type="text"
                                   value="<?= $cur['cart_count'] ?>" id="CartUpdate_<?= $cur['cart_id'] ?>"
                                   name="updates[]" pattern="[0-9]*" data-line="1" class="js-qty__input cart_update"
                                   aria-live="polite">
                            <button data-temp-id="<?= $cur['cart_id'] ?>"
                                    data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" type="button"
                                    class="js-qty__adjust js-qty__adjust--minus cart_minus"
                                    aria-label="Уменьшить количество">
                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 22 3"
                                     class="icon icon-minus">
                                    <path fill="#000" d="M21.5.5v2H.5v-2z" fill-rule="evenodd"></path>
                                </svg>
                                <span class="icon__fallback-text">−</span>
                            </button>
                            <button data-temp-id="<?= $cur['cart_id'] ?>"
                                    data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" type="button"
                                    class="js-qty__adjust js-qty__adjust--plus cart_plus"
                                    aria-label="Увеличить количество">
                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 22 21"
                                     class="icon icon-plus">
                                    <path d="M12 11.5h9.5v-2H12V0h-2v9.5H.5v2H10V21h2v-9.5z" fill="#000"
                                          fill-rule="evenodd"></path>
                                </svg>
                                <span class="icon__fallback-text">+</span>
                            </button>
                        </div>
                    </td>
                    <td class="cart__cell--total">

                            <span class="cart__item-total">
                                <?= CloudStore\App\Engine\Components\Utils::asPrice($cur['cart_price']) ?>
                            </span>

                    </td>

                    <td>
                        <button data-id="<?= $cur['cart_id'] ?>" class="cart__item-delete js-cart-delete"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>
    </table>
    <div class="grid cart__row">

        <div class="grid__item medium-up--one-half">
            <label for="CartSpecialInstructions">Комментарий к заказу</label>
            <textarea name="note" id="CartSpecialInstructions"
                      class="cart__note"><?= \CloudStore\App\Engine\Components\Request::getSession('note') ?></textarea>
        </div>

        <div class="grid__item cart__buttons text-right small--text-center medium-up--one-half">
            <p class="h3 cart__subtotal" id="CartSubtotal"><?= $sum ?></p>
            <p id="cartDiscountTotal">

            </p>
            <p class="cart__taxes">Доставка рассчитывается при оформлении заказа</p>
            <input type="submit" name="checkout" class="btn" value="Оформление заказа">
            <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
        </div>
    </div>
</form>


<button class="order-summary-toggle order-summary-toggle--show" data-drawer-toggle="[data-order-summary]">
    <div class="wrap">
        <div class="order-summary-toggle__inner">
            <div class="order-summary-toggle__icon-wrapper">
                <svg width="20" height="19" xmlns="http://www.w3.org/2000/svg" class="order-summary-toggle__icon">
                    <path d="M17.178 13.088H5.453c-.454 0-.91-.364-.91-.818L3.727 1.818H0V0h4.544c.455 0 .91.364.91.818l.09 1.272h13.45c.274 0 .547.09.73.364.18.182.27.454.18.727l-1.817 9.18c-.09.455-.455.728-.91.728zM6.27 11.27h10.09l1.454-7.362H5.634l.637 7.362zm.092 7.715c1.004 0 1.818-.813 1.818-1.817s-.814-1.818-1.818-1.818-1.818.814-1.818 1.818.814 1.817 1.818 1.817zm9.18 0c1.004 0 1.817-.813 1.817-1.817s-.814-1.818-1.818-1.818-1.818.814-1.818 1.818.814 1.817 1.818 1.817z"/>
                </svg>
            </div>
            <div class="order-summary-toggle__text order-summary-toggle__text--show">
                <span>Показать итоговый заказ</span>
                <svg width="11" height="6" xmlns="http://www.w3.org/2000/svg" class="order-summary-toggle__dropdown"
                     fill="#000">
                    <path d="M.504 1.813l4.358 3.845.496.438.496-.438 4.642-4.096L9.504.438 4.862 4.534h.992L1.496.69.504 1.812z"/>
                </svg>
            </div>
            <div class="order-summary-toggle__text order-summary-toggle__text--hide">
                <span>Скрыть итоговый заказ</span>
                <svg width="11" height="7" xmlns="http://www.w3.org/2000/svg" class="order-summary-toggle__dropdown"
                     fill="#000">
                    <path d="M6.138.876L5.642.438l-.496.438L.504 4.972l.992 1.124L6.138 2l-.496.436 3.862 3.408.992-1.122L6.138.876z"/>
                </svg>
            </div>
            <div class="order-summary-toggle__total-recap total-recap" data-order-summary-section="toggle-total-recap">
                <span class="total-recap__final-price"
                      data-checkout-payment-due-target=""><?= CloudStore\App\Engine\Components\Utils::asPrice($full_price) ?></span>
            </div>
        </div>
    </div>
</button>

<div class="content" data-content="">
    <div class="wrap">
        <div class="sidebar" role="complementary">
            <div class="sidebar__header">

                <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>" class="logo logo--left">
                    <h1 class="logo__text"><?= \CloudStore\App\Engine\Config\Config::$config['site_name'] ?></h1>
                </a>

            </div>
            <div class="sidebar__content">
                <div class="order-summary order-summary--is-collapsed" data-order-summary="">
                    <h2 class="visually-hidden">Итог заказа</h2>

                    <div class="order-summary__sections">
                        <div class="order-summary__section order-summary__section--product-list">
                            <div class="order-summary__section__content">
                                <table class="product-table">
                                    <caption class="visually-hidden">Корзина</caption>
                                    <thead>
                                    <tr>
                                        <th scope="col"><span class="visually-hidden">Изображение товара</span></th>
                                        <th scope="col"><span class="visually-hidden">Описание</span></th>
                                        <th scope="col"><span class="visually-hidden">Количество</span></th>
                                        <th scope="col"><span class="visually-hidden">Цена</span></th>
                                    </tr>
                                    </thead>
                                    <tbody data-order-summary-section="line-items">
                                    <?php if ($checkout_products) { ?>
                                        <?php foreach ($checkout_products as $cur) { ?>
                                            <tr class="product">
                                                <td class="product__image">
                                                    <div class="product-thumbnail">
                                                        <div class="product-thumbnail__wrapper">
                                                            <?php echo CloudStore\App\Engine\Components\Utils::getThumbnail($cur['image']); ?>
                                                        </div>
                                                        <span class="product-thumbnail__quantity"
                                                              aria-hidden="true"><?= $cur['orders_count'] ?></span>
                                                    </div>

                                                </td>
                                                <td class="product__description">
                                                    <span class="product__description__name order-summary__emphasis"><?= $cur['title'] ?></span>
                                                    <span class="product__description__variant order-summary__small-text">Возможность доставки и самовывоза: <span
                                                                class="checkout_product-status"><?= $cur['status']['shipping_date'] ?></span></span>
                                                </td>
                                                <td class="product__quantity visually-hidden"><?= $cur['orders_count'] ?></td>
                                                <td class="product__price">
                                                    <span class="order-summary__emphasis"><?= CloudStore\App\Engine\Components\Utils::asPrice($cur['price']) ?></span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    </tbody>
                                </table>

                                <div class="order-summary__scroll-indicator">
                                    Scroll for more items
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="12" viewBox="0 0 10 12">
                                        <path d="M9.817 7.624l-4.375 4.2c-.245.235-.64.235-.884 0l-4.375-4.2c-.244-.234-.244-.614 0-.848.245-.235.64-.235.884 0L4.375 9.95V.6c0-.332.28-.6.625-.6s.625.268.625.6v9.35l3.308-3.174c.122-.117.282-.176.442-.176.16 0 .32.06.442.176.244.234.244.614 0 .848"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>


                        <div class="order-summary__section order-summary__section--total-lines"
                             data-order-summary-section="payment-lines">
                            <table class="total-line-table">
                                <caption class="visually-hidden">Итоговая стоимость</caption>
                                <thead>
                                <tr>
                                    <th scope="col"><span class="visually-hidden">Описание</span></th>
                                    <th scope="col"><span class="visually-hidden">Цена</span></th>
                                </tr>
                                </thead>
                                <tbody class="total-line-table__tbody">
                                <tr class="total-line total-line--subtotal">
                                    <td class="total-line__name">Промежуточный итог</td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis"
                                                  data-checkout-subtotal-price-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($checkout_price) ?>
                                            </span>
                                    </td>
                                </tr>


                                <tr class="total-line total-line--shipping">
                                    <td class="total-line__name">Доставка</td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis"
                                                  data-checkout-total-shipping-target="0">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice(\CloudStore\App\Engine\Components\Request::getSession('shipper_price')) ?>
                                            </span>
                                    </td>
                                </tr>

                                <?php if (\CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled')) { ?>

                                    <tr id="price_delta" class="total-line total-line--shipping">
                                        <td class="total-line__name">Скидка:</td>
                                        <td class="total-line__price">
                                                <span class="order-summary__emphasis js-price-delta"
                                                      data-price-delta="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_price_delta') ?>"
                                                      data-checkout-total-shipping-target="0">
                                                    <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_price_delta') == 0 ? '' : '-' ?><?= CloudStore\App\Engine\Components\Utils::asPrice(\CloudStore\App\Engine\Components\Request::getSession('checkout_price_delta')) ?>
                                                </span>
                                        </td>
                                    </tr>

                                <?php } ?>

                                <?php if (\CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled')) { ?>

                                    <tr id="points_after" class="total-line total-line--shipping">
                                        <td class="total-line__name">Баллы после покупки:</td>
                                        <td class="total-line__price">
                                                <span class="order-summary__emphasis"
                                                      data-checkout-total-shipping-target="0">
                                                    <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_new_points') ?>
                                                </span>
                                        </td>
                                    </tr>

                                <?php } ?>

                                <tr class="total-line total-line--shipping js-payment-charge hidden">
                                    <td class="total-line__name"><span class="checkout_table_span js-charge-name-out">Сервисный сбор</span>
                                        <span class="tooltip_selector">Подробнее</span></td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis js-payment-charge-out"
                                                  data-checkout-total-shipping-target="0">

                                            </span>
                                    </td>
                                </tr>
                                <tr class="total-line total-line--shipping">
                                    <td>
                                        <div class="order-summary__emphasis tooltip_block js-payment-charge-description"
                                             data-checkout-total-shipping-target="0">
                                            <div class="tooltip_triangle"></div>
                                            <h3 class="js-payment-charge-title-out">Сервисный сбор</h3>
                                            <span class="js-payment-charge-description-out">Информация о сборе. О том, что это вообще такое и почему нужно. Тут может быть много текста, это нужно предусмотреть.</span>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="total-line total-line--taxes hidden" data-checkout-taxes="">
                                    <td class="total-line__name">Налоги</td>
                                    <td class="total-line__price">
                                        <span class="order-summary__emphasis" data-checkout-total-taxes-target="0">0.00 р.</span>
                                    </td>
                                </tr>

                                </tbody>
                                <tfoot class="total-line-table__footer">
                                <tr class="total-line">
                                    <td class="total-line__name payment-due-label">
                                        <span class="payment-due-label__total">Всего</span>
                                    </td>
                                    <td class="total-line__price payment-due">
                                        <span class="payment-due__currency">RUB</span>
                                        <span class="payment-due__price js-full-price-out"
                                              data-checkout-payment-due-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($full_price) ?>
                                            </span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="main" role="main">
            <div class="main__header">

                <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>" class="logo logo--left">
                    <h1 class="logo__text"><?= \CloudStore\App\Engine\Config\Config::$config['site_name'] ?></h1>
                </a>

                <ul class="breadcrumb ">
                    <li class="breadcrumb__item breadcrumb__item--completed">
                        <a class="breadcrumb__link" href="/cart">Корзина</a>
                        <svg class="icon-svg icon-svg--size-10 breadcrumb__chevron-icon rtl-flip" role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                            <path d="M2 1l1-1 4 4 1 1-1 1-4 4-1-1 4-4"></path>
                        </svg>
                    </li>

                    <li class="breadcrumb__item breadcrumb__item--completed">
                        <a class="breadcrumb__link" href="/checkout/step1?token=<?= $token ?>">Информация о
                            покупателе</a>
                        <svg class="icon-svg icon-svg--size-10 breadcrumb__chevron-icon rtl-flip" role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                            <path d="M2 1l1-1 4 4 1 1-1 1-4 4-1-1 4-4"></path>
                        </svg>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--completed">
                        <a class="breadcrumb__link" href="/checkout/step2?token=<?= $token ?>">Способ доставки</a>
                        <svg class="icon-svg icon-svg--size-10 breadcrumb__chevron-icon rtl-flip" role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                            <path d="M2 1l1-1 4 4 1 1-1 1-4 4-1-1 4-4"></path>
                        </svg>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--current">
                        <span class="breadcrumb__text">Способ оплаты</span>
                    </li>
                </ul>

                <div data-alternative-payments="">
                </div>

            </div>
            <div class="main__content">
                <div class="step" data-step="payment_method">

                    <form data-payment-form="" class="edit_checkout animate-floating-labels"
                          action="/checkout/step3?token=<?= CloudStore\App\Engine\Components\Utils::generateCheckoutToken() ?>"
                          accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="✓"><input
                                type="hidden" name="_method" value="patch"><input type="hidden"
                                                                                  name="authenticity_token"
                                                                                  value="brmFCpyveNm13m/FJL+9taPUHs7dFLTlbodU0vAv2xeRGsI8DVvCSMUldVrM+QSjlzz9U1babZw1sQWrcLa6ug==">

                        <input type="hidden" name="previous_step" id="previous_step" value="payment_method">
                        <input type="hidden" name="step">
                        <input type="hidden" name="s" id="s">


                        <div class="step__sections">
                            <div class="section">
                                <div class="content-box">
                                    <div class="content-box__row content-box__row--tight-spacing-vertical content-box__row--secondary">
                                        <div class="review-block">
                                            <div class="review-block__inner">
                                                <div class="review-block__label">
                                                    Адрес доставки
                                                </div>
                                                <div class="review-block__content">
                                                    <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_city') ?>
                                                    , <?= CloudStore\App\Engine\Core\System::getControllerObject()::GetRegion() ?>
                                                    , <?= CloudStore\App\Engine\Core\System::getControllerObject()::GetCountry() ?>
                                                </div>
                                            </div>
                                            <div class="review-block__link">
                                                <a class="link--small" href="/checkout/step1?token=<?= $token ?>">
                                                    <span aria-hidden="">Редактировать</span>
                                                    <span class="visually-hidden">Редактировать адрес доставки</span>
                                                </a></div>
                                        </div>

                                        <hr class="content-box__hr content-box__hr--tight">
                                        <div class="review-block">
                                            <div class="review-block__inner">
                                                <div class="review-block__label">
                                                    Способ доставки
                                                </div>
                                                <div class="review-block__content">
                                                    <?= \CloudStore\App\Engine\Components\Request::getSession('shipper_name') ?>
                                                    ·
                                                    <?= CloudStore\App\Engine\Components\Utils::asPrice(\CloudStore\App\Engine\Components\Request::getSession('shipper_price')) ?>
                                                </div>
                                            </div>
                                            <div class="review-block__link">
                                                <a class="link--small" href="/checkout/step2?token=<?= $token ?>">
                                                    <span aria-hidden="">Редактировать</span>
                                                    <span class="visually-hidden">Редактировать способ доставки</span>
                                                </a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section section--payment-method" data-payment-method="">
                                <div class="section__header">
                                    <h2 class="section__title">Способ оплаты</h2>
                                    <p class="section__text">
                                        Все транзакции безопасны и зашифрованы. Информация о кредитной карте не
                                        сохраняется.
                                    </p>
                                </div>

                                <div class="section__content">

                                    <div data-payment-subform="required">
                                        <div class="content-box">


                                            <?php if (!empty($payment_methods)): ?>

                                                <?php foreach ($payment_methods as $payment_method): ?>

                                                    <div class="radio-wrapper content-box__row "
                                                         data-gateway-group="manual"
                                                         data-select-gateway="<?php echo $payment_method['payment_id'] ?? ""; ?>">
                                                        <div class="radio__input">
                                                            <input class="input-radio js-payment-method"
                                                                   id="checkout_payment_gateway_<?php echo $payment_method['payment_id'] ?? ""; ?>"
                                                                   data-charge-name="<?php echo $payment_method['payment_charge_name'] ?? ""; ?>"
                                                                   data-full-charge-description="<?php echo $payment_method['payment_charge_description'] ?? ""; ?>"
                                                                   data-full-charge-selector="<?php echo $payment_method['payment_full_charge_selector'] ?? ""; ?>"
                                                                   data-full-charge="<?php echo $payment_method['payment_full_charge'] ?? ""; ?>"
                                                                   data-full-price="<?php echo $payment_method['payment_full_price'] ?? ""; ?>"
                                                                   data-backup="payment_gateway_84194051"
                                                                   aria-expanded="true"
                                                                   aria-controls="payment-gateway-subfields-84194051"
                                                                   type="radio"
                                                                   value="<?php echo $payment_method['payment_id'] ?? ""; ?>"
                                                                   checked="checked" name="checkout_payment_gateway">
                                                        </div>

                                                        <label class="radio__label content-box__emphasis "
                                                               for="checkout_payment_gateway_<?php echo $payment_method['payment_id'] ?? ""; ?>">
                                                            <div class="radio__label__primary">
                                                                <?php echo $payment_method['payment_name'] ?? "Стандартная оплата"; ?>
                                                            </div>

                                                            <div class="radio__label__accessory">
                                                            </div>
                                                        </label>
                                                    </div>

                                                    <div class="radio-wrapper content-box__row content-box__row--secondary"
                                                         data-subfields-for-gateway="<?php echo $payment_method['payment_id'] ?? ""; ?>"
                                                         id="payment-gateway-subfields-84194051">
                                                        <div class="blank-slate">
                                                            <p><?php echo $payment_method['payment_description'] ?? "Описание отсутствует" ?></p>
                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>

                                            <?php endif; ?>

                                        </div>

                                        <?php if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) { ?>

                                            <div class="section section--half-spacing-top section--optional section--fade-in"
                                                 data-buyer-accepts-marketing="">
                                                <div class="section__content">
                                                    <div class="checkbox-wrapper">
                                                        <div class="checkbox__input">
                                                            <input
                                                                <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_points_enabled') ? 'checked="checked"' : '' ?>data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"
                                                                class="input-checkbox"
                                                                data-backup="buyer_accepts_marketing" type="checkbox"
                                                                value="1" name="checkout_pontez"
                                                                id="checkout_buyer_accepts_marketing">
                                                        </div>
                                                        <label class="checkbox__label"
                                                               for="checkout_buyer_accepts_marketing">
                                                            Использовать баллы (60% от покупки)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                    </div>

                                    <div data-payment-subform="gift_cards" class="hidden">
                                        <input value="free" disabled="disabled" size="30" type="hidden"
                                               name="checkout[payment_gateway]" aria-expanded="false">
                                        <div class="content-box blank-slate">
                                            <div class="content-box__row">
                                                <i class="blank-slate__icon icon icon--free-tag"></i>
                                                <p>Ваш заказ может быть оплачен вашей подарочной картой</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-payment-subform="free" class="hidden">
                                        <input value="free" disabled="disabled" size="30" type="hidden"
                                               name="checkout[payment_gateway]" aria-expanded="false">
                                        <div class="content-box blank-slate">
                                            <div class="content-box__row">
                                                <i class="blank-slate__icon icon icon--free-tag"></i>
                                                <p>Ваш заказ <strong>free</strong>. Оплата не требуется.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section section--billing-address" data-billing-address="">
                                <div class="section__header">
                                    <h2 class="section__title">Платежный адрес</h2>
                                </div>

                                <div class="section__content">
                                    <div class="content-box">
                                        <div class="radio-wrapper content-box__row">
                                            <div class="radio__input">
                                                <input class="input-radio checkout_billing_radio"
                                                       data-backup="different_billing_address_false" checked="checked"
                                                       type="radio" value="0" name="checkout_billing_address_payment"
                                                       id="checkout_different_billing_address_false">
                                            </div>

                                            <label class="radio__label content-box__emphasis"
                                                   for="checkout_different_billing_address_false">
                                                Такой же как адрес доставки
                                            </label></div>

                                        <div class="radio-wrapper content-box__row">
                                            <div class="radio__input">
                                                <input class="input-radio checkout_billing_radio"
                                                       data-backup="different_billing_address_true"
                                                       aria-expanded="false"
                                                       aria-controls="section--billing-address__different" type="radio"
                                                       value="1" name="checkout_billing_address_payment"
                                                       id="checkout_different_billing_address_true">
                                            </div>
                                            <label class="radio__label content-box__emphasis"
                                                   for="checkout_different_billing_address_true">
                                                Используйте другой платежный адрес
                                            </label></div>

                                        <div class="checkout_billing_block radio-group__row content-box__row content-box__row--secondary hidden"
                                             id="section--billing-address__different">
                                            <div class="fieldset" data-address-fields="">


                                                <!--<input class="visually-hidden" autocomplete="billing given-name" tabindex="-1" data-autocomplete-field="first_name" size="30" type="text" name="checkout[billing_address][first_name" disabled="">

                                                <input class="visually-hidden" autocomplete="billing family-name" tabindex="-1" data-autocomplete-field="last_name" size="30" type="text" name="checkout[billing_address][last_name]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing organization" tabindex="-1" data-autocomplete-field="company" size="30" type="text" name="checkout[billing_address][company]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing address-line1" tabindex="-1" data-autocomplete-field="address1" size="30" type="text" name="checkout[billing_address][address1]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing address-line2" tabindex="-1" data-autocomplete-field="address2" size="30" type="text" name="checkout[billing_address][address2]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing address-level2" tabindex="-1" data-autocomplete-field="city" size="30" type="text" name="checkout[billing_address][city]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing country" tabindex="-1" data-autocomplete-field="country" size="30" type="text" name="checkout[billing_address][country]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing address-level1" tabindex="-1" data-autocomplete-field="province" size="30" type="text" name="checkout[billing_address][province]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing postal-code" tabindex="-1" data-autocomplete-field="zip" size="30" type="text" name="checkout[billing_address][zip]" disabled="">

                                                <input class="visually-hidden" autocomplete="billing tel" tabindex="-1" data-autocomplete-field="phone" size="30" type="text" name="checkout[billing_address][phone]" disabled="">
                                                                                                                        -->
                                                <div class="field field--optional field--half"
                                                     data-address-field="first_name">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_first_name">Имя</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_first_name') ?>"
                                                               placeholder="Имя" autocomplete="billing given-name"
                                                               data-backup="first_name"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_first_name"
                                                               id="checkout_billing_address_first_name">
                                                    </div>
                                                </div>
                                                <div class="field field--required field--half <?= $error['billing_last_name']['class'] ?>"
                                                     data-address-field="last_name">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_last_name">Фамилия</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_last_name') ?>"
                                                               placeholder="Фамилия" autocomplete="billing family-name"
                                                               data-backup="last_name"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_last_name"
                                                               id="checkout_billing_address_last_name">
                                                    </div>
                                                    <?= $error['billing_last_name']['message'] ?>
                                                </div>
                                                <div data-address-field="company" class="field field--optional">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_company">Компания</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_company') ?>"
                                                               placeholder="Компания"
                                                               autocomplete="billing organization" data-backup="company"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_company"
                                                               id="checkout_billing_address_company">
                                                    </div>
                                                </div>
                                                <div class="field field--required field--two-thirds <?= $error['billing_address']['class'] ?>"
                                                     data-address-field="address1">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_address1">Адрес</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_address') ?>"
                                                               placeholder="Адрес" autocomplete="billing address-line1"
                                                               data-backup="address1" data-google-places="name"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_address"
                                                               id="checkout_billing_address_address1">
                                                    </div>
                                                    <?= $error['billing_address']['message'] ?>
                                                </div>
                                                <div class="field field--optional field--third"
                                                     data-address-field="address2">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_address2">Квартира,
                                                            корпус и тд</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_flat') ?>"
                                                               placeholder="Квартира, корпус и тд"
                                                               autocomplete="billing address-line2"
                                                               data-backup="address2"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_flat"
                                                               id="checkout_billing_address_address2">
                                                    </div>
                                                </div>
                                                <div data-address-field="city"
                                                     class="field field--required <?= $error['billing_city']['class'] ?>">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_city">Город</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_city') ?>"
                                                               placeholder="Город" autocomplete="billing address-level2"
                                                               data-backup="city" data-google-places="locality"
                                                               class="js-checkout-address-input field__input" size="30"
                                                               type="text" name="checkout_billing_city"
                                                               id="checkout_billing_address_city">
                                                    </div>
                                                    <?= $error['billing_city']['message'] ?>
                                                </div>
                                                <div data-address-field="country"
                                                     class="field field--required field--show-floating-label field--half">

                                                    <div class="field__input-wrapper field__input-wrapper--select">
                                                        <label class="field__label"
                                                               for="checkout_billing_address_country">Страна</label>
                                                        <select size="1" autocomplete="billing country"
                                                                data-backup="country"
                                                                class="field__input js-checkout-address-select field__input--select"
                                                                name="checkout_billing_country"
                                                                id="checkout_billing_address_country">
                                                            <?php if (!empty($countries)) { ?>
                                                                <?php foreach ($countries as $country) { ?>
                                                                    <option value="<?= $country['country_handle'] ?>"><?= $country['country_name'] ?></option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div data-address-field="zip"
                                                     class="field field--required field--half <?= $error['billing_index']['class'] ?>">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_zip">Почтовый
                                                            индекс</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_index') ?>"
                                                               placeholder="Индекс" autocomplete="billing postal-code"
                                                               data-backup="zip" data-google-places="postal_code"
                                                               class="js-checkout-address-input field__input field__input--zip"
                                                               size="30" type="text" name="checkout_billing_index"
                                                               id="checkout_billing_address_zip">
                                                    </div>
                                                    <?= $error['billing_index']['message'] ?>
                                                </div>
                                                <div data-address-field="phone"
                                                     class="field field--required <?= $error['billing_phone']['class'] ?>">

                                                    <div class="field__input-wrapper"><label class="field__label"
                                                                                             for="checkout_billing_address_phone">Тел.</label>
                                                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_billing_phone') ?>"
                                                               placeholder="Тел." autocomplete="billing tel"
                                                               data-backup="phone" data-phone-formatter="true"
                                                               data-phone-formatter-country-select="[name='checkout[billing_address][country]']"
                                                               class="js-checkout-address-input field__input field__input--numeric"
                                                               size="30" type="tel" name="checkout_billing_phone"
                                                               id="checkout_billing_address_phone"
                                                               data-phone-formatter-country-code="93">
                                                    </div>
                                                    <?= $error['billing_phone']['message'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>


                        <div class="step__footer">
                            <input type="hidden" name="checkout[total_price]" id="checkout_total_price" value="1519900">
                            <input type="hidden" name="checkout_step3" value="1"/>

                            <button name="button" type="submit" class="step__footer__continue-btn btn ">
                                <span class="btn__content">Завершить заказ</span>
                                <i class="btn__spinner icon icon--button-spinner"></i>
                            </button>
                            <a class="step__footer__previous-link" href="/checkout/step2?token=<?= $token ?>">
                                <svg class="icon-svg icon-svg--color-accent icon-svg--size-10 previous-link__icon rtl-flip"
                                     role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                                    <path d="M8 1L7 0 3 4 2 5l1 1 4 4 1-1-4-4"></path>
                                </svg>
                                <span class="step__footer__previous-link-content">Вернуться к доставке</span></a>
                        </div>

                        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                        <input type="hidden" name="checkout[client_details][browser_width]" value="1519"><input
                                type="hidden" name="checkout[client_details][browser_height]" value="735"><input
                                type="hidden" name="checkout[client_details][javascript_enabled]" value="1">
                    </form>
                </div>

            </div>
            <div class="main__footer">
                <div class="modals">
                </div>


                <div role="contentinfo" aria-label="Нижний колонтитул">
                    <p class="copyright-text">
                        Все права защищены <?= \CloudStore\App\Engine\Config\Config::$config['site_name'] ?>
                    </p>
                </div>

                <div id="dialog-close-title" class="hidden">Закрыть</div>

            </div>
        </div>

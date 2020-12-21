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
                      data-checkout-payment-due-target=""><?= CloudStore\App\Engine\Components\Utils::asPrice($checkout_price) ?></span>
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
                                            <tr class="product" data-product-id="" data-variant-id=""
                                                data-product-type="<?= $cur['name'] ?>">
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
                                            <span data-price-total="<?= $checkout_price ?? '' ?>"
                                                  class="order-summary__emphasis js-shipper-price-out"
                                                  data-checkout-subtotal-price-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($checkout_price) ?>
                                            </span>
                                    </td>
                                </tr>


                                <tr class="total-line total-line--shipping">
                                    <td class="total-line__name">Доставка</td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis js-shipper-price-out-2"
                                                  data-checkout-total-shipping-target="0">
                                                —
                                            </span>
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
                                        <span class="payment-due__price js-shipper-price-out-3"
                                              data-checkout-payment-due-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($checkout_price) ?>
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
                    <li class="breadcrumb__item breadcrumb__item--current">
                        <span class="breadcrumb__text">Способ доставки</span>
                        <svg class="icon-svg icon-svg--size-10 breadcrumb__chevron-icon rtl-flip" role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                            <path d="M2 1l1-1 4 4 1 1-1 1-4 4-1-1 4-4"></path>
                        </svg>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--blank">
                        <span class="breadcrumb__text">Способ оплаты</span>
                    </li>
                </ul>

                <div data-alternative-payments="">
                </div>

            </div>
            <div class="main__content">
                <div class="step" data-step="shipping_method">
                    <form data-shipping-method-form="true" class="edit_checkout animate-floating-labels"
                          action="/checkout/step2?token=<?= CloudStore\App\Engine\Components\Utils::generateCheckoutToken() ?>"
                          accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="✓"><input
                                type="hidden" name="_method" value="patch"><input type="hidden"
                                                                                  name="authenticity_token"
                                                                                  value="9/Y+gl57Ya5e2Fsodqk5unFzswkR8yWypn8DUMme02eJUuIeSHuMnSnvzK22Y86BAg0UzZTVLi7Z/lbuxykk2w==">

                        <input type="hidden" name="previous_step" id="previous_step" value="shipping_method">
                        <input type="hidden" name="step" value="payment_method">

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

                                    </div>
                                </div>
                            </div>


                            <div class="section section--shipping-method">
                                <div class="section__header">
                                    <h2 class="section__title">Способ доставки</h2>

                                </div>

                                <div class="section__content">


                                    <div class="content-box" data-shipping-methods="">
                                        <?php if ($shipping['status']) { ?>
                                            <?php foreach ($shipping['shippers'] as $shipper) { ?>
                                                <div class="content-box__row">
                                                    <div class="radio-wrapper"
                                                         data-shipping-method="<?= $shipper['shipper_id'] ?>">
                                                        <div class="radio__input">
                                                            <input id="input-<?= $shipper['shipper_id'] ?>"
                                                                   class="js-shipping-price input-radio"
                                                                   data-checkout-total-shipping="<?= $shipper['full_cost'] ?>"
                                                                   data-checkout-total-shipping-cents="0" type="radio"
                                                                   value="<?= $shipper['shipper_id'] ?>"
                                                                   name="checkout_ship_id">
                                                        </div>
                                                        <label class="radio__label"
                                                               for="input-<?= $shipper['shipper_id'] ?>">
                                                                                <span class="radio__label__primary"
                                                                                      data-shipping-method-label-title="<?= $shipper['shipper_type'] ?>">
                                                                                    <?= $shipper['shipper_type'] ?>
                                                                                </span>
                                                            <span class="radio__label__accessory">
                                                                                    <span class="content-box__emphasis">
                                                                                        <?= CloudStore\App\Engine\Components\Utils::asPrice($shipper['full_cost']) ?>
                                                                                    </span>
                                                                                </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>

                                            <div class="notice"><?= $shipping['cause'] ?></div>

                                        <?php } ?>

                                    </div>
                                </div>
                            </div>


                            <div class="step__footer">


                                <?php if ($shipping['status']): ?>

                                    <button name="button_checkout_step1" type="submit"
                                            class="step__footer__continue-btn btn ">
                                        <span class="btn__content">Продолжить с оформлением оплаты</span>
                                        <i class="btn__spinner icon icon--button-spinner"></i>
                                    </button>

                                <?php endif; ?>

                                <a class="step__footer__previous-link" href="/checkout/step1?token=<?= $token ?>">
                                    <svg class="icon-svg icon-svg--color-accent icon-svg--size-10 previous-link__icon rtl-flip"
                                         role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                                        <path d="M8 1L7 0 3 4 2 5l1 1 4 4 1-1-4-4"></path>
                                    </svg>
                                    <span class="step__footer__previous-link-content">Вернуться к контактной информации</span></a>
                            </div>

                            <input type="hidden" name="checkout_step2" value="1"/>
                            <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>

                            <input type="hidden" name="checkout[client_details][browser_width]" value="1536"><input
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
    </div>
</div>

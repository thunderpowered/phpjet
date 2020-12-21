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
                      data-checkout-payment-due-target=""><?= CloudStore\App\Engine\Components\Utils::asPrice($info['orders_price']) ?></span>
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
                                                    <span class="product__description__name order-summary__emphasis"><?= $cur['title'] ?><?php echo !empty($cur["modification"]) ? "[" . $cur["modification"] . "]" : "" ?></span>
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
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($final) ?>
                                            </span>
                                    </td>
                                </tr>


                                <tr class="total-line total-line--shipping">
                                    <td class="total-line__name">Доставка</td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis"
                                                  data-checkout-total-shipping-target="0">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($info['orders_shipping_price']) ?>
                                            </span>
                                    </td>
                                </tr>

                                <?php /* if($info['orders_charge_price'] != 0) { ?>
                                      <tr class="total-line total-line--shipping">
                                      <td class="total-line__name">Сервисный сбор</td>
                                      <td class="total-line__price">
                                      <span class="order-summary__emphasis" data-checkout-total-shipping-target="0">
                                      //<?= ShopEngine::Help()->as_price($info['orders_charge_price']) ?>
                                      </span>
                                      </td>
                                      </tr>
                                      <?php } */ ?>

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
                                        <span class="payment-due__price" data-checkout-payment-due-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($info['orders_price']) ?>
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

                <div data-alternative-payments="">
                </div>

            </div>
            <div class="main__content">
                <div class="section">
                    <div class="section__header os-header">
                        <svg width="50" height="50" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000"
                             stroke-width="2"
                             class="os-header__hanging-icon os-header__hanging-icon--animate checkmark">
                            <path class="checkmark__circle"
                                  d="M25 49c13.255 0 24-10.745 24-24S38.255 1 25 1 1 11.745 1 25s10.745 24 24 24z"></path>
                            <path class="checkmark__check" d="M15 24.51l7.307 7.308L35.125 19"></path>
                        </svg>

                        <div class="os-header__heading">
                            <span class="os-order-number">
                                Заказ #<?= $info['orders_id'] ?>
                            </span>
                            <h2 class="os-header__title">
                                Спасибо<?php echo $info['orders_name'] !== "" ? ' , ' . $info['orders_name'] : '' ?>!
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="thank-you__additional-content">
                    <script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
                    <style>
                        div.mainPayment p + p {
                            margin-top: 0px;
                        }

                        div.mainPayment {
                            width: 110%;
                            margin-top: 40px;
                            margin-left: -55px;
                        }

                        div.blockPayment:after {
                            content: '';
                            display: block;
                            clear: both;
                            margin-bottom: 50px;
                        }

                        div.blockPayment div {
                            float: left;
                            display: inline-block;
                            width: 480px;
                        }

                        div.blockPayment div.imgPaument {
                            width: 110px;
                            padding-top: 6px;
                            margin-right: 30px;
                            text-align: right;
                        }

                        @media (max-width: 767px) {
                            div.blockPayment div.imgPaument {
                                margin-bottom: 30px;
                            }
                        }

                        div.blockPayment div.textPayment {
                            font-family: 'Arial';
                            font-weight: 400;
                            font-style: normal;
                            font-size: 18px;
                            line-height: 26px;
                        }

                        button, .button {
                            background-color: rgb(0, 120, 255);
                            border-radius: 5px;
                            border-width: 0px;
                            border-color: rgb(0, 0, 0);
                            font-family: Arial;
                            font-weight: 400;
                            font-style: normal;
                            color: rgb(255, 255, 255);
                            font-size: 16px;
                            letter-spacing: 0px;
                            min-width: 168px;
                            height: 39px;
                            display: inline-block;
                            line-height: 39px;
                            text-align: center;
                            text-decoration: none;
                            transition: 1s;
                            cursor: pointer;
                            margin-top: 15px;
                            padding: 0 20px;
                        }

                        button:hover, .button:hover {
                            background-color: rgba(0, 120, 255, .6);
                        }

                        div.mainPayment p {
                            color: #333333;
                            margin: 0;
                            padding-bottom: 1px;
                            line-height: 18px;
                            font-size: 14px;
                            padding-top: 0px;
                        }

                        div.mainPayment p.h1 {
                            font-family: Arial;
                            font-size: 18px;
                        }
                    </style>

                    <div class="mainPayment">
                        <div class="order_block">

                        </div>
                        <div class="blockPayment getOrderUr">
                            <div class="textPayment">
                                <p class="h1">Скачать счет</p>
                                <p><?= $info['payment_instructions'] ?></p>
                                <a class="button" target="_blank"
                                   href="<?= CloudStore\App\Engine\Core\Router::getHost() . '/checkout/download?orderid=' . $info['orders_key'] ?>">Скачать
                                    счет</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__content">
                        <div class="content-box">
                            <div class="content-box__row content-box__row--no-padding">

                                <div class="content-box__row">
                                    <h2 class="os-step__title">Ваш заказ подтвержден</h2>
                                    <div class="os-step__special-description">
                                        <p>Квитанция на оплату будет направлена на Вашу электронную почту.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="content-box">
                                <div class="content-box__row">
                                    <h2 class="os-step__title">Подтверждение заказа</h2>
                                    <p class="os-step__description">
                                        Подтверждение отправлено на <span
                                                class="emphasis"><?= $info['orders_email'] ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="content-box">
                                <div class="content-box__row content-box__row--no-border">
                                    <h2>Информация пользователя</h2>
                                </div>
                                <div class="content-box__row">
                                    <div class="section__content">
                                        <div class="section__content__column section__content__column--half">
                                            <h3>Адрес доставки</h3>
                                            <p><?= $info['orders_name'] ?> <?= $info['orders_last_name'] ?>
                                                <br><?= $info['orders_address'] ?><br><?= $info['orders_city'] ?><br>
                                                <!--<?php /* echo $info['orders_region'] */ ?><br>--><?= $info['orders_index'] ?>
                                                <br><?= $info['orders_country'] ?><br><?= $info['orders_phone'] ?></p>
                                            <h3>Способ доставки</h3>
                                            <p><?= $info['orders_shipping'] ?></p>
                                        </div>
                                        <div class="section__content__column section__content__column--half">
                                            <h3>Платежный адрес</h3>
                                            <?php if ($info['orders_billing_status'] === '0') { ?>
                                                <p><?= $info['orders_name'] ?> <?= $info['orders_last_name'] ?>
                                                    <br><?= $info['orders_address'] ?><br><?= $info['orders_city'] ?>
                                                    <br>
                                                    <!--<?php /* echo $info['orders_region'] */ ?><br>--><?= $info['orders_index'] ?>
                                                    <br><?= $info['orders_country'] ?><br><?= $info['orders_phone'] ?>
                                                </p>
                                            <?php } else { ?>
                                                <p><?= $info['orders_billing_name'] ?> <?= $info['orders_billing_last_name'] ?>
                                                    <br><?= $info['orders_billing_address'] ?>
                                                    <br><?= $info['orders_billing_city'] ?><br>
                                                    <!--<?php /* echo $info['orders_region'] */ ?><br>--><?= $info['orders_billing_index'] ?>
                                                    <br><?= $info['orders_billing_country'] ?>
                                                    <br><?= $info['orders_billing_phone'] ?></p>
                                            <?php } ?>
                                            <h3>Способ оплаты
                                                <ul class="payment-method-list">
                                                    <li class="payment-method-list__item">
                                                        <span class="payment-method-list__item__info"><?= $info['payment_name'] ?></span>
                                                        <span class="payment-method-list__item__amount emphasis"><?= CloudStore\App\Engine\Components\Utils::asPrice($info['orders_price']) ?></span>
                                                    </li>


                                                </ul>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="step__footer">
                        <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>" class="step__footer__continue-btn btn">
                            <span class="btn__content">Продолжить покупку</span>
                            <i class="btn__spinner icon icon--button-spinner"></i>
                        </a>
                        <p class="step__footer__info">
                            <i class="icon icon--os-question"></i>
                            <span>
                                                                                                                                    Нужна помощь? <a
                                        href="mailto:<?= \CloudStore\App\Engine\Config\Config::$config['admin_email'] ?>">Свяжитесь с нами</a>
                                                                                                                                </span>
                        </p>
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

            <!-- trash/final.php -->


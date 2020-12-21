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
                      data-checkout-payment-due-target=""><?= CloudStore\App\Engine\Components\Utils::asPrice($order_price) ?></span>
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
                                    <?php if ($order_products) { ?>
                                        <?php foreach ($order_products as $cur) { ?>
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
                                            <span class="order-summary__emphasis"
                                                  data-checkout-subtotal-price-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($order_price) ?>
                                            </span>
                                    </td>
                                </tr>


                                <tr class="total-line total-line--shipping">
                                    <td class="total-line__name">Доставка</td>
                                    <td class="total-line__price">
                                            <span class="order-summary__emphasis"
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
                                        <span class="payment-due__price" data-checkout-payment-due-target="">
                                                <?= CloudStore\App\Engine\Components\Utils::asPrice($order_price) ?>
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

                    <li class="breadcrumb__item breadcrumb__item--current">
                        <span class="breadcrumb__text">Информация о покупателе</span>
                        <svg class="icon-svg icon-svg--size-10 breadcrumb__chevron-icon rtl-flip" role="img"
                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                            <path d="M2 1l1-1 4 4 1 1-1 1-4 4-1-1 4-4"></path>
                        </svg>
                    </li>
                    <li class="breadcrumb__item breadcrumb__item--blank">
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
                <div class="step" data-step="contact_information">
                    <form novalidate="novalidate" class="edit_checkout animate-floating-labels"
                          data-customer-information-form="true"
                          action="/checkout/step1?token=<?= CloudStore\App\Engine\Components\Utils::generateCheckoutToken() ?>"
                          accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="✓"><input
                                type="hidden" name="_method" value="patch"><input type="hidden"
                                                                                  name="authenticity_token"
                                                                                  value="ajGGyT0LRhBkF3U66a4lEXvyQ/k9E4Y3Rmbi9TSCYgcqdb09q/IXqEpRoOO5ucOPA4tJ1OONlYcUNZYP29VoJg==">

                        <input type="hidden" name="previous_step" id="previous_step" value="contact_information">
                        <input type="hidden" name="step" value="shipping_method">

                        <div class="step__sections">

                            <div class="section section--contact-information">
                                <div class="section__header">
                                    <div class="layout-flex layout-flex--tight-vertical layout-flex--loose-horizontal layout-flex--wrap">
                                        <h2 class="section__title layout-flex__item layout-flex__item--stretch">
                                            Информация о покупателе</h2>
                                        <?php if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) { ?>
                                            <p class="layout-flex__item">
                                                <a href="/user/account" id="customer_logout_link">Редактировать
                                                    профиль</a>
                                            </p>
                                        <?php } ?>
                                        <p style="margin-top:0 !important" class="layout-flex__item">
                                            <?php if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) { ?>
                                                <a href="/user/logout" id="customer_logout_link">Выйти</a>
                                            <?php } else { ?>
                                                <a href="/user/login" id="customer_logout_link">Войти</a>
                                            <?php } ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="section__content">
                                    <div class="fieldset">
                                        <div class="field field--required <?= $error['email']['class'] ?? '' ?>">

                                            <div class="field__input-wrapper field__input-wrapper--with-loader"><label
                                                        class="field__label" for="checkout_email">Email</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('user_is_logged') ? \CloudStore\App\Engine\Components\Request::getSession('user_email') : \CloudStore\App\Engine\Components\Request::getSession('checkout_email') ?>"
                                                       placeholder="Email" autocapitalize="off" spellcheck="false"
                                                       autocomplete="shipping email" data-autofocus="true"
                                                       data-email-input="true" data-backup="customer_email"
                                                       class="field__input" size="30" type="email" name="checkout_email"
                                                       id="checkout_email">
                                            </div>
                                            <?= $error['email']['message'] ?? '' ?>
                                        </div>
                                    </div>

                                    <!--        <div class="section section--half-spacing-top section--optional section--fade-in" data-buyer-accepts-marketing="">
                                              <div class="section__content">
                                                <div class="checkbox-wrapper">
                                                  <div class="checkbox__input">
                                                    <input name="checkout[buyer_accepts_marketing]" type="hidden" value="0"><input class="input-checkbox" data-backup="buyer_accepts_marketing" type="checkbox" value="1" checked="checked" name="checkout[buyer_accepts_marketing]" id="checkout_buyer_accepts_marketing">
                                                  </div>
                                                  <label class="checkbox__label" for="checkout_buyer_accepts_marketing">
                                                    Подписывайтесь на нашу новостную рассылку
                                    </label>            </div>
                                              </div>
                                            </div>-->
                                </div>
                            </div>


                            <div class="section section--shipping-address" data-shipping-address=""
                                 data-update-order-summary="">

                                <div class="section__header">
                                    <h2 class="section__title">
                                        Адрес доставки
                                    </h2>
                                </div>

                            </div>

                            <div class="section__content">
                                <div class="section section--shipping-method">

                                    <div class="fieldset" data-address-fields="">


                                        <!--<input class="visually-hidden" autocomplete="shipping given-name" tabindex="-1" data-autocomplete-field="first_name" size="30" type="text" name="checkout[shipping_address][first_name]">

                                        <input class="visually-hidden" autocomplete="shipping family-name" tabindex="-1" data-autocomplete-field="last_name" size="30" type="text" name="checkout[shipping_address][last_name]">

                                        <input class="visually-hidden" autocomplete="shipping organization" tabindex="-1" data-autocomplete-field="company" size="30" type="text" name="checkout[shipping_address][company]">

                                        <input class="visually-hidden" autocomplete="shipping address-line1" tabindex="-1" data-autocomplete-field="address1" size="30" type="text" name="checkout[shipping_address][address1]">

                                        <input class="visually-hidden" autocomplete="shipping address-line2" tabindex="-1" data-autocomplete-field="address2" size="30" type="text" name="checkout[shipping_address][address2]">

                                        <input class="visually-hidden" autocomplete="shipping address-level2" tabindex="-1" data-autocomplete-field="city" size="30" type="text" name="checkout[shipping_address][city]">

                                        <input class="visually-hidden" autocomplete="shipping country" tabindex="-1" data-autocomplete-field="country" size="30" type="text" name="checkout[shipping_address][country]">

                                        <input class="visually-hidden" autocomplete="shipping address-level1" tabindex="-1" data-autocomplete-field="province" size="30" type="text" name="checkout[shipping_address][province]">

                                        <input class="visually-hidden" autocomplete="shipping postal-code" tabindex="-1" data-autocomplete-field="zip" size="30" type="text" name="checkout[shipping_address][zip]">

                                        <input class="visually-hidden" autocomplete="shipping tel" tabindex="-1" data-autocomplete-field="phone" size="30" type="text" name="checkout[shipping_address][phone]">
                                                                                                    -->

                                        <?php if (\CloudStore\App\Engine\Components\Request::getSession('user_is_logged')) { ?>

                                            <div class="field field--required field--show-floating-label">
                                                <div class="field__input-wrapper field__input-wrapper--select"><label
                                                            class="field__label" for="checkout_shipping_address_id">Адрес
                                                        магазина</label>
                                                    <select data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"
                                                            class="field__input field__input--select"
                                                            name="checkout_address_id"
                                                            id="checkout_shipping_address_id">
                                                        <option value="0">Новый адрес</option>
                                                        <?php if ($addresses) { ?>
                                                            <?php foreach ($addresses as $cur) { ?>
                                                                <option data-properties=""
                                                                        value="<?= $cur['address_id'] ?>"><?= $cur['address'] ?>
                                                                    , <?= $cur['address_city'] ?>
                                                                    , <?= $cur['address_index'] ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                        <?php } ?>

                                        <div class="field field--optional field--half <?= $error['name']['class'] ?? '' ?>"
                                             data-address-field="first_name">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_first_name">Имя</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_name') ?>"
                                                       placeholder="Имя" autocomplete="shipping given-name"
                                                       data-backup="first_name" class="field__input" size="30"
                                                       type="text" name="checkout_name"
                                                       id="checkout_shipping_address_first_name">
                                            </div>
                                            <?= $error['name']['message'] ?? '' ?>
                                        </div>
                                        <div class="field field--required field--half <?= $error['last_name']['class'] ?? '' ?>"
                                             data-address-field="last_name">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_last_name">Фамилия</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_last_name') ?>"
                                                       placeholder="Фамилия" autocomplete="shipping family-name"
                                                       data-backup="last_name" class="field__input" size="30"
                                                       type="text" name="checkout_last_name"
                                                       id="checkout_shipping_address_last_name">
                                            </div>
                                            <?= $error['last_name']['message'] ?? '' ?>
                                        </div>
                                        <div data-address-field="company" class="field field--optional">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_company">Компания</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_company') ?>"
                                                       placeholder="Компания" autocomplete="shipping organization"
                                                       data-backup="company" class="field__input" size="30" type="text"
                                                       name="checkout_company" id="checkout_shipping_address_company">
                                            </div>
                                        </div>
                                        <div class="field field--required field--two-thirds <?= $error['address']['class'] ?? '' ?>"
                                             data-address-field="address1">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_address1">Адрес</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_address') ?>"
                                                       placeholder="Адрес" autocomplete="shipping address-line1"
                                                       data-backup="address1" data-google-places="name"
                                                       class="field__input" size="30" type="text"
                                                       name="checkout_address" id="checkout_shipping_address_address1">
                                            </div>
                                            <?= $error['address']['message'] ?? '' ?>
                                        </div>
                                        <div class="field field--optional field--third" data-address-field="address2">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_address2">Квартира,
                                                    корпус и тд</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_flat') ?>"
                                                       placeholder="Квартира, корпус и тд"
                                                       autocomplete="shipping address-line2" data-backup="address2"
                                                       class="field__input" size="30" type="text" name="checkout_flat"
                                                       id="checkout_shipping_address_address2">
                                            </div>
                                        </div>
                                        <div data-address-field="city"
                                             class="field field--required <?= $error['city']['class'] ?? '' ?>">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_city">Город</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_city') ?>"
                                                       placeholder="Город" autocomplete="shipping address-level2"
                                                       data-backup="city" data-google-places="locality"
                                                       class="field__input" size="30" type="text" name="checkout_city"
                                                       id="checkout_shipping_address_city">
                                            </div>
                                            <?= $error['city']['message'] ?? '' ?>
                                        </div>
                                        <div data-address-field="country"
                                             class="field field--required field--show-floating-label field--three-eights">

                                            <div class="field__input-wrapper field__input-wrapper--select"><label
                                                        class="field__label" for="checkout_shipping_address_country">Страна</label>
                                                <select data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" data-id=""
                                                        size="1" autocomplete="shipping country" data-backup="country"
                                                        class="field__input field__input--select"
                                                        name="checkout_country" id="checkout_shipping_address_country">
                                                    <option class="disabled_label" disabled selected value="">Страна
                                                    </option>
                                                    <?php if (isset($countries) AND $countries) { ?>
                                                        <?php foreach ($countries as $country) { ?>
                                                            <option <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_country') === $country['country_handle'] ? 'selected' : '' ?>
                                                                    class="select_country"
                                                                    value="<?= $country['country_handle'] ?>"><?= $country['country_name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div data-address-field="province"
                                             class="field field--required field--three-eights field--show-floating-label <?= $error['region']['class'] ?? '' ?>">

                                            <div class="field__input-wrapper field__input-wrapper--select"><label
                                                        class="field__label" for="checkout_shipping_address_province">Регион</label>
                                                <select <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_country') ? '' : 'disabled' ?>
                                                        placeholder="Регион" autocomplete="shipping address-level1"
                                                        data-backup="province"
                                                        data-google-places="administrative_area_level_1"
                                                        class="field__input field__input--select" name="checkout_region"
                                                        id="checkout_shipping_address_province">
                                                    <option selected value="" disabled="">Регион</option>
                                                    <?php if (isset($regions) AND $regions) { ?>
                                                        <?php foreach ($regions as $region) { ?>
                                                            <option <?= \CloudStore\App\Engine\Components\Request::getSession('checkout_region') === $region['region_handle'] ? 'selected' : '' ?>
                                                                    value="<?= $region['region_handle'] ?>"><?= $region['region_name'] ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?= $error['region']['message'] ?? '' ?>
                                        </div>
                                        <div data-address-field="zip"
                                             class="field field--required field--quarter <?= $error['index']['class'] ?? '' ?>">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_zip">Почтовый
                                                    индекс</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_index') ?>"
                                                       placeholder="Индекс" autocomplete="shipping postal-code"
                                                       data-backup="zip" data-google-places="postal_code"
                                                       class="field__input field__input--zip" size="30" type="text"
                                                       name="checkout_index" id="checkout_shipping_address_zip">
                                            </div>
                                            <?= $error['index']['message'] ?? '' ?>
                                        </div>
                                        <div data-address-field="phone"
                                             class="field field--required <?= $error['phone']['class'] ?? '' ?>">

                                            <div class="field__input-wrapper"><label class="field__label"
                                                                                     for="checkout_shipping_address_phone">Тел.</label>
                                                <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('checkout_phone') ?>"
                                                       placeholder="Тел." autocomplete="shipping tel"
                                                       data-backup="phone" data-phone-formatter="true"
                                                       data-phone-formatter-country-select="[name='checkout[shipping_address][country]']"
                                                       class="field__input field__input--numeric" size="30" type="tel"
                                                       name="checkout_phone" id="checkout_shipping_address_phone"
                                                       data-phone-formatter-country-code="7">
                                            </div>
                                            <?= $error['phone']['message'] ?? '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="section section--half-spacing-top section--optional">
                                <div class="section__content">
                                    <div class="checkbox-wrapper">
                                    </div>
                                </div>
                            </div>


                        </div>


                        <div class="step__footer">

                            <button name="button_checkout_step1" type="submit" class="step__footer__continue-btn btn ">
                                <span class="btn__content">Продолжить с оформлением доставки</span>
                                <i class="btn__spinner icon icon--button-spinner"></i>
                            </button>
                            <a class="step__footer__previous-link" href="/cart">
                                <svg class="icon-svg icon-svg--color-accent icon-svg--size-10 previous-link__icon rtl-flip"
                                     role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">
                                    <path d="M8 1L7 0 3 4 2 5l1 1 4 4 1-1-4-4"></path>
                                </svg>
                                <span class="step__footer__previous-link-content">Вернуться к корзине</span></a>
                        </div>

                        <input type="hidden" name="checkout_step1" value="1"/>
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
    </div>
</div>

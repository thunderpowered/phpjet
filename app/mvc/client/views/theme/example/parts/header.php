<div id="my-header">
    <header class="header" id="header">
        <div class="header__upper">
            <div class="container">
                <div class="row header__row--correct">
                    <div class="d-md-none col-8">
                        <div class="header__upper-logo-wrapper">
                            <a href="/" class="header__home header__upper-home">
                                <span class="header__upper-title p-hidden"><?= \CloudStore\App\Engine\Config\Config::$config['site_name'] ?></span>
                                <div style="background-image: url(<?= $this->widget->widgetInfo->getLogotype() ?>)" class="header__upper-logo o-logo"></div>
                            </a>
                        </div>
                    </div>
                    <div class="d-none">
                        <nav id="main_menu" class="nav">
                            <ul class="nav__list">
                                <li class="mm-devider">Магазин</li>
                                <li class="nav__item">
                                    <a href="/cart" class="nav__link">Корзина (<span class="js-cart-sum"><?= $this->widget->widgetCart->getFullCartValue() ?></span>)</a>
                                </li>
                                <li class="nav__item">
                                    <a href="/" class="nav__link">Избранное</a>
                                </li>
                                <li class="mm-devider">Аккаунт</li>
                                <li class="nav__item">
                                    <a href="/user/login" class="nav__link">Вход</a>
                                </li>
                                <li class="nav__item">
                                    <a href="/user/signup" class="nav__link">Регистрация</a>
                                </li>
                                <li class="mm-devider">Разделы</li>
                                <li class="nav__item">
                                    <a href="/" class="nav__link">Ссылка #1</a>
                                </li>
                                <li class="nav__item">
                                    <a href="/" class="nav__link">Ссылка #2</a>
                                </li>
                                <li class="nav__item">
                                    <a href="/" class="nav__link">Ссылка #3</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-md-10 col-lg-7 d-md-block d-none">
                        <div id="main_menu-wrapper">
                            <nav class="nav">
                                <ul class="nav__list d-flex">
                                    <li class="nav__item"><a class="nav__link" href="/pages/kak-oformit-zakaz">Как оформить заказ</a></li>
                                    <li class="nav__item"><a class="nav__link" href="/pages/delivery">Доставка</a></li>
                                    <li class="nav__item"><a class="nav__link" href="/pages/pickup">Самовывоз</a></li>
                                    <li class="nav__item"><a class="nav__link" href="/pages/payment">Оплата</a></li>
                                    <li class="nav__item"><a class="nav__link" href="/pages/contact">Контакты</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block p-text-align--right">
                        <ul class="header__contact o-flex-container">
                            <li class="header__contact-item"><a class="header__contact-link header__contact-phone" href="tel:+74957406609">+7 (XXX) XXX XX XX</a></li>
                            <li class="header__contact-item"><a class="header__contact-link" href="mailto:<?= \CloudStore\App\Engine\Config\Config::$config['admin_email'] ?>"><?= \CloudStore\App\Engine\Config\Config::$config['admin_email'] ?></a></li>
                        </ul>
                    </div>
                    <div class="col-4 col-md-2 d-lg-none">
                        <div class="header__hamburger-wrapper p-text-align--right">
                            <a href="#main_menu" class="header__overhead-menu-link hamburger hamburger--3dxy">
								<span class="hamburger-box">
		   							<span class="hamburger-inner"></span>
		  						</span>
                            </a>
                        </div>
                    </div>
                    <div class="p-clearfix">
                    </div>
                </div>
            </div>
        </div>
        <div class="header__center d-none d-md-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-12">
                        <div class="header__center-wrapper o-wrapper o-flex-container">
                            <a href="/" class="header__home">
                                <span class="header__logo-title p-hidden"><?= CloudStore\App\Engine\Config\Config::$config['site_name'] ?></span>
                                <div style="background-image: url(<?= $this->widget->widgetInfo->getLogotype() ?>)" class="header__logo o-logo"></div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-12">
                        <div class="header__center-wrapper o-wrapper">
                            <div class="header__info">
                                <span class="header__info--bold p-font--bold">
                                    <a href="/" class="header__info-link p-color--light-blue">Ссылка #1</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="header__center-wrapper o-wrapper">
                            <div class="header__info header__info--center">
                                <span class="header__info--bold p-font--bold">
                                    <a href="/" class="header__info-link p-color--purple">Ссылка #2</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-12">
                        <div class="header__center-wrapper o-wrapper">
                            <div class="header__info">
                                <span class="header__info--bold p-font--bold">
                                    <a href="/" class="header__info-link p-color--light-blue">Ссылка #3</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="StickNavWrapper" class="header__bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 d-lg-block d-none">
                        <div class="o-wrapper--regular header__bottom-wrapper js-menu-toggle">
                            <a class="header__catalog-link o-button p-width--max p-padding--half o-flex-container" href="javascript:void(0);">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" <!--xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"--> viewBox="2 6 18 11" version="1.1" x="0px" y="0px" preserveAspectRatio="xMidYMid meet" fill="rgb(255,255,255)" fill-opacity="1"><title>editor_list_view_hambuger_menu_outline_stroke</title><description>Created with Sketch.</description><defs><path d="M2,6.5 C2,6.77614238 2.26239648,7 2.57713839,7 L19.4228616,7 C19.7416064,7 20,6.77806759 20,6.5 C20,6.22385762 19.7376035,6 19.4228616,6 L2.57713839,6 C2.25839366,6 2,6.22193241 2,6.5 Z M2,11.5 C2,11.7761424 2.26239648,12 2.57713839,12 L19.4228616,12 C19.7416064,12 20,11.7780676 20,11.5 C20,11.2238576 19.7376035,11 19.4228616,11 L2.57713839,11 C2.25839366,11 2,11.2219324 2,11.5 Z M2,16.5 C2,16.7761424 2.26239648,17 2.57713839,17 L19.4228616,17 C19.7416064,17 20,16.7780676 20,16.5 C20,16.2238576 19.7376035,16 19.4228616,16 L2.57713839,16 C2.25839366,16 2,16.2219324 2,16.5 Z" id="a"></path></defs><g stroke="none" stroke-width="1" fill-rule="evenodd" sketch:type="MSPage"><g><use fill-rule="evenodd" sketch:type="MSShapeGroup" xlink:href="#a"></use><use xlink:href="#a"></use></g></g></svg>
                                <span class="header__catalog-label">Каталог товаров</span>
                                <div class="header__catalog-arrow o-angle"><i class="fa fa-angle-down"></i></div>
                            </a>

                            <?php if (!\CloudStore\CloudStore::$app->router->isHome()): ?>
                            <div class="site_nav__item-block">
                                <ul class="main__main_menu">

                                    <?php $this->widget->widgetMenu->getMenuTree(); ?>

                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6 col-12">
                        <div id="Search">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 d-none d-md-block">
                        <div class="header__bottom-wrapper o-wrapper--regular p-text-align--right">
                            <ul class="header__user-list o-flex-container">
                                <li class="header__user-item"><a href="/products/favorite" class="header__user-icon ico-love"></a></li>
                                <li class="header__user-item"><a href="/user" class="header__user-icon ico-user"></a></li>
                                <li class="header__user-item"><a href="/cart" class="header__user-icon ico-cart2"></a></li>
                                <li class="header__user-item"><a href="/cart" class="header__user-link"><span class="d-none js-cart-amount"></span><span class="js-cart-sum"><?= $this->widget->widgetCart->getFullCartValue() ?></span></a><span class="header__user-arrow o-angle"><i class="fa fa-angle-down"></i></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- OLD -->
            <div id="NotificationSuccess" class="notification notification--success" aria-hidden="true">
                <div class="page-width notification__inner notification__inner--has-link">
                    <a href="/cart" class="notification__link">
                        <span class="notification__message">Товар добавлен в корзину. <span> Посмотреть корзину и оформить </span>.</span>
                    </a>
                    <button type="button" class="text-link notification__close">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32"
                             class="icon icon-close">
                            <path fill="#444"
                                  d="M25.313 8.55L23.45 6.688 16 14.138l-7.45-7.45L6.69 8.55 14.14 16l-7.45 7.45 1.86 1.862 7.45-7.45 7.45 7.45 1.863-1.862-7.45-7.45z"></path>
                        </svg>
                        <span class="icon__fallback-text">Закрыть</span>
                    </button>
                </div>
            </div>
            <!-- END OLD -->
        </div>
    </header>
</div>
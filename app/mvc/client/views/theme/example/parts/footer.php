<div id="my-footer">
    <footer class="footer" id="footer">
        <div class="footer__center o-wrapper--double">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="footer__logo-wrapper">
                            <a href="/" class="footer__home">
                                <div style="background-image: url(<?= $this->widget->widgetInfo->getLogotype() ?>)" class="footer__logo o-logo"></div>
                            </a>
                        </div>
                        <div class="footer__info o-wrapper--half">
                            {INFORMATION}
                        </div>
                        <div class="footer__info o-wrapper--half">
                            {ADDRESS}
                        </div>
                        <div class="footer__info o-wrapper--half">
                            {NUMBER}
                        </div>
                        <div class="footer__info o-wrapper--half">
                            <?= \CloudStore\App\Engine\Config\Config::$config['admin_email']?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span class="footer__menu-title">Информация</span>
                        <ul class="footer__menu-list o-list--vertical">

                            <?php $pages = $this->widget->widgetPages->getPages(); ?>

                            <?php if ($pages): ?>

                                <?php for ($i = 0; $i < (count($pages) > 4 ? 5 : count($pages)); $i++): ?>

                                    <li class="footer__menu-item">
                                        <a href="<?= \CloudStore\CloudStore::$app->router->getHost() ?>/pages/<?= $pages[$i]['pages_handle'] ?>" class="footer__menu-link"><?= $pages[$i]['pages_title'] ?></a>
                                    </li>

                                <?php endfor; ?>

                            <?php endif; ?>

                        </ul>
                    </div>
                    <div class="col-md-4">
                        <span class="footer__menu-title">Другое</span>
                        <ul class="footer__menu-list o-list--vertical">
                            <?php if ($pages AND count($pages) > 5): ?>

                                <?php for ($i = 5; $i < (count($pages) > 9 ? 10 : count($pages)); $i++): ?>
                                    <li class="footer__menu-item">
                                        <a href="<?= \CloudStore\CloudStore::$app->router->getHost() ?>/pages/<?= $pages[$i]['pages_handle'] ?>" class="footer__menu-link"><?= $pages[$i]['pages_title'] ?></a>
                                    </li>
                                <?php endfor; ?>

                            <?php endif; ?>

<!--                            <li class="footer__menu-item">-->
<!--                                <a href="/sitemap" class="footer__menu-link">Карта сайта</a>-->
<!--                            </li>-->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 order-md-1 col-12">
                        <div class="footer__social-wrapper o-wrapper">
                            <ul class="footer__social-list o-flex-container">
                                <li class="footer__social-item">
                                    <a href="/" class="footer__social-link">
                                        <div class="ico ico-twitter"></div>
                                    </a>
                                </li>
                                <li class="footer__social-item">
                                    <a href="/" class="footer__social-link">
                                        <div class="ico ico-facebook"></div>
                                    </a>
                                </li>
                                <li class="footer__social-item">
                                    <a href="/" class="footer__social-link">
                                        <div class="ico ico-instagram"></div>
                                    </a>
                                </li>
                                <li class="footer__social-item">
                                    <a href="/" class="footer__social-link">
                                        <div class="ico ico-video"></div>
                                    </a>
                                </li>
                                <li class="footer__social-item">
                                    <a href="/" class="footer__social-link">
                                        <div class="ico ico-cart"></div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 order-md-0 col-12">
                        <div class="o-wrapper footer__copyright">
                            © 2009 - <?= date('Y') ?>    Вcе права защищены <a href="/sitemap" class="footer__menu-link sitemap-link">Карта сайта</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
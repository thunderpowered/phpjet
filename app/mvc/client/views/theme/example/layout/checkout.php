<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ru" dir="ltr"
      class="no-js multi-step windows chrome desktop page--no-banner page--logo-main page--show  card-fields">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, height=device-height, minimum-scale=1.0, user-scalable=0">

    <title><?= $this->controller->title ?></title>


    <!--[if lt IE 9]>
        <link rel="stylesheet" media="all" href="/style/assets/checkout_old.css" />
    <![endif]-->

    <!--[if gte IE 9]><!-->
    <link rel="stylesheet" media="all" href="<?php echo THEME_STATIC_URL; ?>style/assets/checkout.css"/>
    <link rel="stylesheet" media="all" href="<?php echo THEME_STATIC_URL; ?>style/assets/input.css"/>
    <link href="<?php echo THEME_STATIC_URL; ?>style/_full_/custom.css" rel="stylesheet" type="text/css" media="all">

    <!--<![endif]-->

    <meta name="shopify-checkout-authorization-token" content="6502329019daea27c38185c276d900aa"/>

    <!-- ANALYTICS -->
    <?php $this->widget->widgetSEO->getAnalytics(); ?>
    <!-- END ANALYTICS -->


</head>
<body>
<div class="banner" data-header>
    <div class="wrap">

        <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>" class="logo logo--left">
            <h1 class="logo__text"><?= \CloudStore\App\Engine\Config\Config::$config['site_name'] ?></h1>
        </a>

    </div>
</div>

<?php require_once $view_path; ?>

<!--
<div class="js-user-popup-start js-user_help_popup__start">
    Подсказка
</div>
<div class="js-user_help_popup">
    <div class="user_help_popup__header">
        Помощь
    </div>
    <span class="user_help_popup__close">&#215;</span>
    <div class="user_help_popup__body">
        <ul class="user_help_popup__body-tabs">
            <li data-tab="popup_video_tab" class="popup_tab popup-active">Подсказки</li>
            <li data-tab="popup_text_tab" class="popup_tab">Помощь</li>
        </ul>
        <div id="popup_video_tab" class="user_help_popup__body-main popup_tab_block-active">
            <span class="user_help_popup__body-video-title">При загрузкe ролика возникла ошибка</span>
            <div class="user_help_popup__body-video">
                <iframe class="user_help_popup__body-youtube-frame" width="100%" height="230px" src="" frameborder="0"
                        allowfullscreen></iframe>
            </div>
            <div class="user_help_popup__body-video-list">
                <span class="user_help_popup__body-video-list-title">Другие ролики</span>
                <ul class="user_help_popup__body-video-list-links">
                    <?php $videos = $this->widget->widgetPopup->videos() ?>
                    <?php if ($videos): ?>

                        <?php foreach ($videos as $video): ?>
                            <li data-popup-id="<?= $video['popup_id'] ?>" data-popup-name="<?= $video['popup_name'] ?>"
                                data-src="<?= $video['popup_youtube'] ?>"
                                class="popup-link-active user_help_popup__body-video-link"><?= $video['popup_name'] ?></li>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div id="popup_text_tab" class="user_help_popup__body-main"></div>
    </div>
</div>
-->

<script type="text/javascript" src="<?php echo THEME_STATIC_URL; ?>style/assets/checkout.js"></script>
<script type="text/javascript" src="<?php echo THEME_STATIC_URL; ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo THEME_STATIC_URL; ?>js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo THEME_STATIC_URL; ?>js/main_scripts.js"></script>
<script type="text/javascript" src="<?php echo THEME_STATIC_URL; ?>js/easing.js"></script>

</body>
</html>


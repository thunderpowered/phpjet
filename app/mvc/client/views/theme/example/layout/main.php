<!DOCTYPE html>
<!--[if lt IE 7]><html lang="ru" class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html lang="ru" class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html lang="ru" class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru">
<!--<![endif]-->
<head>

    <!-- META -->
    <meta charset="UTF-8">

    <!-- CSRF TOKEN (CO) -->
    <meta name="csrf_token" content="<?= \CloudStore\CloudStore::$app->system->token->generateToken() ?>">

    <!-- Color cheme for Chrome, Firefox and Opera -->
    <meta name="theme-color" content="#000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#000">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <!-- END META -->

    <?= \CloudStore\CloudStore::$app->tool->SEO->getMetaTags($this->controller) ?>

    <title><?php echo $this->controller->getTitle(); ?></title>

    <link rel="shortcut icon" href="<?php echo $this->widget->widgetInfo->getFavicon(); ?>" type="image/x-icon">

    <!-- STYLESHEETS -->

    <!-- CUSTOM STYLES -->
    <?php $this->includeCSS("css/shopengine.all.css"); ?>
    <!-- END STYLESHEETS -->

    <!-- CUSTOM STYLES -->
    <?php $this->includeCSS("css/theme.libs.css"); ?>
    <?php $this->includeCSS("css/theme.min.css"); ?>

    <!-- COMMON CSS -->
    <!-- TODO: MOVE SITE SELECTION INTO FUNCTION -->
    <?php $this->includeCommon("common/styles/" . \CloudStore\App\Engine\Config\Config::$config["site_id"] . "/common.min.css"); ?>

    <?php $this->includeJS("js/shopengine.libs.js"); ?>

    <!-- BOOTSTRAP -->
    <?php $this->includeJS("js/bootstrap.min.js"); ?>

    <!-- TEMP -->
    <?php $this->includeCSS("css/jquery-ui.min.css"); ?>
    <?php $this->includeCSS("css/owl.carousel.min.css"); ?>
    <?php $this->includeJS("js/owl.carousel.min.js"); ?>

    <!-- MAGNIFIC POPUP -->
    <?php $this->includeCSS("css/magnific-popup.css"); ?>

    <?php $this->includeCSS("css/temp.css"); ?>

    <!-- DEFAULT MESSAGE WIDGET [TEMP] -->
    <?php $this->includeJS('js/jquery.cookie.js'); ?>
    <?php $this->includeJS('js/widget_message.js'); ?>
    <?php $this->includeCSS('css/widget_message.css'); ?>
    <!-- END DEFAULT MESSAGE WIDGET-->

    <!-- ANALYTICS -->
    <?php $this->widget->widgetSEO->getAnalytics(); ?>
    <!-- END ANALYTICS -->

    <style class="theme-adjust">
        <?php $this->widget->widgetTheme->getWidget(); ?>
    </style>
    <script>
        var shopengineSystemHost = "<?= \CloudStore\CloudStore::$app->router->getHost() ?>";
    </script>

    <!-- WIDGET STATIC FILES -->
    <?= $this->widget->getStyles(); ?>
    <?= $this->widget->getScripts(); ?>
    <!-- END WIDGET STYLES -->
</head>

<!-- BODY -->
<body data-spy="scroll" data-target="#menuScrollSpy" data-offset="150" class="<?php echo \CloudStore\CloudStore::$app->router->isHome() ? "body--home " : ""; ?>">

<!-- MMENU PLUGIN -->
<div id="my-page">

    <!-- HEADER -->
    <?php $this->includePart("header"); ?>
    <!-- END HEADER -->

    <!-- MAIN CONTENT -->
    <div id="my-content">
        <main class="main" id="MainContent">
            <!-- TEMP ('till template is ready enough) -->
            <?php if(\CloudStore\CloudStore::$app->router->isHome()): ?>
                <?php echo $this->view; ?>
            <?php else: ?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $this->view ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
    <!-- END MAIN CONTENT -->

    <!-- FOOTER -->
    <?php $this->includePart("footer"); ?>
    <!-- END FOOTER -->

</div>

<!-- MESSAGE -->
<?php echo $this->widget->widgetMessage->getWidget(); ?>

<!-- POPUP -->
<?php $this->includePart("popup"); ?>

<!-- SCRIPTS -->
<?php $this->includePart("scripts"); ?>
<!-- END SCRIPTS -->

<!-- TEMP -->
<?php $this->includeJS('js/jquery.mask.min.js'); ?>
<?php $this->includeJS("js/temp.js"); ?>

</body>
<!-- END BODY -->

</html>
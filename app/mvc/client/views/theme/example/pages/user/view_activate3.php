<!DOCTYPE HTML>
<html style="background-color:#fff">
    <meta http-equiv="Refresh" content="3; url=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/account">
    <head>
        <title>Учётная запись уже активирована, либо не существует. Вы будете перенаправлены автоматически.</title>

        <?php $this->includeCSS("css/shopengine.libs.css"); ?>
        <?php $this->includeCSS("css/shopengine.all.css"); ?>

        <style>
            .text-center {
                padding:15px;
            }
        </style>

    </head>
    <body>
    <div class="page-empty text-center">
        <h1>Учётная запись уже активирована, либо не существует. Вы будете перенаправлены автоматически.</h1>
        <p>Если ничего не происходит, нажмите на ссылку ниже.</p>
        <hr>
        <p>
            <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/account" class="btn">Вернуться в личный кабинет.</a>
</p>
</div>
</body>
</html>
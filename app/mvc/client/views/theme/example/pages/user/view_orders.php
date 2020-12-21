<h1>Профиль</h1>

<div class="grid">
    <div class="grid__item medium-up--two-thirds">
        <div class="content-block content-block--large">
            <h2>История заказов</h2>

            <?php if ($orders): ?>

                <table class="responsive-table">
                    <thead>
                    <tr>
                        <th>Заказ</th>
                        <th>Дата</th>

                        <th>Статус</th>
                        <th>Всего</th>
                        <th>Счет / оплата</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($orders as $order): ?>

                        <tr>
                            <td data-label="Заказ"><a
                                        href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/checkout/thank_you?orderid=<?= $order['orders_key'] ?>"
                                        title="">#<?= $order['orders_id'] ?></a></td>
                            <td data-label="Дата"><?= $order['orders_date'] ?></td>

                            <td data-label="">


                                <?php if ($order['orders_status'] === '0' OR !$order['orders_status']) $status = 'Не завершён' ?>
                                <?php if ($order['orders_status'] === '1') $status = 'Выполняется' ?>
                                <?php if ($order['orders_status'] === '2') $status = 'Завершён' ?>

                                <?= $status ?>

                            </td>
                            <td data-label="Всего"><?= CloudStore\App\Engine\Components\Utils::as_price($order['orders_price']) ?></td>
                            <td>
                                <a target="_blank"
                                   href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/checkout/download?orderid=<?= $order['orders_key'] ?>">Скачать
                                    счет</a>

                                <span class="paymentLink_old" data-id="<?= $order['orders_id'] ?>"></span>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>
                </table>

            <?php else: ?>
                <h4>История заказов пуста</h4>
            <?php endif; ?>


        </div>
    </div>
    <div class="grid__item medium-up--one-third">
        <div class="content-block">
            <h6 class="content-block__title text-left">Информация Вашего Аккаунта</h6>

            <p><?= \CloudStore\App\Engine\Components\Request::getSession('user_name') ?> <?= \CloudStore\App\Engine\Components\Request::getSession('user_last_name') ?> <?= \CloudStore\App\Engine\Components\Request::getSession('user_patronymic') ?></p>

            <?php if ($addresses): ?>


                <p>
                    <?= $addresses[0]['address'] ?><br>


                    <?= $addresses[0]['address_city'] ?><br>


                    <?= $addresses[0]['region_name'] ?><br>


                    <?= $addresses[0]['address_index'] ?><br>


                    <?= $addresses[0]['country_name'] ?><br>


                    <?= $addresses[0]['address_phone'] ?>

                </p>

            <?php endif; ?>


            <p><a href="/user/addresses">Просмотреть Адреса</a></p>
        </div>
    </div>
</div>

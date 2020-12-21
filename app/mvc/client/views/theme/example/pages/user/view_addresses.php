<h1>Просмотр адресов</h1>
<div class="grid">
    <div class="grid__item medium-up--two-thirds">
        <?php if (isset($start["new"])) { ?>
            <div id="AddressNewForm" class="content-block content-block--large form-vertical">
                <form method="post" action="/user/addresses/add" id="address_form_new" accept-charset="UTF-8"><input
                            type="hidden" value="customer_address" name="form_type"><input type="hidden" name="utf8"
                                                                                           value="✓">
                    <h2 class="h3">Добавьте Новый Адрес</h2>

                    <div class="grid">

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressFirstNameNew">Имя</label>
                            <input type="text" id="AddressFirstNameNew" name="address_new_first_name"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_first_name') ?>">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressLastNameNew">Фамилия</label>
                            <input type="text" id="AddressLastNameNew" name="address_new_last_name"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_last_name') ?>">
                        </div>

                    </div>

                    <div class="grid">

                        <div class="grid__item">
                            <label for="AddressCompanyNew">Компания</label>
                            <input type="text" id="AddressCompanyNew" name="address_new_company"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_company') ?>">

                            <label for="AddressAddress1New">Адрес доставки*</label>
                            <input type="text" id="AddressAddress1New" name="address_new_address"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_address') ?>">

                            <label for="AddressAddress2New">Дополнительная информация</label>
                            <input type="text" id="AddressAddress2New" name="address_new_optional"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_optional') ?>">
                        </div>

                    </div>

                    <div class="grid">

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressCityNew">Город*</label>
                            <input type="text" id="AddressCityNew" name="address_new_city"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_city') ?>">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressCountryNew">Страна</label>
                            <select id="checkout_shipping_address_country" name="address_country"
                                    data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" data-default="">
                                <option selected disabled value="" data-provinces="">Страна</option>
                                <?php if (!empty($countries)) { ?>
                                    <?php foreach ($countries as $country) { ?>
                                        <option value="<?= $country['country_handle'] ?>"
                                                data-provinces=""><?= $country['country_name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="grid__item" id="AddressProvinceContainerNew">
                            <label for="AddressProvinceNew">Область</label>
                            <select selected disabled id="checkout_shipping_address_province" name="address_new_region"
                                    data-default="">
                                <option value="">Область</option>
                            </select>
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressZipNew">Почтовый индекс*</label>
                            <input type="text" id="AddressZipNew" name="address_new_index"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_index') ?>"
                                   autocapitalize="characters">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressPhoneNew">Телефон*</label>
                            <input type="tel" id="AddressPhoneNew" name="address_new_phone"
                                   value="<?= \CloudStore\App\Engine\Components\Request::getSession('address_new_phone') ?>">
                        </div>
                    </div>
                    <p>* - обязательные поля</p>

                    <p>
                        <!--          <input type="checkbox" id="address_default_address_new" name="address[default]" value="1">
                                  <label for="address_default_address_new">Сохранить как адрес по умолчанию</label>-->
                    </p>

                    <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                    <input type="hidden" name="address_new" value="1"
                    <p><input type="submit" class="btn" value="Сохранить Адрес"></p>
                    <p class="text-center"><a href="/user/addresses">Отмена</a></p>

                </form>
            </div>
        <?php } ?>

        <?php if (isset($start["red"])) { ?>
            <div id="AddressNewForm" class="content-block content-block--large form-vertical">
                <form method="post" action="/user/addresses" id="address_form_new" accept-charset="UTF-8"><input
                            type="hidden" value="customer_address" name="form_type"><input type="hidden" name="utf8"
                                                                                           value="✓">
                    <h2 class="h3">Редактировать адрес</h2>

                    <div class="grid">

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressFirstNameNew">Имя</label>
                            <input type="text" id="AddressFirstNameNew" name="address_first_name"
                                   value="<?= $start['red']['address_name'] ?>">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressLastNameNew">Фамилия</label>
                            <input type="text" id="AddressLastNameNew" name="address_last_name"
                                   value="<?= $start['red']['address_last_name'] ?>">
                        </div>

                    </div>

                    <div class="grid">

                        <div class="grid__item">
                            <label for="AddressCompanyNew">Компания</label>
                            <input type="text" id="AddressCompanyNew" name="address_company"
                                   value="<?= $start['red']['address_company'] ?>">

                            <label for="AddressAddress1New">Адрес доставки*</label>
                            <input type="text" id="AddressAddress1New" name="address_address"
                                   value="<?= $start['red']['address'] ?>">

                            <label for="AddressAddress2New">Дополнительная информация</label>
                            <input type="text" id="AddressAddress2New" name="address_optional"
                                   value="<?= $start['red']['address_optional'] ?>">
                        </div>

                    </div>

                    <div class="grid">

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressCityNew">Город*</label>
                            <input type="text" id="AddressCityNew" name="address_city"
                                   value="<?= $start['red']['address_city'] ?>">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressCountryNew">Страна</label>
                            <select id="AddressCountryNew" name="address_country" data-default="">
                                <?php if (isset($countries) AND $countries) { ?>
                                    <?php foreach ($countries as $country) { ?>
                                        <option <?= $start['red']['address_country'] === $country['country_handle'] ? 'selected' : '' ?>
                                                class="select_country"
                                                value="<?= $country['country_handle'] ?>"><?= $country['country_name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="grid__item" id="AddressProvinceContainerNew">
                            <label for="AddressProvinceNew">Область</label>
                            <select id="AddressProvinceNew" name="address_region" data-default="">
                                <?php if (isset($regions) AND $regions) { ?>
                                    <?php foreach ($regions as $region) { ?>
                                        <option <?= $start['red']['address_region'] === $region['region_handle'] ? 'selected' : '' ?>
                                                value="<?= $region['region_handle'] ?>"><?= $region['region_name'] ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressZipNew">Почтовый индекс*</label>
                            <input type="text" id="AddressZipNew" name="address_index"
                                   value="<?= $start['red']['address_index'] ?>" autocapitalize="characters">
                        </div>

                        <div class="grid__item medium-up--one-half">
                            <label for="AddressPhoneNew">Телефон*</label>
                            <input type="tel" id="AddressPhoneNew" name="address_phone"
                                   value="<?= $start['red']['address_phone'] ?>">
                        </div>
                    </div>
                    <p>* - обязательные поля</p>

                    <p>
                        <!--          <input type="checkbox" id="address_default_address_new" name="address[default]" value="1">
                                  <label for="address_default_address_new">Сохранить как адрес по умолчанию</label>-->
                    </p>

                    <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                    <input type="hidden" name="address_change" value="<?= $start['red']['address_id'] ?>"
                    <p><input type="submit" class="btn" value="Сохранить Адрес"></p>
                    <p class="text-center"><a href="/user/addresses">Отмена</a></p>

                </form>
            </div>
        <?php } ?>

        <div class="content-block content-block--large">
            <h2>Ваши Адреса</h2>


            <p><?= \CloudStore\App\Engine\Components\Request::getSession('user_last_name') ?> <?= \CloudStore\App\Engine\Components\Request::getSession('user_name') ?> <?= \CloudStore\App\Engine\Components\Request::getSession('user_patronymic') ?></p>

            <?php if (isset($start['addresses']) AND $start['addresses']) { ?>

                <?php foreach ($start['addresses'] as $address) { ?>

                    <p>
                        <?= $address['address'] ?><br>


                        <?= $address['address_city'] ?><br>


                        <?= $address['region_name'] ?><br>


                        <?= $address['address_index'] ?><br>


                        <?= $address['country_name'] ?><br>


                        <?= $address['address_phone'] ?>

                    </p>

                    <p>
                        <a href="/user/addresses/red?addid=<?= $address['address_id'] ?>">Редактировать</a>
                        <!--          <button type="button" class="text-link link-accent-color address-edit-toggle" data-form-id="5739200643">Редактировать</button>-->
                        <button data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"
                                data-id="<?= $address['address_id'] ?>" type="button"
                                class="delete_address text-link link-accent-color address-delete"
                                data-form-id="5739200643"
                                data-confirm-message="Вы уверены, что хотите удалить этот адрес?">Удалить
                        </button>
                    </p>

                <?php } ?>

            <?php } else { ?>
                <h4>Нет адресов</h4>
            <?php } ?>

            <!--        <div id="EditAddress_5739200643" class="hide form-vertical">
                      <form method="post" action="/account/addresses/5739200643" id="address_form_5739200643" accept-charset="UTF-8"><input type="hidden" value="customer_address" name="form_type"><input type="hidden" name="utf8" value="✓">
            
                        <h4>Редактировать адрес</h4>
            
                        <div class="grid">
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressFirstName_5739200643">Имя</label>
                            <input type="text" id="AddressFirstName_5739200643" name="address[first_name]" value="Александр">
                          </div>
            
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressLastName_5739200643">Фамилия</label>
                            <input type="text" id="AddressLastName_5739200643" name="address[last_name]" value="Грачёв">
                          </div>
                        </div>
            
                        <label for="AddressCompany_5739200643">Компания</label>
                        <input type="text" id="AddressCompany_5739200643" name="address[company]" value="">
            
                        <label for="AddressAddress1_5739200643">Адрес доставки</label>
                        <input type="text" id="AddressAddress1_5739200643" name="address[address1]" value="Москва">
            
                        <label for="AddressAddress2_5739200643">Дополнительная информация</label>
                        <input type="text" id="AddressAddress2_5739200643" name="address[address2]" value="">
            
                        <div class="grid">
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressCity_5739200643">Город</label>
                            <input type="text" id="AddressCity_5739200643" name="address[city]" value="Москва">
                          </div>
            
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressCountry_5739200643">Страна</label>
                            <select id="AddressCountry_5739200643" class="address-country-option" data-form-id="5739200643" name="address[country]" data-default="Россия"><option value="Russia" data-provinces="[[&quot;Republic of Adygeya&quot;,&quot;Адыгея (респ)&quot;],[&quot;Altai Republic&quot;,&quot;Алтай (респ)&quot;],[&quot;Altai Krai&quot;,&quot;Алтайский (край)&quot;],[&quot;Amur Oblast&quot;,&quot;Амурская (обл)&quot;],[&quot;Arkhangelsk Oblast&quot;,&quot;Архангельская (обл)&quot;],[&quot;Astrakhan Oblast&quot;,&quot;Астраханская (обл)&quot;],[&quot;Republic of Bashkortostan&quot;,&quot;Башкортостан (респ)&quot;],[&quot;Belgorod Oblast&quot;,&quot;Белгородская (обл)&quot;],[&quot;Bryansk Oblast&quot;,&quot;Брянская (обл)&quot;],[&quot;Republic of Buryatia&quot;,&quot;Бурятия (респ)&quot;],[&quot;Vladimir Oblast&quot;,&quot;Владимирская (обл)&quot;],[&quot;Volgograd Oblast&quot;,&quot;Волгоградская (обл)&quot;],[&quot;Vologda Oblast&quot;,&quot;Вологодская (обл)&quot;],[&quot;Voronezh Oblast&quot;,&quot;Воронежская (обл)&quot;],[&quot;Republic of Dagestan&quot;,&quot;Дагестан (респ)&quot;],[&quot;Jewish Autonomous Oblast&quot;,&quot;Еврeйская (Аобл)&quot;],[&quot;Zabaykalsky Krai&quot;,&quot;Забайкальский край&quot;],[&quot;Ivanovo Oblast&quot;,&quot;Ивановская (обл)&quot;],[&quot;Republic of Ingushetia&quot;,&quot;Ингушетия (респ)&quot;],[&quot;Irkutsk Oblast&quot;,&quot;Иркутская (обл)&quot;],[&quot;Kabardino-Balkarian Republic&quot;,&quot;Кабардино-Балкарская (респ)&quot;],[&quot;Kaliningrad Oblast&quot;,&quot;Калининградская (обл)&quot;],[&quot;Republic of Kalmykia&quot;,&quot;Калмыкия (респ)&quot;],[&quot;Kaluga Oblast&quot;,&quot;Калужская (обл)&quot;],[&quot;Kamchatka Krai&quot;,&quot;Камчатский (край)&quot;],[&quot;Karachay–Cherkess Republic&quot;,&quot;Карачаево-Черкесская (респ)&quot;],[&quot;Republic of Karelia&quot;,&quot;Карелия (респ)&quot;],[&quot;Kemerovo Oblast&quot;,&quot;Кемеровская (обл)&quot;],[&quot;Kirov Oblast&quot;,&quot;Кировская (обл)&quot;],[&quot;Komi Republic&quot;,&quot;Коми (респ)&quot;],[&quot;Kostroma Oblast&quot;,&quot;Костромская (обл)&quot;],[&quot;Krasnodar Krai&quot;,&quot;Краснодарский (край)&quot;],[&quot;Krasnoyarsk Krai&quot;,&quot;Красноярский (край)&quot;],[&quot;Kurgan Oblast&quot;,&quot;Курганская (обл)&quot;],[&quot;Kursk Oblast&quot;,&quot;Курская (обл)&quot;],[&quot;Leningrad Oblast&quot;,&quot;Ленинградская (обл)&quot;],[&quot;Lipetsk Oblast&quot;,&quot;Липецкая (обл)&quot;],[&quot;Magadan Oblast&quot;,&quot;Магаданская (обл)&quot;],[&quot;Mari El Republic&quot;,&quot;Марий Эл (респ)&quot;],[&quot;Republic of Mordovia&quot;,&quot;Мордовия (респ)&quot;],[&quot;Moscow&quot;,&quot;Москва (г)&quot;],[&quot;Moscow Oblast&quot;,&quot;Московская (обл)&quot;],[&quot;Murmansk Oblast&quot;,&quot;Мурманская (обл)&quot;],[&quot;Nizhny Novgorod Oblast&quot;,&quot;Нижегородская (обл)&quot;],[&quot;Novgorod Oblast&quot;,&quot;Новгородская (обл)&quot;],[&quot;Novosibirsk Oblast&quot;,&quot;Новосибирская (обл)&quot;],[&quot;Omsk Oblast&quot;,&quot;Омская (обл)&quot;],[&quot;Orenburg Oblast&quot;,&quot;Оренбургская (обл)&quot;],[&quot;Oryol Oblast&quot;,&quot;Орловская (обл)&quot;],[&quot;Penza Oblast&quot;,&quot;Пензенская (обл)&quot;],[&quot;Perm Krai&quot;,&quot;Пермский (край)&quot;],[&quot;Primorsky Krai&quot;,&quot;Приморский (край)&quot;],[&quot;Pskov Oblast&quot;,&quot;Псковская (обл)&quot;],[&quot;Rostov Oblast&quot;,&quot;Ростовская (обл)&quot;],[&quot;Ryazan Oblast&quot;,&quot;Рязанская (обл)&quot;],[&quot;Samara Oblast&quot;,&quot;Самарская (обл)&quot;],[&quot;Saint Petersburg&quot;,&quot;Санкт-Петербург (г)&quot;],[&quot;Saratov Oblast&quot;,&quot;Саратовская (обл)&quot;],[&quot;Sakha Republic (Yakutia)&quot;,&quot;Саха (респ)&quot;],[&quot;Sakhalin Oblast&quot;,&quot;Сахалинская (обл)&quot;],[&quot;Sverdlovsk Oblast&quot;,&quot;Свердловская (обл)&quot;],[&quot;Republic of North Ossetia–Alania&quot;,&quot;Северная Осетия (респ)&quot;],[&quot;Smolensk Oblast&quot;,&quot;Смоленская (обл)&quot;],[&quot;Stavropol Krai&quot;,&quot;Ставропольский (край)&quot;],[&quot;Tambov Oblast&quot;,&quot;Тамбовская (обл)&quot;],[&quot;Republic of Tatarstan&quot;,&quot;Татарстан (респ)&quot;],[&quot;Tver Oblast&quot;,&quot;Тверская (обл)&quot;],[&quot;Tomsk Oblast&quot;,&quot;Томская (обл)&quot;],[&quot;Tula Oblast&quot;,&quot;Тульская (обл)&quot;],[&quot;Tyva Republic&quot;,&quot;Тыва (респ)&quot;],[&quot;Tyumen Oblast&quot;,&quot;Тюменская (обл)&quot;],[&quot;Udmurtia&quot;,&quot;Удмуртская (респ)&quot;],[&quot;Ulyanovsk Oblast&quot;,&quot;Ульяновская (обл)&quot;],[&quot;Khabarovsk Krai&quot;,&quot;Хабаровский (край)&quot;],[&quot;Republic of Khakassia&quot;,&quot;Хакасия (респ)&quot;],[&quot;Khanty-Mansi Autonomous Okrug&quot;,&quot;Ханты-Мансийский (АО)&quot;],[&quot;Chelyabinsk Oblast&quot;,&quot;Челябинская (обл)&quot;],[&quot;Chechen Republic&quot;,&quot;Чеченская (респ)&quot;],[&quot;Chuvash Republic&quot;,&quot;Чувашская (респ)&quot;],[&quot;Chukotka Autonomous Okrug&quot;,&quot;Чукотский (АО)&quot;],[&quot;Yamalo-Nenets Autonomous Okrug&quot;,&quot;Ямaло-Нeнецкий (АО)&quot;],[&quot;Yaroslavl Oblast&quot;,&quot;Ярослaвская (обл)&quot;]]">Россия</option></select>
                          </div>
                        </div>
            
                        <div id="AddressProvinceContainer_5739200643">
                          <label for="AddressProvince_5739200643">Область</label>
                          <select id="AddressProvince_5739200643" name="address[province]" data-default="Москва (г)"><option value="Republic of Adygeya">Адыгея (респ)</option><option value="Altai Republic">Алтай (респ)</option><option value="Altai Krai">Алтайский (край)</option><option value="Amur Oblast">Амурская (обл)</option><option value="Arkhangelsk Oblast">Архангельская (обл)</option><option value="Astrakhan Oblast">Астраханская (обл)</option><option value="Republic of Bashkortostan">Башкортостан (респ)</option><option value="Belgorod Oblast">Белгородская (обл)</option><option value="Bryansk Oblast">Брянская (обл)</option><option value="Republic of Buryatia">Бурятия (респ)</option><option value="Vladimir Oblast">Владимирская (обл)</option><option value="Volgograd Oblast">Волгоградская (обл)</option><option value="Vologda Oblast">Вологодская (обл)</option><option value="Voronezh Oblast">Воронежская (обл)</option><option value="Republic of Dagestan">Дагестан (респ)</option><option value="Jewish Autonomous Oblast">Еврeйская (Аобл)</option><option value="Zabaykalsky Krai">Забайкальский край</option><option value="Ivanovo Oblast">Ивановская (обл)</option><option value="Republic of Ingushetia">Ингушетия (респ)</option><option value="Irkutsk Oblast">Иркутская (обл)</option><option value="Kabardino-Balkarian Republic">Кабардино-Балкарская (респ)</option><option value="Kaliningrad Oblast">Калининградская (обл)</option><option value="Republic of Kalmykia">Калмыкия (респ)</option><option value="Kaluga Oblast">Калужская (обл)</option><option value="Kamchatka Krai">Камчатский (край)</option><option value="Karachay–Cherkess Republic">Карачаево-Черкесская (респ)</option><option value="Republic of Karelia">Карелия (респ)</option><option value="Kemerovo Oblast">Кемеровская (обл)</option><option value="Kirov Oblast">Кировская (обл)</option><option value="Komi Republic">Коми (респ)</option><option value="Kostroma Oblast">Костромская (обл)</option><option value="Krasnodar Krai">Краснодарский (край)</option><option value="Krasnoyarsk Krai">Красноярский (край)</option><option value="Kurgan Oblast">Курганская (обл)</option><option value="Kursk Oblast">Курская (обл)</option><option value="Leningrad Oblast">Ленинградская (обл)</option><option value="Lipetsk Oblast">Липецкая (обл)</option><option value="Magadan Oblast">Магаданская (обл)</option><option value="Mari El Republic">Марий Эл (респ)</option><option value="Republic of Mordovia">Мордовия (респ)</option><option value="Moscow">Москва (г)</option><option value="Moscow Oblast">Московская (обл)</option><option value="Murmansk Oblast">Мурманская (обл)</option><option value="Nizhny Novgorod Oblast">Нижегородская (обл)</option><option value="Novgorod Oblast">Новгородская (обл)</option><option value="Novosibirsk Oblast">Новосибирская (обл)</option><option value="Omsk Oblast">Омская (обл)</option><option value="Orenburg Oblast">Оренбургская (обл)</option><option value="Oryol Oblast">Орловская (обл)</option><option value="Penza Oblast">Пензенская (обл)</option><option value="Perm Krai">Пермский (край)</option><option value="Primorsky Krai">Приморский (край)</option><option value="Pskov Oblast">Псковская (обл)</option><option value="Rostov Oblast">Ростовская (обл)</option><option value="Ryazan Oblast">Рязанская (обл)</option><option value="Samara Oblast">Самарская (обл)</option><option value="Saint Petersburg">Санкт-Петербург (г)</option><option value="Saratov Oblast">Саратовская (обл)</option><option value="Sakha Republic (Yakutia)">Саха (респ)</option><option value="Sakhalin Oblast">Сахалинская (обл)</option><option value="Sverdlovsk Oblast">Свердловская (обл)</option><option value="Republic of North Ossetia–Alania">Северная Осетия (респ)</option><option value="Smolensk Oblast">Смоленская (обл)</option><option value="Stavropol Krai">Ставропольский (край)</option><option value="Tambov Oblast">Тамбовская (обл)</option><option value="Republic of Tatarstan">Татарстан (респ)</option><option value="Tver Oblast">Тверская (обл)</option><option value="Tomsk Oblast">Томская (обл)</option><option value="Tula Oblast">Тульская (обл)</option><option value="Tyva Republic">Тыва (респ)</option><option value="Tyumen Oblast">Тюменская (обл)</option><option value="Udmurtia">Удмуртская (респ)</option><option value="Ulyanovsk Oblast">Ульяновская (обл)</option><option value="Khabarovsk Krai">Хабаровский (край)</option><option value="Republic of Khakassia">Хакасия (респ)</option><option value="Khanty-Mansi Autonomous Okrug">Ханты-Мансийский (АО)</option><option value="Chelyabinsk Oblast">Челябинская (обл)</option><option value="Chechen Republic">Чеченская (респ)</option><option value="Chuvash Republic">Чувашская (респ)</option><option value="Chukotka Autonomous Okrug">Чукотский (АО)</option><option value="Yamalo-Nenets Autonomous Okrug">Ямaло-Нeнецкий (АО)</option><option value="Yaroslavl Oblast">Ярослaвская (обл)</option></select>
                        </div>
            
                        <div class="grid">
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressZip_5739200643">Почтовый индекс</label>
                            <input type="text" id="AddressZip_5739200643" name="address[zip]" value="234234" autocapitalize="characters">
                          </div>
            
                          <div class="grid__item medium-up--one-half">
                            <label for="AddressPhone_5739200643">Телефон</label>
                            <input type="tel" id="AddressPhone_5739200643" name="address[phone]" value="8 965 391-64-14">
                          </div>
                        </div>
            
                        <p>
                          <input type="checkbox" id="address_default_address_5739200643" name="address[default]" value="1">
                          <label for="address_default_address_5739200643">Сохранить как адрес по умолчанию</label>
                        </p>
            
                        <p><input type="submit" class="btn" value="Обновить Адрес"></p>
                        <p class="text-center"><button type="button" class="text-link link-accent-color address-edit-toggle" data-form-id="5739200643">Отмена</button></p>
            
                      <input type="hidden" name="_method" value="put"></form>
                    </div>-->
        </div>
    </div>
    <div class="grid__item medium-up--one-third">
        <div class="content-block text-center">
            <p><a href="/user/addresses/add" class="btn address-new-toggle">Добавьте Новый Адрес</a></p>
        </div>
    </div>
</div>


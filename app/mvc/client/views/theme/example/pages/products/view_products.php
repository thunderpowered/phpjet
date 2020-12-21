<meta itemprop="name" content="<?= $product['title'] ?>">
<meta itemprop="url" content="<?= $this->controller->GetLink() ?>">
<meta itemprop="image" content="<?= $product['image_link'] ?>">

<?php $this->widget->widgetMenu->getBreadCrumbs(true); ?>

<button data-id="<?= $product['id'] ?>" class="main__product-favorite main__product-favorite--product-page js-favorite <?= $product["favorite"] ? "is-favorite" : "" ?>">
    <span class="main__product-favorite-indicator o-block-center"></span>
</button>

<div class="grid product-single">
    <div class="grid__item medium-up--one-half">
        <div class="photos">
            <div class="photos__item photos__item--main">
                <div class="product-single__photo" id="ProductPhoto">

                    <a data-index="0" href="<?php echo CloudStore\App\Engine\Components\Utils::getImageLink($product['image_link']); ?>"
                       class="magnific-popup js-modal-open-product-modal image-link no-outline" id="MainImageLink">
                        <?php echo CloudStore\App\Engine\Components\Utils::resizeImage(IMAGES . $product['image_link'], null, null, $product['title'], null, "ProductPhotoImg"); ?>
                    </a>

                </div>
            </div>
            <?php if (isset($gallery) AND count($gallery) > 1): ?>
                <div class="photos__item photos__item--thumbs">
                    <div class="product-single__thumbnails" style="" id="ProductThumbs">

                        <?php foreach ($gallery as $image): ?>

                            <div class="product-single__thumbnail-item  is-active ">
                                <a data-index="<?= $image['add_img_id'] ?? '' ?>"
                                   href="<?php echo CloudStore\App\Engine\Components\Utils::getImageLink($image['add_img_link']); ?>"
                                   data-zoom="" class="product-single__thumbnail gallery-item magnific-popup">
                                    <img src="<?php echo CloudStore\App\Engine\Components\Utils::getThumbnailLink($image['add_img_link']); ?>"
                                         alt="<?= $product['title'] ?>">
                                </a>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

            <?php endif; ?>

            <script>
                var settings = {
                    id: 0,
                    items: []
                };

                $(document).ready(function () {

                    $('.magnific-popup').magnificPopup({
                        type: 'image',
                        gallery: {
                            enabled: true
                        },
                        callbacks: {
                            elementParse: function (item) {
                                var magnificPopup = $.magnificPopup.instance;
                            },
                            beforeOpen: function () {

                                var magnificPopup = $.magnificPopup.instance;

                                settings.array = magnificPopup.items;

                                var item_href = magnificPopup.st.el[0].href;

                                if (magnificPopup.st.el[0].id !== "MainImageLink") {
                                    magnificPopup.close();
                                }

                                for (var i = 0, c = magnificPopup.items.length; i < c; i++) {
                                    if (magnificPopup.items[i].id !== "MainImageLink" && magnificPopup.items[i].href === item_href) {
                                        if (magnificPopup.st.el[0].id === "MainImageLink") {
                                            settings.id = i;
                                            magnificPopup.items.splice(i, 1);
                                            /* magnificPopup.items.splice(i, 1, magnificPopup.items[magnificPopup.items.length - 1]); */
                                            /* magnificPopup.items.splice(magnificPopup.items.length - 1, 1); */
                                            break;
                                        }
                                    }

                                }

                            },
                            open: function () {
                                var magnificPopup = $.magnificPopup.instance;
                                if (magnificPopup.st.el[0].id !== "MainImageLink") {
                                    magnificPopup.close();
                                }
                            },
                            beforeClose: function () {
                                var magnificPopup = $.magnificPopup.instance;
                            }
                        },
                    });

                });

            </script>

        </div>
    </div>

    <div class="grid__item medium-up--one-half" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <div class="product-single__info-wrapper">
            <meta itemprop="priceCurrency" content="RUB">
            <link itemprop="availability" href="http://schema.org/InStock">

            <div class="product-single__meta small--text-center">

                <p class="product-single__type"><?php //echo $product["category_full"]; ?></p>


                <p class="product-single__vendor"><a href="/search/?q=<?= $product['brand'] ?>"
                                                     title="<?= $product['brand'] ?>"><?= $product['brand'] ?></a></p>


                <h1 itemprop="name" class="product-single__title"><?= $product['title'] ?></h1>

                <p class="product-signle__sku"><?= $product['products_sku'] ?></p>

                <ul class="product-single__meta-list list--no-bullets list--inline">
                    <?php if ($product['old_price']): ?>

                        <li id="ProductSaleTag" class="">
                            <div class="product-tag">
                                %
                            </div>
                        </li>
                    <?php endif; ?>

                    <li>
                        <span id="ProductPrice" class="product-single__price" itemprop="price" content="13399">
                            <?php if ($product["modifications"]): ?>

                                <?= \CloudStore\App\Engine\Components\Utils::asPrice(reset($product["modifications"])["price"]) ?>
                            <?php else: ?>

                                <?= $product['price'] ?>
                            <?php endif; ?>
                        </span>
                    </li>

                    <li>
                        <span class="visually-hidden">Обычная цена</span>
                        <s id="ComparePrice" class="product-single__price product-single__price--compare">
                            <?= $product['old_price'] ?>
                        </s>
                    </li>


                </ul>

                <?php if ($product['message']): ?>

                <p class="product-signle__field"><?= $product['message'] ?></p>

                <?php endif; ?>

            </div>

            <?php if ($product["modifications"]): ?>

                <hr>

                <div class="product-single__meta small--text-center">

                    <label for="Modifications" class="visually-hidden"></label>
                    <select id="Modifications" class="js-modification-select">

                        <?php $i = 0;
                        foreach ($product["modifications"] as $modification): ?>

                            <option data-price="<?= \CloudStore\App\Engine\Components\Utils::asPrice($modification["price"]); ?>" <?php echo $i === 0 ? "selected" : ""; ?>
                                    value="<?php echo $modification["id"]; ?>"><?php echo $modification["modification"]; ?>
                                | Доставка: <?php echo $modification["status"]["shipping_date"]; ?></option>

                            <?php $i++;
                        endforeach; ?>

                    </select>

                </div>

            <?php endif; ?>

            <hr>

            <?php if ($product['shipping_avail'] || $product["modifications"]): ?>

                <form data-id="<?=$product["id"] ?>" data-csrf="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>" data-handle="<?= $product['handle'] ?>"
                      action="javascript:void(0)" method="post" enctype="multipart/form-data" class="product-form"
                      id="AddToCartForm">
                    <select name="id" id="ProductSelect" class="product-form__variants">

                        <option selected="selected" data-sku="" value="32695415619">

                            Default Title - <?= $product['price'] ?>

                        </option>

                    </select>
                    <label class="d-none" for="ProductSelect">Стоимость</label>
                    <div class="product-form__item product-form__item--quantity">
                        <!--<label for="Quantity">Количество</label>-->
                        <div class="js-qty">
                            <label class="d-none" for="Quantity">Количество</label>
                            <input type="text" value="1" id="Quantity" name="quantity" pattern="[0-9]*" data-line=""
                                   class="js-qty__input" aria-live="polite">
                            <button type="button" class="js-qty__adjust js-qty__adjust--minus add_to_cart_minus"
                                    aria-label="Уменьшить количество">
                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 22 3"
                                     class="icon icon-minus">
                                    <path fill="#000" d="M21.5.5v2H.5v-2z" fill-rule="evenodd"></path>
                                </svg>
                                <span class="icon__fallback-text">−</span>
                            </button>
                            <button type="button" class="js-qty__adjust js-qty__adjust--plus add_to_cart_plus"
                                    aria-label="Увеличить количество">
                                <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 22 21"
                                     class="icon icon-plus">
                                    <path d="M12 11.5h9.5v-2H12V0h-2v9.5H.5v2H10V21h2v-9.5z" fill="#000"
                                          fill-rule="evenodd"></path>
                                </svg>
                                <span class="icon__fallback-text">+</span>
                            </button>
                        </div>
                    </div>
                    <div id="Order" class="product-form__item product-form__item--submit d-flex"></div>
                </form>

                <hr>

            <?php endif; ?>


            <div class="social-sharing grid medium-up--grid--table"
                 data-permalink="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>">

                <div class="grid__item medium-up--one-third medium-up--text-left">
                    <span class="social-sharing__title">Поделиться</span>
                </div>

                <div class="grid__item medium-up--two-thirds medium-up--text-right">

                    <a target="_blank"
                       href="//www.facebook.com/sharer.php?u=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>"
                       class="social-sharing__link share-facebook" title="Поделиться в Facebook">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32"
                             class="icon icon-facebook">
                            <path fill="#444"
                                  d="M18.222 11.556V8.91c0-1.194.264-1.799 2.118-1.799h2.326V2.667h-3.882c-4.757 0-6.326 2.181-6.326 5.924v2.965H9.333V16h3.125v13.333h5.764V16h3.917l.528-4.444h-4.444z"></path>
                        </svg>
                        <span class="share-title visually-hidden">Поделиться в Facebook</span>
                    </a>

                    <a target="_blank"
                       href="//twitter.com/share?text=6%20Professional&amp;url=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>"
                       class="social-sharing__link share-twitter">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32"
                             class="icon icon-twitter">
                            <path fill="#444"
                                  d="M30.75 6.844c-1.087.48-2.25.806-3.475.956a6.08 6.08 0 0 0 2.663-3.35 12.02 12.02 0 0 1-3.844 1.47A6.044 6.044 0 0 0 21.674 4a6.052 6.052 0 0 0-6.05 6.056c0 .475.05.938.157 1.38a17.147 17.147 0 0 1-12.474-6.33 6.068 6.068 0 0 0 1.88 8.088 5.91 5.91 0 0 1-2.75-.756v.075a6.056 6.056 0 0 0 4.857 5.937 6.113 6.113 0 0 1-1.594.212c-.39 0-.77-.038-1.14-.113a6.06 6.06 0 0 0 5.657 4.205 12.132 12.132 0 0 1-8.963 2.507A16.91 16.91 0 0 0 10.516 28c11.144 0 17.23-9.23 17.23-17.238 0-.262-.005-.525-.018-.78a12.325 12.325 0 0 0 3.02-3.14z"/>
                        </svg>
                        <span class="share-title visually-hidden">Поделиться в Twitter</span>
                    </a>


                    <a target="_blank"
                       href="//pinterest.com/pin/create/button/?url=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>&amp;media=<?= $product['image_link'] ?>&amp;description=<?= $product['desc_clear'] ?>"
                       class="social-sharing__link share-pinterest">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32"
                             class="icon icon-pinterest">
                            <path fill="#444"
                                  d="M16 2C8.27 2 2 8.27 2 16c0 5.73 3.45 10.656 8.38 12.825-.036-.975-.005-2.15.245-3.212.27-1.137 1.8-7.63 1.8-7.63s-.45-.895-.45-2.214c0-2.076 1.2-3.626 2.7-3.626 1.275 0 1.887.956 1.887 2.1 0 1.28-.82 3.194-1.238 4.97-.35 1.487.744 2.693 2.212 2.693 2.65 0 4.438-3.406 4.438-7.444 0-3.07-2.07-5.362-5.825-5.362-4.245 0-6.895 3.17-6.895 6.707 0 1.22.363 2.08.925 2.744.256.307.294.432.2.782-.07.256-.22.875-.287 1.125-.094.356-.38.48-.7.35-1.956-.8-2.87-2.938-2.87-5.35 0-3.975 3.357-8.744 10.007-8.744 5.344 0 8.863 3.87 8.863 8.02 0 5.493-3.056 9.593-7.556 9.593-1.512 0-2.93-.82-3.42-1.744 0 0-.812 3.225-.987 3.85-.294 1.08-.875 2.156-1.406 3 1.256.37 2.588.575 3.97.575 7.73 0 14-6.27 14-14C29.998 8.27 23.73 2 15.998 2z"/>
                        </svg>
                        <span class="share-title visually-hidden">Поделиться в Pinterest</span>
                    </a>


                    <a target="_blank"
                       href="//fancy.com/fancyit?ItemURL=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>&amp;Title=<?= $product['title'] ?>&amp;Category=<?= $product['category'] ?>&amp;ImageURL=<?= $product['image_link'] ?>"
                       class="social-sharing__link share-fancy">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 24 32"
                             class="icon icon-fancy">
                            <path fill="#444"
                                  d="M21.415 10.487q0-3.37-2.845-5.744T11.738 2.37 4.924 4.742t-2.827 5.744v10.837q0 .652.598 1.142t1.432.49h5.255v5.254q0 .834.69 1.413t1.666.58 1.685-.58.707-1.413v-5.255h5.29q.835 0 1.414-.49t.58-1.14V10.486z"/>
                        </svg>
                        <span class="share-title visually-hidden">Поделиться в Fancy</span>
                    </a>


                    <a target="_blank"
                       href="//plus.google.com/share?url=<?= CloudStore\App\Engine\Core\Router::getHost() ?>/products/<?= $product['handle'] ?>"
                       class="social-sharing__link share-google">
                        <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 50 32"
                             class="icon icon-google-plus">
                            <path fill="#444"
                                  d="M17.828 14.17v4.39h7.26c-.294 1.883-2.195 5.522-7.26 5.522-4.37 0-7.936-3.62-7.936-8.082s3.566-8.082 7.936-8.082c2.487 0 4.15 1.06 5.102 1.975l3.474-3.346c-2.23-2.085-5.12-3.346-8.576-3.346-7.077 0-12.8 5.724-12.8 12.8s5.723 12.8 12.8 12.8c7.387 0 12.288-5.192 12.288-12.506 0-.84-.09-1.48-.2-2.12H17.827zM45.257 14.17H41.6v-3.656h-3.657v3.657h-3.657v3.658h3.657v3.657H41.6v-3.657h3.657z"/>
                        </svg>
                        <span class="share-title visually-hidden">+1</span>
                    </a>

                </div>
            </div>


        </div>
        <hr>

        <?php if (!$product['modifications']): ?>
            <div class="grid medium-up--grid--table">
                <span style="font-size:14px;font-weight:400">Возможность доставки и самовывоза: <span
                            style="font-weight:600"><?= $product['shipping_date'] ?></span></span>
            </div>
        <?php endif; ?>

    </div>

</div>
<hr>

<div class="row">
    <div data-offset="200" id="main_product_description"
         class="full textForAffixMenu article rte product-single__description" itemprop="description">
        <?= $product['description'] ?>
        <div class='einstein_helper'>Нашли ошибку в тексте? Выделите ее и нажмите SHIFT+ENTER</div>
    </div>
    <script>

        var images = document.querySelectorAll(".textForAffixMenu img");

        console.log(images);

        for (var i = 0, c = images.length; i < c; i++) {
            var link = document.createElement("a");
            link.href = images[i].getAttribute("src");
            link.style.height = "auto";
            link.style.display = "block";
            link.style.outline = "none";
            link.style.textDecoration = "none";
            link.style.borderBottom = "none";
            link.classList.add("product_description_gallery");

            images[i].parentNode.insertBefore(link, images[i]);

            link.appendChild(images[i]);
        }

        $('.product_description_gallery').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    </script>
</div>
<hr>
<div id="shopify-product-reviews" data-id="9249056259"></div>

<div class="full-width full-width--return-link">
    <span class="h1 return-link">Назад к категориям: <?php echo $product["category_full"]; ?></span>
    <!--
    <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/catalog/<?= $product['category'] ?>" class="h1 return-link">
      <svg aria-hidden="true" focusable="false" role="presentation" viewBox="0 0 32 32" class="icon icon-arrow-thin-left"><path fill="#444" d="M10.253 24.134c.27-.27.26-.694 0-.98l-5.764-6.46h24.704c.382 0 .694-.312.694-.693s-.313-.693-.694-.693H4.49l5.754-6.458c.252-.296.278-.704.01-.973s-.74-.286-.99-.01c0 0-6.876 7.553-6.945 7.64s-.21.243-.21.495.14.425.21.495 6.943 7.64 6.943 7.64a.716.716 0 0 0 .99 0z"/></svg>
        Назад к категории
    </a>
    -->

    <!--      <script src="/plugins/gist-toc.js"></script>-->

</div>


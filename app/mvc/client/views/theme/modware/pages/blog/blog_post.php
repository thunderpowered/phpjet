<section class="preloaded section--product">
    <div class="container">
        <div class="col-md-12">
            <div class="row">

                <h1 class="small--text-center"><?php echo $post['title']; ?></h1>

                <div class="grid">
                    <div class="grid__item">
                        <div class="row">
                            <div class="full textForAffixMenu article rte content-block content-block--large">

                                <?php echo $post['body']; ?>

                                <div class="article__footer">
                                    <span class="article__datetime">Опубликовано: <?php echo $post["date"]; ?></span>

                                    <?php if ($tags): ?>

                                        <span class="article__datetime">Теги:
                                            <?php $first = ""; ?>

                                            <!-- That's not good, i know -->
                                            <?php foreach ($tags as $tag): ?><?php echo $first; ?><a
                                                href="<?php echo CloudStore\App\Engine\Core\Router::getHost() . "/blog/tag/" . $tag["id"]; ?>"><?php echo $tag["name"]; ?></a><?php $first = ", "; ?><?php endforeach; ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

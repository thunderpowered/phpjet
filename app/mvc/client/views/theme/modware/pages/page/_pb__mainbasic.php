<?php
/**
 * @var array $bestLastItems
 * @var object $_pb__Data
 */
?>
<!-- MAIN PAGE BEST LAST ITEMS -->
    <div class="p-4 flex-nowrap justify-content-start flex-row d-flex">
        <h1 class="text-left fs-5 fw-bold mb-0 d-block"><?php echo $_pb__Data->props->name ?></h1>
    </div>

    <div class="p-4 main__feed-section">
        <ul class="m-0 p-0 list-unstyled d-flex flex-column justify-content-start align-items-baseline main__feed-list">
            <?php if ($bestLastItems): ?>
            <?php foreach ($bestLastItems as $key => $item): ?>
                <li class="main__feed-item">
                    <div class="feed-item__header">
                        <span class="feed-item__category"><?php echo $item['item_name'] ?></span>
                    </div>
                    <div class="feed-item__main">
                        <h2 class="feed-item__title"><a href="<?php echo $item['item_url']; ?>"><?php echo $item['item_name'] ?></a></h2>
                        <div class="feed-item__description d-flex flex-nowrap justify-content-between align-items-baseline">
                            <?php if ($item['icon']): ?>
                                <div class="feed-item__thumbnail">
                                    <img src="<?php echo $item['icon'] ?>" alt="<?php echo $item['item_name'] ?>">
                                </div>
                            <?php endif; ?>
<!--                            <div class="feed-item__text">--><?php //echo $item['description'] ?><!--</div>-->
                        </div>
                    </div>
                    <div class="feed-item__footer">

                    </div>
                </li>
            <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

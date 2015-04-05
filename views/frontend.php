<?php
/**
 * ViewName: Grid View
 * @var \Carbontwelve\Widgets\PinterestFeedWidget $widget
 * @var string $title
 * @var int $numberOfPins
 * @var array $items
 */
?>

<?php echo $title; ?>

<div class="pinterest-pins-container">
    <?php
    /** @var \Carbontwelve\Widgets\Feeds\FeedItem $item */
    foreach ($items as $item ){ ?>
        <a href="<?php echo $item->href; ?>" title="<?php echo $item->title; ?>" class="pin" style="background-image: url('<?php echo $item->src; ?>')"></a>
    <?php } ?>
</div>

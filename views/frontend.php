<?php
/**
 * ViewName: Grid View.
 *
 * @var \Carbontwelve\Widgets\SocialFeeds\PinterestFeedWidget
 * @var string                                                $title
 * @var int                                                   $numberOfPins
 * @var array                                                 $items
 * @var array                                                 $fields
 * @var string                                                $followSrc
 * @var string                                                $feed_type
 */
?>

<?php echo $title; ?>

<div class="pinterest-pins-container">
    <?php
    /** @var \Carbontwelve\Widgets\SocialFeeds\Feeds\FeedItem $item */
    foreach ($items as $item) {
        ?>
        <a href="<?php echo $item->href;
        ?>" title="<?php echo $item->title;
        ?>" class="pin" style="background-image: url('<?php echo $item->src;
        ?>')"></a>
    <?php 
    } ?>

    <a target="_blank" href="<?php echo $followSrc; ?>" class="follow-link <?php echo $feed_type; ?>">follow me @<?php echo $fields['USERNAME']; ?></a>
</div>

<?php namespace Carbontwelve\Widgets\SocialFeeds\Feeds;

interface FeedInterface
{

    public function __construct( array $uniqueFieldsData = array(), $numberOfItems = 9, $dateFormat = 'M d, Y', $TTL = 60 );

    public function getFeedSrc();

    public function setFeedSrc( $feedSrc );

    public function setTTL( $TTL );

    public function setNumberOfItems ( $numberOfItems );

    public function setDateFormat( $dateFormat );

    public function getName();

    public function getUniqueFields();

    public function hydrate( array $fields );

    public function execute();

}
<?php

namespace Carbontwelve\Widgets\SocialFeeds\Feeds\FeedScrapers;

/** @noinspection PhpIncludeInspection */
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'AbstractFeed.php';
/** @noinspection PhpIncludeInspection */
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'FeedInterface.php';

use Carbontwelve\Widgets\SocialFeeds\Feeds\AbstractFeed;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedInterface;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedItem;

class PinterestUserActivityFeed extends AbstractFeed implements FeedInterface
{
    /**
     * The Public facing name of this feed.
     *
     * @var string
     */
    protected $name = 'Pinterest User Activity Feed';

    /**
     * The feed source url.
     *
     * @var string
     */
    protected $feedSrc = 'https://pinterest.com/%USERNAME%/feed.rss';

    /**
     * The follow user source url.
     *
     * @var string
     */
    protected $followSrc = 'https://pinterest.com/%USERNAME%/';

    /**
     * Unique fields for the source url, this is so that we may have different inputs for the widget
     * depending upon which source feed the user has defined.
     *
     * @var array
     */
    protected $uniqueFields = [
        'USERNAME' => 'Your pinterest username',
    ];

    public function execute()
    {
        include_once ABSPATH.DIRECTORY_SEPARATOR.WPINC.'/feed.php';
        /** @noinspection PhpIncludeInspection */
        include_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'FeedItem.php';

        $feedSrc = str_replace('%USERNAME%', $this->feedData['USERNAME'], $this->feedSrc);
        $feed = fetch_feed($feedSrc);

        if ($feed instanceof \WP_Error) {
            throw new \Exception('Fetching the feed failed');
        }

        // Set Maximum time before we give up requesting feed data
        $feed->set_timeout($this->TTL);

        $maxItems = $feed->get_item_quantity($this->numberOfItems);
        $items = $feed->get_items(0, $maxItems);
        $output = [];

        /** @var \SimplePie_Item $item */
        foreach ($items as $item) {
            $tmp = new FeedItem();
            $tmp->title = $item->get_title();
            $tmp->date = $item->get_date($this->dateFormat);
            $tmp->href = $item->get_permalink();
            $tmp->src = null;
            $tmp->content = $item->get_content();

            // Identify the <img> tag if any found
            preg_match('/<img[^>]+>/i', $tmp->content, $imgTag);
            if (!isset($imgTag[0])) {
                continue;
            } else {
                $imgTag = $imgTag[0];
            }

            // Identify the src property of the <img> tag if any found
            preg_match('/(src)=("[^"]*")/i', $imgTag, $imgSrc);
            if (!isset($imgSrc[2])) {
                continue;
            } else {
                $tmp->src = str_replace('"', '', $imgSrc[2]);
            }

            $output[] = $tmp;
        }

        return $output;
    }
}

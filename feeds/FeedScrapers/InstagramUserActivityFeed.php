<?php

namespace Carbontwelve\Widgets\SocialFeeds\Feeds\FeedScrapers;

/** @noinspection PhpIncludeInspection */
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'AbstractFeed.php';
/** @noinspection PhpIncludeInspection */
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'FeedInterface.php';

use Carbontwelve\Widgets\SocialFeeds\Feeds\AbstractFeed;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedInterface;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedItem;

/**
 * Class InstagramUserActivityFeed.
 *
 * @author Simon Dann <simon.dann@gmail.com>
 */
class InstagramUserActivityFeed extends AbstractFeed implements FeedInterface
{
    /**
     * The Public facing name of this feed.
     *
     * @var string
     */
    protected $name = 'Instagram User Activity Feed';

    /**
     * The feed source url.
     *
     * @var string
     */
    protected $feedSrc = 'http://instagram.com/%USERNAME%';

    /**
     * The follow user source url.
     *
     * @var string
     */
    protected $followSrc = 'http://instagram.com/%USERNAME%';

    /**
     * Unique fields for the source url, this is so that we may have different inputs for the widget
     * depending upon which source feed the user has defined.
     *
     * @var array
     */
    protected $uniqueFields = [
        'USERNAME' => 'Your instagram username',
    ];

    /**
     * Returns feed data as an array, if the feed returned by getFeedData is null then
     * an exception is thrown.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function execute()
    {
        if ($feedData = $this->getFeedData()) {
            return $feedData;
        }

        throw new \Exception('Problem with executing Instagram User Activity Feed');
    }

    /**
     * Returns null on feed error, otherwise it will provide an array with n items, where n is the number of items that
     * the widget it configured to show.
     *
     * @return array|null
     */
    private function getFeedData()
    {
        /** @noinspection PhpIncludeInspection */
        include_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'FeedItem.php';

        $feedSrc = str_replace('%USERNAME%', $this->feedData['USERNAME'], $this->feedSrc);
        $response = wp_remote_get($feedSrc, ['sslverify' => false, 'timeout' => $this->TTL]);

        // If something bad happened
        if (is_wp_error($response)) {
            return;
        }

        if ($response['response']['code'] == 200) {
            $json = str_replace('window._sharedData = ', '', strstr($response['body'], 'window._sharedData = '));
            $json = strstr($json, '</script>', true);
            $json = rtrim($json, ';');

            $json = json_decode($json, true);

            if ((function_exists('json_last_error') && json_last_error() !== JSON_ERROR_NONE) || (!is_array($json))) {
                return;
            }

            $userMedia = isset($json['entry_data']['ProfilePage'][0]['user']['media']['nodes']) ? $json['entry_data']['ProfilePage'][0]['user']['media']['nodes'] : [];

            if (empty($userMedia)) {
                return;
            }

            $foundMedia = [];

            foreach ($userMedia as $media) {
                // If we have enough items then we should break the foreach
                if (count($foundMedia) >= $this->numberOfItems) {
                    break;
                }

                $tmp = new FeedItem();
                $tmp->title = 'Click to view more';
                $tmp->date = date($this->dateFormat, (int) $media['date']);
                $tmp->href = 'https://instagram.com/p/'.$media['code'];
                $tmp->src = $media['display_src'];
                $foundMedia[] = $tmp;
            }

            return $foundMedia;
        }

        return;
    }
}

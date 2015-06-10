<?php namespace Carbontwelve\Widgets\SocialFeeds\Feeds\FeedScrapers;

/** @noinspection PhpIncludeInspection */
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AbstractFeed.php');
/** @noinspection PhpIncludeInspection */
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'FeedInterface.php');

use Carbontwelve\Widgets\SocialFeeds\Feeds\AbstractFeed;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedInterface;
use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedItem;

class InstagramUserActivityFeed extends AbstractFeed implements FeedInterface
{
    /**
     * The Public facing name of this feed
     *
     * @var string
     */
    protected $name    = 'Instagram User Activity Feed';

    /**
     * The feed source url
     *
     * @var string
     */
    protected $feedSrc = 'http://instagram.com/%USERNAME%';

    /**
     * The follow user source url
     * @var string
     */
    protected $followSrc = 'http://instagram.com/%USERNAME%';

    /**
     * Unique fields for the source url, this is so that we may have different inputs for the widget
     * depending upon which source feed the user has defined
     *
     * @var array
     */
    protected $uniqueFields  = array(
        'USERNAME' => 'Your instagram username'
    );

    public function execute()
    {
        if ( $feedData = $this->getFeedData() )
        {
            return $feedData;
        }

        throw new \Exception('Problem with executing Instagram User Activity Feed');
    }

    private function getFeedData()
    {
        /** @noinspection PhpIncludeInspection */
        include_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'FeedItem.php');

        $feedSrc  = str_replace('%USERNAME%', $this->feedData['USERNAME'], $this->feedSrc);
        $response = wp_remote_get( $feedSrc, array( 'sslverify' => false, 'timeout' => $this->TTL ) );

        // If something bad happened
        if ( is_wp_error($response) )
        {
            return null;
        }

        if ( $response['response']['code'] == 200 ) {

            $json = str_replace( 'window._sharedData = ', '', strstr( $response['body'], 'window._sharedData = ' ) );
            $json = strstr( $json, '</script>', true );
            $json = rtrim( $json, ';' );

            $json = json_decode($json, true);

            if ( ( function_exists( 'json_last_error' ) && json_last_error() !== JSON_ERROR_NONE ) || ( ! is_array($json) ) )
            {
                return null;
            }

            $userMedia = isset( $json['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ? $json['entry_data']['ProfilePage'][0]['user']['media']['nodes'] : array();

            if (empty($userMedia))
            {
                return null;
            }

            $foundMedia = array();

            foreach ( $userMedia as $media ) {
                // If we have enough items then we should break the foreach
                if (count($foundMedia) >= $this->numberOfItems)
                {
                    break;
                }

                $tmp = new FeedItem();
                $tmp->title   = 'Click to view more';
                $tmp->date    = date( $this->dateFormat, (int) $media['date']);
                $tmp->href    = 'https://instagram.com/p/' .$media['code'];
                $tmp->src     = $media['display_src'];
                $foundMedia[] = $tmp;
            }

            return $foundMedia;
        }

        return null;
    }
}

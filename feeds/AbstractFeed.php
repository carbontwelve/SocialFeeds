<?php

namespace Carbontwelve\Widgets\SocialFeeds\Feeds;

require_once __DIR__.DIRECTORY_SEPARATOR.'FeedInterface.php';

abstract class AbstractFeed implements FeedInterface
{
    /**
     * The Public facing name of this feed.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The feed source url.
     *
     * @var string
     */
    protected $feedSrc = '';

    /**
     * The follow user source url.
     *
     * @var string
     */
    protected $followSrc = '';

    /**
     * Unique fields for the source url, this is so that we may have different inputs for the widget
     * depending upon which source feed the user has defined.
     *
     * @var array
     */
    protected $uniqueFields = [];

    /**
     * Feed data, must match the fields in $this->uniqueFields.
     *
     * @var array
     */
    protected $feedData = [];

    /**
     * How long we should wait for the feed provider before giving up.
     *
     * @var int
     */
    protected $TTL = 60;

    /**
     * Total number of feed items to grab on execute.
     *
     * @var int
     */
    protected $numberOfItems = 9;

    /**
     * Format to be used for formatting dates.
     *
     * @var string
     */
    protected $dateFormat = 'M d, Y';

    public function __construct(array $uniqueFieldsData = [], $numberOfItems = 9, $dateFormat = 'M d, Y', $TTL = 60)
    {
        $this->setFieldData($uniqueFieldsData);
        $this->setNumberOfItems($numberOfItems);
        $this->setDateFormat($dateFormat);
        $this->setTTL($TTL);
    }

    /**
     * Set the TTL value.
     *
     * @param int $TTL
     */
    public function setTTL($TTL)
    {
        $this->TTL = (int) $TTL;
    }

    /**
     * Set the number of items to grab on execute.
     *
     * @param int $numberOfItems
     */
    public function setNumberOfItems($numberOfItems)
    {
        $this->numberOfItems = $numberOfItems;
    }

    /**
     * Set the format to be used for formatting dates.
     *
     * @param string $dateFormat
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Return the feed source url.
     *
     * @return string
     */
    public function getFeedSrc()
    {
        return $this->feedSrc;
    }

    /**
     * Set the feed source url.
     *
     * @param string $feedSrc
     */
    public function setFeedSrc($feedSrc)
    {
        $this->feedSrc = (string) $feedSrc;
    }

    /**
     * Returns the public facing name for this feed.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the unique fields for this feed type.
     *
     * @return array
     */
    public function getUniqueFields()
    {
        return $this->uniqueFields;
    }

    /**
     * Get the follow link for this feed type.
     *
     * @return string
     */
    public function getFollowSrc()
    {
        return str_replace('%USERNAME%', $this->feedData['USERNAME'], $this->followSrc);
    }

    /**
     * Sets the $feedData array from input; input must match keys to the $uniqueFieldsData property otherwise
     * things will go wrong.
     *
     * @param array $uniqueFieldsData
     */
    protected function setFieldData(array $uniqueFieldsData)
    {
        foreach ($this->uniqueFields as $key => $value) {
            if (!isset($uniqueFieldsData[$key])) {
                continue;
            }

            $this->feedData[$key] = $uniqueFieldsData[$key];
        }
    }

    public function hydrate(array $fields)
    {
        $this->setFieldData($fields);
    }

    abstract public function execute();
}

<?php namespace Carbontwelve\Widgets\SocialFeeds;

/*
Plugin Name:        Social Feeds Widget
Plugin URI:         http://www.photogabble.co.uk
Version:            1.0.0
Description:        Various social feeds for you to template
Author:             Simon Dann
Author URI:         http://www.photogabble.co.uk

License:            MIT
License URI:        http://opensource.org/licenses/MIT
*/

/** @noinspection PhpIncludeInspection */
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'feeds' . DIRECTORY_SEPARATOR . 'FeedFactory.php');
/** @noinspection PhpIncludeInspection */
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'View.php');

use Carbontwelve\Widgets\SocialFeeds\Feeds\FeedFactory;
use Carbontwelve\Widgets\SocialFeeds\Libs\View;

/**
 * Class PinterestFeedWidget
 * @package Carbontwelve\Widgets\SocialFeeds
 */
class PinterestFeedWidget extends \WP_Widget {

    /**
     * Plugin version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Plugin Slug, used for caching
     *
     * @var string
     */
    private $slug = 'Carbontwelve_PinterestFeed_Widget';

    /**
     * Path where we are to look for views
     *
     * @var string
     */
    private $viewsPath;

    /**
     * @var string
     */
    private $defaultTemplate;

    /**
     * @var FeedFactory
     */
    private $feedFactory;

    /**
     * Register widget with WordPress
     */
    public function __construct(){
        parent::__construct(
            $this->slug,
            __('Pinterest Feed', 'carbontwelve'),
            array(
                'description' => __('Display your pinterest feed.','carbontwelve')
            )
        );

        $this->feedFactory     = new FeedFactory();
        $this->viewsPath       = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
        $this->defaultTemplate = base64_encode( $this->viewsPath . 'frontend.php');

        // Enqueue Plugin Styles and scripts for admin pages
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance )
    {

        $dataProvider = $this->feedFactory->getFeed( $instance['feed'] );
        $dataProvider->setNumberOfItems( $instance['numberOfPins'] );
        $dataProvider->hydrate( $instance['metaFields'][$instance['feed']] );

        $view = new View( base64_decode($instance['template']) );

        // If the loaded view does not exist then roll back to a sane default
        if ( ! $view->exists() )
        {
            $view  = new View( base64_decode($this->defaultTemplate) );
        }

        $output  = $args['before_widget'];
        $output .= $view->render(array(
            'widget'        => $this,
            'title'         => $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'],
            'items'         => $dataProvider->execute()
        ));
        $output .= $args['after_widget'];

        echo $output;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return void
     */
    public function form ( $instance )
    {

        $view = new View( $this->viewsPath . 'backend.php');
        echo $view->render(array(
            'widget'             => $this,
            'title'              => ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Title', 'carbontwelve' ),
            'username'           => ( isset( $instance[ 'username' ] ) ) ? $instance[ 'username' ] : '',
            'numberOfPins'       => (int) ( isset( $instance[ 'numberOfPins' ] ) ) ? $instance[ 'numberOfPins' ] : 6,
            'template'           => ( isset( $instance[ 'template' ] ) ) ? $instance[ 'template' ] : $this->defaultTemplate,
            'availableTemplates' => $this->identifyWidgetViews(),
            'availableFeeds'     => $this->feedFactory->getFeedsForDropDown(),
            'feed'               => ( isset( $instance[ 'feed' ] ) ) ? $instance[ 'feed' ] : '',
            'feedFields'         => $this->feedFactory->getFeedFields( $instance, $this )
        ));
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $this->clearCache();

        $output = array(
            'title'         => ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '',
            'numberOfPins'  => (int) ( ! empty( $new_instance['numberOfPins'] ) ) ? strip_tags( $new_instance['numberOfPins'] ) : 6,
            'template'      => ( isset( $new_instance[ 'template' ] ) ) ? $new_instance[ 'template' ] : $this->defaultTemplate,
            'feed'          => ( isset( $new_instance[ 'feed' ] ) ) ? $new_instance[ 'feed' ] : '',
            'metaFields'    => array()
        );

        // Parse Meta Fields
        foreach ($new_instance['metaFields'] as $feedName => $fields)
        {
            if ( ! isset($output['metaFields'][$feedName]) )
            {
                $output['metaFields'][$feedName] = array();
            }
            foreach ($fields as $key => $value)
            {
                $output['metaFields'][$feedName][$key] = $value;
            }
        }

        return $output;
    }

    /**
     * Clears the cache for this widget
     * @return bool
     */
    public function clearCache() {
        return wp_cache_delete( $this->slug, 'widget' );
    }

    public function enqueueAdminScripts( $hookName )
    {
        if ( $hookName !== 'widgets.php' ){ return; }
        wp_enqueue_script( 'c12-social-admin-script', plugins_url( 'assets/js/c12-social-admin.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
    }

    private function identifyWidgetViews()
    {
        $output = array();

        $views = array_filter(scandir($this->viewsPath), function($value){
            if ($value === '.' || $value === '..' || strpos($value, 'backend') !== false ){ return false; }
            return true;
        });

        foreach ( $views as $view )
        {
            $fileContent = file_get_contents($this->viewsPath . $view );
            if ( $fileContent === false){ continue; }
            preg_match('/ViewName: (.*)/', $fileContent, $matches);
            $output[ base64_encode( $this->viewsPath . $view ) ] = isset($matches[1]) ? $matches[1] : 'Unknown';
        }

        return $output;
    }
}

add_action( 'widgets_init', function(){
    register_widget( __NAMESPACE__ . '\\PinterestFeedWidget' );
});
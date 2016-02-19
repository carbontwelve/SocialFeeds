<?php
/**
 * @var \Carbontwelve\Widgets\RecentPostsWidget
 * @var string                                  $title
 * @var int                                     $numberOfPins
 * @var string                                  $username
 * @var array                                   $availableTemplates
 * @var string                                  $template
 * @var array                                   $availableFeeds
 * @var string                                  $feed
 * @var array                                   $feedFields
 */
?>
<p>
    <label for="<?php echo $widget->get_field_id('title'); ?>"><?php echo __('Title:'); ?>
        <input type="text" id="<?php echo $widget->get_field_id('title'); ?>" name="<?php echo $widget->get_field_name('title') ?>" value="<?php echo esc_attr($title); ?>" class="widefat" />
    </label>
</p>

<p>
    <label for="<?php echo $widget->get_field_id('feed'); ?>"><?php echo __('Feed:'); ?>
        <select id="<?php echo $widget->get_field_id('feed'); ?>" name="<?php echo $widget->get_field_name('feed') ?>" class="widefat additionalFieldSelector">
            <?php foreach ($availableFeeds as $feedKey => $feedTitle) {
    ?>
                <option value="<?php echo $feedKey;
    ?>" <?php if ($feedKey === $feed) {
    echo 'selected';
}
    ?>><?php echo $feedTitle;
    ?></option>
            <?php 
} ?>
        </select>
    </label>
</p>

<?php
    foreach ($feedFields as $feedName => $fields) {
        $style = 'display:none;';

        if ($feedName === $feed) {
            $style = '';
        }
        ?>
    <div class="additionalFields <?php echo $feedName;
        ?>" style="<?php echo $style;
        ?>">
        <?php foreach ($fields as $field) {
    ?>
            <p>
                <label for="<?php echo $field['id'] ?>"><?php echo $field['title'];
    ?>
                    <input type="text" id="<?php echo $field['id'];
    ?>" name="<?php echo $field['name'];
    ?>" value="<?php echo esc_attr($field['value']);
    ?>" class="widefat" />
                </label>
            </p>
        <?php 
}
        ?>
    </div>
<?php 
    } ?>

<p>
    <label for="<?php echo $widget->get_field_id('numberOfPins'); ?>"><?php echo __('Number of items to show:'); ?>
        <input type="text" id="<?php echo $widget->get_field_id('numberOfPins'); ?>" name="<?php echo $widget->get_field_name('numberOfPins') ?>" value="<?php echo esc_attr($numberOfPins); ?>" class="widefat" />
    </label>
</p>

<p>
    <label for="<?php echo $widget->get_field_id('template'); ?>"><?php echo __('Template:'); ?>
        <select id="<?php echo $widget->get_field_id('template'); ?>" name="<?php echo $widget->get_field_name('template') ?>" class="widefat">
            <?php foreach ($availableTemplates as $templateKey => $templateTitle) {
    ?>
                <option value="<?php echo $templateKey;
    ?>" <?php if ($templateKey === $template) {
    echo 'selected';
}
    ?>><?php echo $templateTitle;
    ?></option>
            <?php 
} ?>
        </select>
    </label>
</p>

<?php 
class Elementor_Dynamic_Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag
{

    public function get_name()
    {
        return 'dynamic-image-tag'; // Unique ID for the tag
    }

    public function get_title()
    {
        return __('Dynamic Image', 'fiverr-market');
    }

    public function get_group()
    {
        return 'site'; // Group it under the 'site' type in Elementor
    }

    public function get_categories()
    {
        return [\Elementor\Modules\DynamicTags\Module::MEDIA_CATEGORY]; // Set this to the image category
    }

    protected function register_controls()
    {
        // Optionally, you can add custom controls here if needed
    }

    public function get_value(array $options = [])
    {
        $post_id = get_the_ID(); // Get current post ID
        $attachment_id = get_post_meta($post_id, 'cover_image', true); // Fetch the custom meta field 'cover_image'

        if (! $attachment_id) {
            return 'nothing is here'; // Return an empty string if no attachment is found
        }

        // Get the image URL from the attachment ID
        $image_url = wp_get_attachment_url($attachment_id);

       // Return the image data array that Elementor expects
       return [
        'id' => $attachment_id,          // The attachment ID
        'url' => $image_url[0],              // The image URL
        'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true), // The image alt text
    ];
    }

    public function render()
    {
        // The render method should not echo anything in this case because Elementor will process the URL
        return;
    }
}

// Register the new dynamic tag
function register_custom_dynamic_tag($dynamic_tags)
{
    $dynamic_tags->register_tag('Elementor_Dynamic_Image_Tag');
}
add_action('elementor/dynamic_tags/register', 'register_custom_dynamic_tag');



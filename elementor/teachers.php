<?php

class Elementor_fiverr_market_teacher_loop extends \Elementor\Widget_Base
{

    public function get_name()
    {
        return 'teacher-loop';
    }

    public function get_title()
    {
        return esc_html__('Teaceher Loop', 'fiverr-market');
    }

    public function get_icon()
    {
        return 'fab fa-teamspeak';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    public function get_keywords()
    {
        return ['teacher', 'loop'];
    }

    protected function render()
    {
?>
        <div class="pre-loader">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <div class="gigs">

            <?php
            // Get all users with the role of 'teacher'
            $args = array(
                'role' => 'teacher', // Specify the user role
            );

            $user_query = new WP_User_Query($args);
            $teachers = $user_query->get_results(); // Get the results

            if (!empty($teachers)) {

                foreach ($teachers as $teacher) {
                    $name = $teacher->display_name; // Get the display name of the user
                    $profile_Id =  get_user_meta($teacher->ID, 'profile_picture', true); // Replace 'your_meta_key' with your actual meta key
                    $profile = wp_get_attachment_url($profile_Id);
                    $cover_image_id = get_user_meta($teacher->ID, 'cover_image', true); // Replace 'your_meta_key' with your actual meta key
                    $cover_image = wp_get_attachment_url($cover_image_id);
                    $expertice = get_user_meta($teacher->ID, 'expertice', true); // Replace 'your_meta_key' with your actual meta key
                    $per_hour = get_user_meta($teacher->ID, 'per_hour', true) ?  get_user_meta($teacher->ID, 'per_hour', true) : 30;
                    $reviews = get_user_meta($teacher->ID, 'teacher_reviews', true);
                    $reviews = $reviews ? json_decode($reviews, true) : [];
                    $total_review = count($reviews);
                    $total_star = 0;
                    if (!empty($reviews)) {
                       
                        foreach ($reviews as $review) {
                            $total_star += $review['rating'];
                        }
                    }
                    // Calculate average score if there are reviews
                    $average_score = $total_review > 0 ? $total_star / $total_review : 0; // Avoid division by zero

            ?>
                    <div class="gig-card">
                        <div class="gig-image">
                            <div class="gig-price">
                                <span> <strong>$<?php echo $per_hour; ?></strong>/hr</span>
                            </div>
                            <img
                                src="<?php echo $cover_image; ?>"
                                alt="Gig Image" />
                            <img
                                src="<?php echo $profile; ?>"
                                alt="Gig Image"
                                class="profile" />
                        </div>
                        <div class="gig-info">
                            <h2 class="gig-title"><?php echo $name; ?></h2>
                            <div class="subjects">

                                <span><?php echo $expertice; ?></span>

                            </div>
                            <div class="gig-rating">

                                <?php

                                // Loop through 5 stars
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($average_score)) {
                                        // Full star
                                        echo '<i class="fas fa-star"></i>'; // Font Awesome full star
                                    } elseif ($i == ceil($average_score)) {
                                        // Half star
                                        echo '<i class="fas fa-star-half-alt"></i>'; // Font Awesome half star
                                    } else {
                                        // Empty star
                                        echo '<i class="far fa-star"></i>'; // Font Awesome empty star
                                    }
                                }
                                ?>

                                (<?php echo $total_review; ?> Reviews)</div>

                            <div class="gig-btn">
                                <a href="<?php echo get_author_posts_url($teacher->ID); ?>">Get Started <i class="fas fa-right-long ms-2"></i></a>
                            </div>
                        </div>
                    </div>


    <?php



}
} else {
    echo 'No teachers found.';
}
?>


        </div>
<?php
    }
}

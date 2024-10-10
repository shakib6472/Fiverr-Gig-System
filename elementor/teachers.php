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

        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="filter-sidebar">
                        <h5>Filter by:</h5>
                        <form id="filterForm" method="GET" action="">

                            <!-- Grade Filter -->
                            <div class="mb-3">
                                <label for="grade">Grade</label>
                                <select class="form-select filter-select" name="grade" id="grade">
                                    <option value="">All Grades</option>
                                    <option value="primary" <?php if (isset($_GET['grade']) && $_GET['grade'] == 'primary') echo 'selected'; ?>>Primary</option>
                                    <option value="secondary" <?php if (isset($_GET['grade']) && $_GET['grade'] == 'secondary') echo 'selected'; ?>>Secondary</option>
                                    <option value="high" <?php if (isset($_GET['grade']) && $_GET['grade'] == 'high') echo 'selected'; ?>>High School</option>
                                </select>
                            </div>

                            <!-- Region Filter -->
                            <div class="mb-3">
                                <label for="region">Region</label>
                                <select class="form-select filter-select" name="region" id="region">
                                    <option value="">All Regions</option>
                                    <option value="north" <?php if (isset($_GET['region']) && $_GET['region'] == 'north') echo 'selected'; ?>>North</option>
                                    <option value="south" <?php if (isset($_GET['region']) && $_GET['region'] == 'south') echo 'selected'; ?>>South</option>
                                    <option value="east" <?php if (isset($_GET['region']) && $_GET['region'] == 'east') echo 'selected'; ?>>East</option>
                                    <option value="west" <?php if (isset($_GET['region']) && $_GET['region'] == 'west') echo 'selected'; ?>>West</option>
                                </select>
                            </div>

                            <!-- Rating Filter -->
                            <div class="mb-3">
                                <label for="rating">Rating</label>
                                <select class="form-select filter-select" name="rating" id="rating">
                                    <option value="">All Ratings</option>
                                    <option value="4" <?php if (isset($_GET['rating']) && $_GET['rating'] == '4') echo 'selected'; ?>>4 stars and up</option>
                                    <option value="3" <?php if (isset($_GET['rating']) && $_GET['rating'] == '3') echo 'selected'; ?>>3 stars and up</option>
                                    <option value="2" <?php if (isset($_GET['rating']) && $_GET['rating'] == '2') echo 'selected'; ?>>2 stars and up</option>
                                    <option value="1" <?php if (isset($_GET['rating']) && $_GET['rating'] == '1') echo 'selected'; ?>>1 star and up</option>
                                </select>
                            </div>

                            <!-- Expertise Filter -->
                            <div class="mb-3">
                                <label for="expertise">Expertise</label>
                                <select class="form-select filter-select" name="expertise" id="expertise">
                                    <option value="">All Expertise</option>
                                    <option value="math" <?php if (isset($_GET['expertise']) && $_GET['expertise'] == 'math') echo 'selected'; ?>>Math</option>
                                    <option value="science" <?php if (isset($_GET['expertise']) && $_GET['expertise'] == 'science') echo 'selected'; ?>>Science</option>
                                    <option value="english" <?php if (isset($_GET['expertise']) && $_GET['expertise'] == 'english') echo 'selected'; ?>>English</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>



                <div class="pre-loader">
                    <div class="lds-ellipsis">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <div class="col-md-9">
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


                                $args = array(
                                    'post_type' => 'teacher', // Post-type key
                                    'posts_per_page' => 1, // -1 retrieves all posts
                                    'author' => $teacher->ID,
                                );

                                $query = new WP_Query($args);

                                if ($query->have_posts()) {
                                    while ($query->have_posts()) {
                                        $query->the_post();
                                        $post_id = get_the_ID();
                                        $teacher_link = get_permalink($post_id);
                                    }

                                    // Restore original post data
                                    wp_reset_postdata();
                                } else {
                                    // No posts found
                                    continue;
                                }
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
                                            <a href="<?php echo $teacher_link; ?>">Get Started <i class="fas fa-right-long ms-2"></i></a>
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
                </div>
            </div>
        </div>
<?php
    }
}

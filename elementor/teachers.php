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
            <div class="sidebar">
                <h5>Filter by:</h5>
                <form id="filterForm" method="GET" action="">
                    <!-- Grade Filter -->
                    <div class="mb-3">
                        <label for="grade">Grade</label>
                        <select class="form-select filter-select" name="grade" id="grade">
                            <option value="">All Grades</option>
                            <?php 
                            $grades = get_terms(array('taxonomy' => 'grade', 'hide_empty' => false));
                            foreach ($grades as $grade) {
                                $selected = isset($_GET['grade']) && $_GET['grade'] == $grade->slug ? 'selected' : '';
                                echo "<option value='{$grade->slug}' {$selected}>{$grade->name} ({$grade->count})</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Region Filter -->
                    <div class="mb-3">
                        <label for="region">Region</label>
                        <select class="form-select filter-select" name="region" id="region">
                            <option value="">All Regions</option>
                            <?php 
                            $regions = get_terms(array('taxonomy' => 'region', 'hide_empty' => false));
                            foreach ($regions as $region) {
                                $selected = isset($_GET['region']) && $_GET['region'] == $region->slug ? 'selected' : '';
                                echo "<option value='{$region->slug}' {$selected}>{$region->name} ({$region->count})</option>";
                            }
                            ?>
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
                        <label for="expertise">Subject</label>
                        <select class="form-select filter-select" name="expertise" id="expertise">
                            <option value="">All Subjects</option>
                            <?php 
                            $expertises = get_terms(array('taxonomy' => 'expertise', 'hide_empty' => false));
                            foreach ($expertises as $expertise) {
                                $selected = isset($_GET['expertise']) && $_GET['expertise'] == $expertise->slug ? 'selected' : '';
                                echo "<option value='{$expertise->slug}' {$selected}>{$expertise->name} ({$expertise->count})</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-9">
            <div class="gigs">

                <?php
                // Capture filter values
                $grade = isset($_GET['grade']) ? sanitize_text_field($_GET['grade']) : '';
                $region = isset($_GET['region']) ? sanitize_text_field($_GET['region']) : '';
                $expertise = isset($_GET['expertise']) ? sanitize_text_field($_GET['expertise']) : '';
                $rating = isset($_GET['rating']) ? intval($_GET['rating']) : '';

                // Build WP_Query
                $args = array(
                    'post_type' => 'teacher',
                    'posts_per_page' => -1, // Show all matching teachers
                    'tax_query' => array('relation' => 'AND'),
                    'meta_query' => array('relation' => 'AND')
                );

                // Taxonomy-based filtering
                if ($grade) {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'grade',
                        'field'    => 'slug',
                        'terms'    => $grade,
                    );
                }

                if ($region) {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'region',
                        'field'    => 'slug',
                        'terms'    => $region,
                    );
                }

                if ($expertise) {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'expertise',
                        'field'    => 'slug',
                        'terms'    => $expertise,
                    );
                }

                // Meta-based filtering for rating
                if ($rating) {
                    $args['meta_query'][] = array(
                        'key'     => 'average_rating', // Ensure this key matches how you're storing the rating
                        'value'   => $rating,
                        'compare' => '>=', // We're looking for ratings equal to or greater than the selected value
                        'type'    => 'NUMERIC'
                    );
                }

                // Execute query
                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();

                        // Fetch meta fields, e.g., profile picture, reviews, etc.
                        $name = get_the_title();
                        $profile = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                        $cover_image_id = get_post_meta(get_the_ID(), 'cover_image', true);
                        $cover_image = wp_get_attachment_url($cover_image_id);
                        $reviews = get_post_meta(get_the_ID(), 'teacher_reviews', true);
                        $reviews = $reviews ? json_decode($reviews, true) : [];
                        $total_review = count($reviews);
                        $total_star = array_sum(array_column($reviews, 'rating'));
                        $average_score = $total_review > 0 ? $total_star / $total_review : 0;

                        // Display the teacher card
                        ?>
                        <div class="gig-card">
                            <div class="gig-image">
                                <img src="<?php echo $cover_image; ?>" alt="Cover Image" />
                                <img src="<?php echo $profile; ?>" alt="Profile Image" class="profile" />
                            </div>
                            <div class="gig-info">
                                <h2 class="gig-title"><?php echo $name; ?></h2>
                                <div class="subjects"><span><?php echo get_the_terms(get_the_ID(), 'expertise')[0]->name; ?></span></div>
                                <div class="gig-rating">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= floor($average_score)) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif ($i == ceil($average_score)) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                    (<?php echo $total_review; ?> Reviews)
                                </div>
                                <div class="gig-btn"><a href="<?php the_permalink(); ?>">Get Started <i class="fas fa-right-long ms-2"></i></a></div>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
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

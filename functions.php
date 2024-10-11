<?php
// Add the 'teacher' role with specific capabilities
add_role('teacher', 'Teacher', array(
    'read' => true, // Can read posts and pages
    'edit_posts' => true, // Can edit their own posts
    'delete_posts' => true, // Can delete their own posts
    'publish_posts' => true, // Can publish their own posts
    'upload_files' => true, // Can upload files
));

function register_teacher_taxonomies(){
    // Grade Taxonomy
    register_taxonomy(
        'grade',
        'teacher',
        array(
            'labels' => array(
                'name' => 'Grades',
                'singular_name' => 'Grade',
            ),
            'public' => true,
            'hierarchical' => true, // Like categories
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true, // For Gutenberg editor support
            'rewrite' => array('slug' => 'grade'),
        )
    );

    // Region Taxonomy
    register_taxonomy(
        'region',
        'teacher',
        array(
            'labels' => array(
                'name' => 'Regions',
                'singular_name' => 'Region',
            ),
            'public' => true,
            'hierarchical' => true, // Like categories
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'region'),
        )
    );

    // Expertise Taxonomy
    register_taxonomy(
        'expertise',
        'teacher',
        array(
            'labels' => array(
                'name' => 'Expertise',
                'singular_name' => 'Expertise',
            ),
            'public' => true,
            'hierarchical' => true, // Like tags
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'expertise'),
        )
    );
}
// Hook into the 'init' action to register taxonomies
add_action('init', 'register_teacher_taxonomies', 0);

// Add Review Function


// Hook into init action to add reviews for teacher post type
// add_action('init', 'add_reviews_example');
function add_reviews_example()
{
    // Replace with the actual teacher post ID
    $teacher_post_id = 18733;

    // Review data
    $review_data = [
        'reviewer_name' => 'Shakib',
        'rating' => 5,
        'comment' => 'Great teacher! Thanks for the collaboration',
        'date' => current_time('mysql'),
        'image' => 'https://cdn-icons-png.flaticon.com/512/2919/2919906.png'
    ];

    // Make sure the teacher post exists before adding a review
    if (get_post($teacher_post_id) && get_post_type($teacher_post_id) == 'teacher') {
        add_review_to_teacher($teacher_post_id, $review_data);

    } else {
        error_log("Teacher post with ID $teacher_post_id does not exist.");
    }
}

// Function to add a review to a teacher post and update the average rating
function add_review_to_teacher($teacher_post_id, $review_data)
{
    // Get existing reviews from the teacher post
    $existing_reviews = get_post_meta($teacher_post_id, 'teacher_reviews', true);
    $existing_reviews = $existing_reviews ? json_decode($existing_reviews, true) : [];

    // Add the new review to the existing reviews
    $existing_reviews[] = $review_data;

    // Update the post meta with the new reviews
    update_post_meta($teacher_post_id, 'teacher_reviews', json_encode($existing_reviews));

    // Calculate the new average rating
    calculate_and_update_average_rating($teacher_post_id, $existing_reviews);
}

// Function to calculate and update the average rating
function calculate_and_update_average_rating($teacher_post_id, $reviews)
{
    $total_rating = 0;
    $total_reviews = count($reviews);

    // Sum up all ratings
    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
    }

    // Calculate the average rating
    $average_rating = ($total_reviews > 0) ? $total_rating / $total_reviews : 0;

    // Update the average rating in the post meta
    update_post_meta($teacher_post_id, 'average_rating', $average_rating);

    error_log("Updated average rating for teacher post ID $teacher_post_id to $average_rating.");
}



function create_teacher_student_message_cpt()
{
    // Labels for Messages Post Type
    $message_labels = array(
        'name' => _x('Messages', 'Post type general name', 'fiverr-market'),
        'singular_name' => _x('Message', 'Post type singular name', 'fiverr-market'),
        'menu_name' => _x('Messages', 'Admin Menu text', 'fiverr-market'),
        'name_admin_bar' => _x('Message', 'Add New on Toolbar', 'fiverr-market'),
        'add_new' => __('Add New', 'fiverr-market'),
        'add_new_item' => __('Add New Message', 'fiverr-market'),
        'new_item' => __('New Message', 'fiverr-market'),
        'edit_item' => __('Edit Message', 'fiverr-market'),
        'view_item' => __('View Message', 'fiverr-market'),
        'all_items' => __('All Messages', 'fiverr-market'),
        'search_items' => __('Search Messages', 'fiverr-market'),
        'not_found' => __('No messages found.', 'fiverr-market'),
        'not_found_in_trash' => __('No messages found in Trash.', 'fiverr-market'),
        'featured_image' => _x('Message Image', 'Overrides the “Featured Image” phrase for this post type.', 'fiverr-market'),
        'set_featured_image' => _x('Set message image', 'Overrides the “Set featured image” phrase for this post type.', 'fiverr-market'),
        'remove_featured_image' => _x('Remove message image', 'Overrides the “Remove featured image” phrase for this post type.', 'fiverr-market'),
        'use_featured_image' => _x('Use as message image', 'Overrides the “Use as featured image” phrase for this post type.', 'fiverr-market'),
        'archives' => _x('Message archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'fiverr-market'),
        'insert_into_item' => _x('Insert into message', 'Overrides the “Insert into post” phrase (used when inserting media into a post).', 'fiverr-market'),
        'uploaded_to_this_item' => _x('Uploaded to this message', 'Overrides the “Uploaded to this post” phrase (used when viewing media attached to a post).', 'fiverr-market'),
        'filter_items_list' => _x('Filter messages list', 'Screen reader text for the filter links heading on the post type listing screen.', 'fiverr-market'),
        'items_list_navigation' => _x('Messages list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'fiverr-market'),
        'items_list' => _x('Messages list', 'Screen reader text for the items list heading on the post type listing screen.', 'fiverr-market'),
    );
    // Arguments for Messages Post Type
    $message_args = array(
        'labels' => $message_labels,
        'public' => false, // Hides from front end
        'publicly_queryable' => false,
        'show_ui' => true, // Shows in admin UI
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'message'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'custom-fields'),
    );
    register_post_type('message', $message_args);
    // Labels for Teacher Post Type
    $teacher_labels = array(
        'name' => _x('Teachers', 'Post type general name', 'fiverr-market'),
        'singular_name' => _x('Teacher', 'Post type singular name', 'fiverr-market'),
        'menu_name' => _x('Teachers', 'Admin Menu text', 'fiverr-market'),
        'name_admin_bar' => _x('Teacher', 'Add New on Toolbar', 'fiverr-market'),
        'add_new' => __('Add New Teacher', 'fiverr-market'),
        'add_new_item' => __('Add New Teacher', 'fiverr-market'),
        'new_item' => __('New Teacher', 'fiverr-market'),
        'edit_item' => __('Edit Teacher', 'fiverr-market'),
        'view_item' => __('View Teacher', 'fiverr-market'),
        'all_items' => __('All Teachers', 'fiverr-market'),
        'search_items' => __('Search Teachers', 'fiverr-market'),
        'not_found' => __('No teachers found.', 'fiverr-market'),
        'not_found_in_trash' => __('No teachers found in Trash.', 'fiverr-market'),
        'featured_image' => _x('Teacher Image', 'Overrides the “Featured Image” phrase for this post type.', 'fiverr-market'),
        'set_featured_image' => _x('Set teacher image', 'Overrides the “Set featured image” phrase for this post type.', 'fiverr-market'),
        'remove_featured_image' => _x('Remove teacher image', 'Overrides the “Remove featured image” phrase for this post type.', 'fiverr-market'),
        'use_featured_image' => _x('Use as teacher image', 'Overrides the “Use as featured image” phrase for this post type.', 'fiverr-market'),
        'archives' => _x('Teacher archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'fiverr-market'),
        'insert_into_item' => _x('Insert into teacher', 'Overrides the “Insert into post” phrase.', 'fiverr-market'),
        'uploaded_to_this_item' => _x('Uploaded to this teacher', 'Overrides the “Uploaded to this post” phrase.', 'fiverr-market'),
        'filter_items_list' => _x('Filter teachers list', 'Screen reader text for the filter links heading on the post type listing screen.', 'fiverr-market'),
        'items_list_navigation' => _x('Teachers list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'fiverr-market'),
        'items_list' => _x('Teachers list', 'Screen reader text for the items list heading on the post type listing screen.', 'fiverr-market'),
    );
    // Arguments for Teacher Post Type
    $teacher_args = array(
        'labels' => $teacher_labels,
        'public' => true, // Show it on the front end
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'teacher'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => true,
        'menu_position' => 1,
        'supports' => array('title', 'editor', 'author', 'custom-fields', 'thumbnail'),
    );

    register_post_type('teacher', $teacher_args);
}
add_action('init', 'create_teacher_student_message_cpt');

function conversation_shortcode()
{
    // Get sender ID from URL parameter
    // $sender_id = isset($_GET['r']) ? intval($_GET['r']) : 0;
    $current_user_id = get_current_user_id();

    ob_start();

?>

    <div class="container">
        <?php
        // Query to get all messages for the user
        $args = array(
            'post_type' => 'message',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'sender_id',
                    'value' => $current_user_id,
                    'compare' => '='
                ),
                array(
                    'key' => 'receiver_id',
                    'value' => $current_user_id,
                    'compare' => '='
                ),
            ),

            'posts_per_page' => -1 // Retrieve all messages
        );


        $messages = new WP_Query($args);

        // Array to hold aggregated messages by sender
        $message_data = array();

        if ($messages->have_posts()) {
            while ($messages->have_posts()) {
                $messages->the_post();
                $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
                $receiver_id = get_post_meta(get_the_ID(), 'receiver_id', true);
                $is_read = get_post_meta(get_the_ID(), 'is_read', true);
                $message_time = get_the_date('D, j M');

                // If this sender already exists in the array, update the count
                if (isset($message_data[$sender_id . $receiver_id])) {
                    $message_data[$sender_id . $receiver_id]['count'] += !$is_read ? 1 : 0;
                } else {
                    if (isset($message_data[$receiver_id . $sender_id])) {

                        $message_data[$receiver_id . $sender_id]['count'] += !$is_read ? 1 : 0;
                    } else {
                        if ($sender_id == $current_user_id) {
                            $id = $receiver_id;
                        } else {
                            $id = $sender_id;
                        }
                        // Otherwise, add a new entry for this sender
                        $message_data[$sender_id . $receiver_id] = array(
                            'sender_id' => $id,
                            'sender_name' => get_userdata($id)->display_name,
                            'message_time' => $message_time,
                            'count' => !$is_read ? 1 : 0
                        );
                    }
                }
            }
            wp_reset_postdata();
        }

        ?>


        <div class="container mt-4 position-relative">
            <div class="pre-loading">
                <div class="loading">
                    <div class="lds-ellipsis">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>

            <div class="chat-container">

                <!-- Sidebar -->
                <div class="chat-sidebar">

                    <div class="list-group gap-3">
                        <?php
                        $active = $_GET['r'];
                        // Output the aggregated data
                        if (!empty($message_data)) {
                            foreach ($message_data as $data) {
                        ?>
                                <a href="<?php echo home_url('/chat') . '?r=' . $data['sender_id']; ?>"
                                    class="list-group-item list-group-item-action 
                                <?php
                                if ($active == $data['sender_id']) {
                                    echo 'active ';
                                } ?>" data-id="<?php echo $data['sender_id']; ?>">
                                    <div class="img">
                                        <?php
                                        $user_id = $data['sender_id'];
                                        $name = get_the_author_meta('display_name', $user_id);
                                        $profile_id = get_user_meta($user_id, 'profile_picture', true);
                                        $avatar_url = wp_get_attachment_url($profile_id);

                                        // $avatar_url = get_avatar_url($data['sender_id']);

                                        if ($avatar_url) {
                                        ?> <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2"
                                                width="30" alt="<?php echo $data['sender_id'] . $data['sender_name']; ?>">
                                        <?php
                                        } else {
                                        ?>
                                            <img src="https://media.istockphoto.com/id/1223671392/vector/default-profile-picture-avatar-photo-placeholder-vector-illustration.jpg?s=612x612&w=0&k=20&c=s0aTdmT5aU6b8ot7VKm11DeID6NctRCpB755rA1BIP0="
                                                class="rounded-circle objerct-fit-cover mr-2" width="30" alt="<?php echo $data['sender_name']; ?>">
                                        <?php
                                        }
                                        ?> <span class="text-truncate">
                                            <?php echo  $data['sender_name']; ?>
                                        </span>
                                        <?php
                                        if ($data['count'] > 0) {
                                        ?>
                                            <div class="unread">
                                                <div class="circle"></div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>


                                </a>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php  // Get sender ID from URL parameter
                $sender_id = isset($_GET['r']) ? intval($_GET['r']) : $sender_id;
                $user_id = $sender_id;
                $name = get_the_author_meta('display_name', $user_id);
                $profile_id = get_user_meta($sender_id, 'profile_picture', true);
                $avatar_url = wp_get_attachment_url($profile_id);


                ?>
                <div class="chat-main">
                    <!-- Chat Header -->
                    <div class="chat-header">
                        <strong><?php
                                if ($avatar_url) {
                                ?>
                                <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2" width="30" alt="<?php echo $data['sender_name']; ?>">
                            <?php
                                } else {
                            ?> <img src="https://media.istockphoto.com/id/1223671392/vector/default-profile-picture-avatar-photo-placeholder-vector-illustration.jpg?s=612x612&w=0&k=20&c=s0aTdmT5aU6b8ot7VKm11DeID6NctRCpB755rA1BIP0="
                                    class="rounded-circle mr-2" width="30" alt="<?php echo $data['sender_name']; ?>">
                            <?php
                                }
                            ?> <?php echo $name; ?>
                        </strong>
                        <!-- <span class="float-right">Last seen: 2 months ago</span> -->
                    </div>

                    <?php


                    // Check if sender ID is valid
                    if ($sender_id <= 0) {
                        return '<div class="alert alert-danger">Invalid sender ID.</div>';
                    }

                    // Query messages between the current user and the sender
                    $args = array(
                        'post_type' => 'message',
                        'meta_query' => array(
                            'relation' => 'OR',
                            array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'sender_id',
                                    'value' => $sender_id,
                                    'compare' => '='
                                ),
                                array(
                                    'key' => 'receiver_id',
                                    'value' => $current_user_id,
                                    'compare' => '='
                                ),
                            ),
                            array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'sender_id',
                                    'value' => $current_user_id,
                                    'compare' => '='
                                ),
                                array(
                                    'key' => 'receiver_id',
                                    'value' => $sender_id,
                                    'compare' => '='
                                ),
                            ),
                        ),

                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'order' => 'ASC'
                    );

                    $messages = new WP_Query($args);


                    $myid = get_current_user_id();
                    $myname = get_the_author_meta('display_name', $myid);
                    $student_id = isset($_GET['r']) ? intval($_GET['r']) : $sender_id;
                    $student_name = get_the_author_meta('display_name', $student_id);


                    ?>

                    <!-- Chat Messages -->
                    <div class="chat-messages position-relative">

                        <!-- Popup -->

                        <div class="chat-messages">
                            <div class="chat-popup">
                                <div class="container mt-5">
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="card">
                                                <div class="card-header text-center">
                                                    <h4>Send Invitation</h4>
                                                    <div class="teacher-students d-flex justify-content-between">
                                                        <div class="teacher d-flex flex-column" data-id="45">
                                                            <div class="">Teacher</div>
                                                            <div class="t-name"><?php echo $myname; ?></div>
                                                        </div>
                                                        <div class="student d-flex flex-column" data-id="45">
                                                            <div class="">Student</div>
                                                            <div class="t-name"><?php echo $student_name; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <form>
                                                        <div class="form-row d-none">
                                                            <!-- First Column: Date Picker Field -->
                                                            <div class="col-md-6 mb-3">
                                                                <input
                                                                    type="text"
                                                                    class="form-control datepicker"
                                                                    id="teacher"
                                                                    name="teacher"
                                                                    value="<?php echo $myid; ?>" />
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <input
                                                                    type="text"
                                                                    class="form-control datepicker"
                                                                    id="student"
                                                                    name="student"
                                                                    value="<?php echo $student_id; ?>" />
                                                            </div>
                                                            <!-- Second Column: Time Field -->

                                                        </div>
                                                        <div class="form-row row">
                                                            <!-- First Column: Date Picker Field -->
                                                            <div class="col-md-6 mb-3">
                                                                <label for="date">Select Date</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control datepicker"
                                                                    id="date"
                                                                    name="date"
                                                                    placeholder="Select Date"
                                                                    required />
                                                            </div>
                                                            <!-- Second Column: Time Field -->
                                                            <div class="col-md-6 mb-2">
                                                                <label for="time">Select Time</label>
                                                                <input
                                                                    type="time"
                                                                    class="form-control"
                                                                    id="time"
                                                                    name="time"
                                                                    placeholder="Select Time"
                                                                    required />
                                                            </div>
                                                        </div>
                                                        <div class="form-row row">
                                                            <!-- First Column: Amount Field -->
                                                            <div class="col-md-6 mb-2">
                                                                <input
                                                                    type="number"
                                                                    class="form-control"
                                                                    id="amount"
                                                                    name="amount"
                                                                    placeholder="Enter Amount"
                                                                    required />
                                                            </div>
                                                            <!-- Second Column: Class Length Field -->
                                                            <div class="col-md-6 mb-2">
                                                                <select name="length" id="length" class="form-control form-select h-100">
                                                                    <option value="15">15 Munites</option>
                                                                    <option value="30">30 Munites</option>
                                                                    <option value="45">45 Munites</option>
                                                                    <option value="60" selected>60 Munites</option>
                                                                    <option value="75">75 Munites</option>
                                                                    <option value="90">90 Munites</option>
                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="form-row d-none">
                                                            <!-- Hidden Coupon Code Field (Full-Width) -->
                                                            <div class="col-md-12 mb-2">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    id="coupon"
                                                                    name="coupon"
                                                                    placeholder="Coupon Code" />
                                                            </div>
                                                        </div>
                                                        <!-- Full-Width Submit Button -->
                                                        <div class="text-center">
                                                            <button
                                                                type="button"
                                                                class="btn btn-primary w-100 send-invitation-confirm">
                                                                Send Invitation
                                                            </button>
                                                        </div>
                                                        <div class="text-center">
                                                            <button
                                                                type="button"
                                                                class="btn btn-danger w-100 cencel-invitation">
                                                                Cencel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Example Messages -->
                        <?php


                        if ($messages->have_posts()) {
                            while ($messages->have_posts()) {
                                $messages->the_post();
                                $is_read = get_post_meta(get_the_ID(), 'is_read', true);
                                $amount = get_post_meta(get_the_ID(), 'amount', true) ? true : false;
                                $author_id = get_the_author_meta('ID');
                                $current_user_id = get_current_user_id();
                                if ($author_id !== $current_user_id) {
                                    // Update the 'is_read' meta field to true
                                    update_post_meta(get_the_ID(), 'is_read', true);
                                }
                                $message_time = get_the_date('D, j M Y \a\t H:i');
                                $message_content = get_the_content();
                                // Determine message alignment
                                $left = get_post_meta(get_the_ID(), 'sender_id', true) == $sender_id ? true : false;
                                $message_class = $is_read ? 'list-group-item' : 'list-group-item list-group-item-info';
                                if ($left) {
                        ?>
                                    <div class="message received border-0 <?php echo $message_class; ?>">
                                        <?php
                                        if ($amount) {
                                            $myid = get_current_user_id();
                                            $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
                                            $receiver_id = get_post_meta(get_the_ID(), 'receiver_id', true);
                                            $amount = get_post_meta(get_the_ID(), 'amount', true);
                                            $date = get_post_meta(get_the_ID(), 'date', true);
                                            $time = get_post_meta(get_the_ID(), 'time', true);
                                            $length = get_post_meta(get_the_ID(), 'length', true);
                                            $is_accepted = get_post_meta(get_the_ID(), 'is_accepted', true);
                                            $teacher_name = get_userdata($sender_id)->display_name;
                                            $student_name = get_userdata($receiver_id)->display_name;
                                            $is_me_student =  $myid == $receiver_id ? true : false;
                                        ?>

                                            <div class="card offer-box mb-3">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <span class="offer-title">Invitation</span>
                                                    <span class="offer-price h5 mb-0">$<?php echo $amount; ?></span>
                                                </div>
                                                <div class="card-body">
                                                    <p class="p-0 m-0"><strong>Teacher:</strong> <?php echo $teacher_name; ?></p>
                                                    <p class="p-0 m-0"><strong>Student:</strong> <?php echo $student_name; ?></p>
                                                    <p class="p-0 m-0"><strong>Date:</strong> <?php echo $date; ?></p>
                                                    <p class="p-0 m-0"><strong>Start Time:</strong><?php echo $time; ?> </p>
                                                    <p class="p-0 m-0"><strong>Duration:</strong> <?php echo $length; ?></p>
                                                </div>
                                                <div class="card-footer text-center w-100">
                                                    <?php
                                                    if ($is_me_student) {
                                                        if ($is_accepted) {
                                                    ?>
                                                            <button class="btn btn-desable w-100" disabled>Accepted</button>

                                                        <?php
                                                        } else {
                                                        ?>
                                                            <button class="btn btn-primary accept-btn w-100">Accept it now</button>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <button class="btn btn-desable w-100" disabled>Offer Sent</button>

                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        <?php
                                        } else {
                                        ?>

                                            <div class="message-text"><?php echo esc_html($message_content); ?>
                                                <div class="msg-time " style="font-weight: 300;"><?php echo esc_html($message_time); ?>
                                                </div>
                                            </div>

                                        <?php
                                        }
                                        ?>
                                    </div>
                                <?php

                                } else {
                                ?>
                                    <div class="message sent border-0 <?php echo $message_class; ?>">
                                        <?php
                                        if ($amount) {
                                            $myid = get_current_user_id();
                                            $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
                                            $receiver_id = get_post_meta(get_the_ID(), 'receiver_id', true);
                                            $amount = get_post_meta(get_the_ID(), 'amount', true);
                                            $date = get_post_meta(get_the_ID(), 'date', true);
                                            $time = get_post_meta(get_the_ID(), 'time', true);
                                            $length = get_post_meta(get_the_ID(), 'length', true);
                                            $is_accepted = get_post_meta(get_the_ID(), 'is_accepted', true);
                                            $teacher_name = get_userdata($sender_id)->display_name;
                                            $student_name = get_userdata($receiver_id)->display_name;
                                            $is_me_student =  $myid == $receiver_id ? true : false;
                                        ?>

                                            <div class="card offer-box mb-3">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <span class="offer-title">Invitation</span>
                                                    <span class="offer-price h5 mb-0">$<?php echo $amount; ?></span>
                                                </div>
                                                <div class="card-body">
                                                    <p class="p-0 m-0"><strong>Teacher:</strong> <?php echo $teacher_name; ?></p>
                                                    <p class="p-0 m-0"><strong>Student:</strong> <?php echo $student_name; ?></p>
                                                    <p class="p-0 m-0"><strong>Date:</strong> <?php echo $date; ?></p>
                                                    <p class="p-0 m-0"><strong>Start Time:</strong><?php echo $time; ?> </p>
                                                    <p class="p-0 m-0"><strong>Duration:</strong> <?php echo $length; ?></p>
                                                </div>
                                                <div class="card-footer text-center w-100">
                                                    <?php
                                                    if ($is_me_student) {
                                                        if ($is_accepted) {
                                                    ?>
                                                            <button class="btn btn-desable w-100" disabled>Accepted</button>

                                                        <?php
                                                        } else {
                                                        ?>
                                                            <button class="btn btn-primary accept-btn w-100">Accept it now</button>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <button class="btn btn-desable w-100" disabled>Offer Sent</button>

                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        <?php
                                        } else {
                                        ?>

                                            <div class="message-text"><?php echo esc_html($message_content); ?>
                                                <div class="msg-time " style="font-weight: 300;"><?php echo esc_html($message_time); ?>
                                                </div>
                                            </div>

                                        <?php
                                        }
                                        ?>


                                    </div><?php
                                        }
                                            ?>


                            <?php
                            }
                        } else {
                            ?>
                            <div class="alert alert-info">No messages found.</div>
                        <?php
                        }
                        ?>


                    </div>

                    <!-- Chat Footer -->
                    <div class="chat-footer">
                        <textarea id="reply-message" class="form-control chat-input"
                            placeholder="Type your message here..."></textarea>

                        <div class="chat-footer-buttons">
                            <?php
                            // If current login user is an teacher.
                            if (current_user_can('teacher') || current_user_can('administrator')) {
                            ?>
                                <button class="btn btn-primary" id="send-invitation"><i class="fas fa-plus"></i>
                                    Send Invitation</button>

                            <?php } else {
                            ?>
                                <div class="" id=""> </div>
                            <?php
                            } ?>
                            <button type="submit" style="width: 120px; font-size: 16px"
                                class="btn btn-primary reply_message"
                                data-receiver-id="<?php echo esc_attr($sender_id); ?>">Send <i style="font-size: 20px;"
                                    class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>
    </div>

<?php

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('conversation', 'conversation_shortcode');

function is_read_or_not_header()
{
    // Get sender ID from URL parameter
    // $sender_id = isset($_GET['r']) ? intval($_GET['r']) : 0;
    $current_user_id = get_current_user_id();
    ob_start();
?>
    <?php
    // Query to get all messages for the user
    $args = array(
        'post_type' => 'message',
        'meta_query' => array(
            array(
                'key' => 'receiver_id',
                'value' => $current_user_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => -1 // Get all messages
    );

    $messages = new WP_Query($args);

    // Array to hold aggregated messages by sender
    $message_data = array();
    $unread_msgs = 0;
    if ($messages->have_posts()) {
        while ($messages->have_posts()) {
            $messages->the_post();
            $is_read = get_post_meta(get_the_ID(), 'is_read', true);
            $unread_msgs += !$is_read ? 1 : 0;
        }
        wp_reset_postdata();
    }
    ?>

    <div class="chat-button">
        <a href="<?php home_url('/chat'); ?>" class="link">
            <i class="fas fa-message"></i>
            <div class="">Chat</div>
            <?php if ($unread_msgs > 0) { ?>
                <div class="unread">
                    <div class="circle"></div>
                </div>
            <?php } ?>
        </a>
    </div>




<?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('header_chat', 'is_read_or_not_header');
function chat_url()
{
    // Get the current post ID
    $post_id = get_the_ID();

    // Get the author (teacher) ID
    $teacher_id = get_the_author_meta('ID');

    // Build the chat URL with the post ID as a parameter
    $chat_url = home_url('/chat?r=' . $teacher_id);

    // Return the chat URL (shortcodes should return, not echo)
    return esc_url($chat_url);
}
add_shortcode('chat_url', 'chat_url');


// delete_option('grades_and_expertise_added');
function add_grades_and_expertise() {
    // American and British Grades
    $expertise = array(
        // American Curriculum
        'SAT', 'ACT',
        'AP Calc AB', 'AP Calc BC', 'AP Stats',
        'AP Physics 1', 'AP Physics 2', 'AP Physics C (Mech)', 'AP Physics C (E&M)',
        'AP Bio', 'AP Chem', 'AP Env Science',
        'AP Eng Lang', 'AP Eng Lit',
        'AP World Hist', 'AP US Hist', 'AP Euro Hist', 'AP Human Geo', 'AP Psych',
        'AP Gov (US)', 'AP Gov (Comparative)',
        'AP Spanish', 'AP French', 'AP Chinese', 'AP German',
        'AP Art & Design', 'AP Music Theory', 'PSAT',

        // British Curriculum (IGCSE/GCSE and A Levels)
        'IGCSE Math', 'IGCSE English', 'IGCSE Bio', 'IGCSE Chem', 'IGCSE Physics',
        'IGCSE History', 'IGCSE Geography', 'IGCSE Econ', 'IGCSE Sociology',
        'IGCSE Arabic', 'IGCSE French', 'IGCSE Spanish', 'IGCSE Art & Design', 'IGCSE Drama', 'IGCSE Music',
        'IGCSE Business', 'IGCSE Comp Sci', 'IGCSE ICT', 'IGCSE PE', 'IGCSE Global Perspectives',
        'A-Level Math', 'A-Level Further Math', 'A-Level Bio', 'A-Level Chem', 'A-Level Physics',
        'A-Level History', 'A-Level Geography', 'A-Level Econ', 'A-Level Sociology', 'A-Level Psych',
        'A-Level Arabic', 'A-Level French', 'A-Level Spanish',
        'A-Level Art & Design', 'A-Level Drama', 'A-Level Music',
        'A-Level Business', 'A-Level Comp Sci', 'A-Level ICT', 'A-Level PE', 'A-Level Global Perspectives',

        // Saudi National Curriculum
        'Qiyas Verbal', 'Qiyas Quantitative', 'Tahsili Math', 'Tahsili Physics', 'Tahsili Chem', 'Tahsili Bio',

        // International Baccalaureate (IB) Curriculum
        'IB Math (SL/HL)', 'IB Bio (SL/HL)', 'IB Chem (SL/HL)', 'IB Physics (SL/HL)',
        'IB Econ (SL/HL)', 'IB Business (SL/HL)', 'IB Eng Lit (SL/HL)',
        'IB Arabic (SL/HL)', 'IB History (SL/HL)', 'IB Psych (SL/HL)'
    );

    $grades = array(
        // American system
        'Primary School', 'Middle School', 'High School', 'Undergraduate', 'Postgraduate', 'Diploma', 'Doctorate',
        // British system
        'Key Stage 1', 'Key Stage 2', 'Key Stage 3', 'Key Stage 4', 'GCSE', 'A-Level'
    );


    foreach ($grades as $grade) {
        if (!term_exists($grade, 'grade')) {
            wp_insert_term($grade, 'grade');
        }
    }

    // Expertise (Curriculums) including American and British systems
    $expertise = array(
        // American Curriculum
        'SAT', 'ACT',
        'AP Calc AB', 'AP Calc BC', 'AP Stats',
        'AP Physics 1', 'AP Physics 2', 'AP Physics C (Mech)', 'AP Physics C (E&M)',
        'AP Bio', 'AP Chem', 'AP Env Science',
        'AP Eng Lang', 'AP Eng Lit',
        'AP World Hist', 'AP US Hist', 'AP Euro Hist', 'AP Human Geo', 'AP Psych',
        'AP Gov (US)', 'AP Gov (Comparative)',
        'AP Spanish', 'AP French', 'AP Chinese', 'AP German',
        'AP Art & Design', 'AP Music Theory', 'PSAT',

        // British Curriculum (IGCSE/GCSE and A Levels)
        'IGCSE Math', 'IGCSE English', 'IGCSE Bio', 'IGCSE Chem', 'IGCSE Physics',
        'IGCSE History', 'IGCSE Geography', 'IGCSE Econ', 'IGCSE Sociology',
        'IGCSE Arabic', 'IGCSE French', 'IGCSE Spanish', 'IGCSE Art & Design', 'IGCSE Drama', 'IGCSE Music',
        'IGCSE Business', 'IGCSE Comp Sci', 'IGCSE ICT', 'IGCSE PE', 'IGCSE Global Perspectives',
        'A-Level Math', 'A-Level Further Math', 'A-Level Bio', 'A-Level Chem', 'A-Level Physics',
        'A-Level History', 'A-Level Geography', 'A-Level Econ', 'A-Level Sociology', 'A-Level Psych',
        'A-Level Arabic', 'A-Level French', 'A-Level Spanish',
        'A-Level Art & Design', 'A-Level Drama', 'A-Level Music',
        'A-Level Business', 'A-Level Comp Sci', 'A-Level ICT', 'A-Level PE', 'A-Level Global Perspectives',

        // Saudi National Curriculum
        'Qiyas Verbal', 'Qiyas Quantitative', 'Tahsili Math', 'Tahsili Physics', 'Tahsili Chem', 'Tahsili Bio',

        // International Baccalaureate (IB) Curriculum
        'IB Math (SL/HL)', 'IB Bio (SL/HL)', 'IB Chem (SL/HL)', 'IB Physics (SL/HL)',
        'IB Econ (SL/HL)', 'IB Business (SL/HL)', 'IB Eng Lit (SL/HL)',
        'IB Arabic (SL/HL)', 'IB History (SL/HL)', 'IB Psych (SL/HL)'
    

    );

    foreach ($expertise as $curriculum) {
        if (!term_exists($curriculum, 'expertise')) {
            wp_insert_term($curriculum, 'expertise');
        }
    }
}
// Hook into 'init' action to run the function once
add_action('init', 'run_once_add_grades_and_expertise');
function run_once_add_grades_and_expertise() {
    if (!get_option('grades_and_expertise_added')) {
        add_grades_and_expertise();
        update_option('grades_and_expertise_added', true);
    }
}
function add_countries_to_region_taxonomy() {
    $countries = array(
        'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 
        'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 
        'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 
        'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia', 'Cameroon', 
        'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 
        'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 
        'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 
        'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon', 'Gambia', 'Georgia', 'Germany', 
        'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 
        'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 
        'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 
        'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 
        'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 
        'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 
        'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 
        'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 
        'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 
        'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 
        'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 
        'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 
        'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 
        'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 
        'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 
        'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 
        'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
    );

    foreach ($countries as $country) {
        if (!term_exists($country, 'region')) {
            wp_insert_term($country, 'region');
        }
    }
}
// Hook into 'init' action to run the function once
add_action('init', 'run_once_add_countries');
function run_once_add_countries() {
    if (!get_option('countries_added_to_region')) {
        add_countries_to_region_taxonomy();
        update_option('countries_added_to_region', true);
    }
}


<?php

// Add the 'teacher' role with specific capabilities
add_role('teacher', 'Teacher', array(
    'read' => true, // Can read posts and pages
    'edit_posts' => true, // Can edit their own posts
    'delete_posts' => true, // Can delete their own posts
    'publish_posts' => true, // Can publish their own posts
    'upload_files' => true, // Can upload files
));


// Add Review Function
function add_review($teacher_id, $review_data)
{
    // Get existing reviews
    $reviews = get_user_meta($teacher_id, 'teacher_reviews', true);
    $reviews = $reviews ? json_decode($reviews, true) : [];

    // Add new review
    $reviews[] = $review_data;

    // Update user meta with new reviews
    update_user_meta($teacher_id, 'teacher_reviews', json_encode($reviews));
}

// // Example usage
// $teacher_id = 1; // Replace with the actual teacher ID
// $review_data = [
//     'reviewer_name' => 'John Doe',
//     'rating' => 5,
//     'comment' => 'Great teacher!',
//     'date' => current_time('mysql')
// ];
// add_review($teacher_id, $review_data);


// Hook into init action to add reviews
// add_action('init', 'add_reviews_example');

function add_reviews_example()
{
    // Replace with the actual teacher ID
    $teacher_id = 6;

    // Review data
    $review_data = [
        'reviewer_name' => 'ALim',
        'rating' => 3,
        'comment' => 'Great teacher! Thanks for the colaboration',
        'date' => current_time('mysql'),
        'image' => 'https://cdn-icons-png.flaticon.com/512/2919/2919906.png'
    ];

    // Make sure the user exists before adding a review
    if (get_user_by('id', $teacher_id)) {
        add_review($teacher_id, $review_data);
    } else {
        error_log("User with ID $teacher_id does not exist.");
    }
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




/* ---------------------
 *  Get Message Function
 * -------------------------- */

function get_user_messages($user_id)
{
    // Query to get all messages for the user
    $args = array(
        'post_type' => 'message',
        'meta_query' => array(
            array(
                'key' => 'receiver_id',
                'value' => $user_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => -1 // Get all messages
    );

    $messages = new WP_Query($args);

    // Array to hold aggregated messages by sender
    $message_data = array();

    if ($messages->have_posts()) {
        while ($messages->have_posts()) {
            $messages->the_post();
            $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
            $is_read = get_post_meta(get_the_ID(), 'is_read', true);
            $message_time = get_the_date('D, j M');

            // If this sender already exists in the array, update the count
            if (isset($message_data[$sender_id])) {
                $message_data[$sender_id]['count'] += !$is_read ? 1 : 0;
            } else {
                // Otherwise, add a new entry for this sender
                $message_data[$sender_id] = array(
                    'sender_name' => get_userdata($sender_id)->display_name,
                    'message_time' => $message_time,
                    'count' => !$is_read ? 1 : 0
                );
            }
        }
        wp_reset_postdata();
    }

?>


    <div class="container mt-4">
        <div class="chat-container">
            <!-- Sidebar -->
            <div class="list-group">


                <?php

                // Output the aggregated data
                if (!empty($message_data)) {
                    foreach ($message_data as $data) {

                ?>
                        <tr class="border-0">
                            <td class="border-0"> <?php echo $data['message_time']; ?></td>
                            <td class="border-0"> <?php echo $data['sender_name']; ?></td>
                            <td class="border-0">
                                <?php echo ($data['count'] > 0 ? $data['count'] . ' New Message' . ($data['count'] > 1 ? 's' : '') : 'No New Messages'); ?>
                            </td>
                            <td class="border-0"> <a href="<?php echo home_url('/chat') . '?r=' . $sender_id; ?>">Chat</a></td>

                        </tr>


                        <a href="#" class="list-group-item list-group-item-action active" data-id="<?php echo $sender_id; ?>">
                            <div class="img">
                                <?php $avatar_url = get_avatar_url($sender_id);

                                if ($avatar_url) {
                                ?> <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2" width="30"
                                        alt="<?php echo $data['sender_name']; ?>"> <?php
                                                                                } else {
                                                                                    ?> <img
                                        src="https://media.istockphoto.com/id/1223671392/vector/default-profile-picture-avatar-photo-placeholder-vector-illustration.jpg?s=612x612&w=0&k=20&c=s0aTdmT5aU6b8ot7VKm11DeID6NctRCpB755rA1BIP0="
                                        class="rounded-circle mr-2" width="30" alt="<?php echo $data['sender_name']; ?>"> <?php
                                                                                                                        }
                                                                                                                            ?> <span class="text-truncate">
                                    <?php echo $data['sender_name']; ?>
                                </span>
                                <?php
                                if ($data['count'] > 0) {
                                ?>
                                    <div class="unread text-danger"> unread </div>
                                <?php
                                }
                                ?>
                            </div>
                        </a>
                    <?php
                    }
                    ?>
            </div>

        </div>
    </div>

<?php
                } else {
                    echo '<tr><td colspan="4">No messages found.</td></tr>';
                }
            }


            /* ---------------------
 *  Get Message Function
 * -------------------------- */

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

                if ($messages->have_posts()) {
                    while ($messages->have_posts()) {
                        $messages->the_post();
                        $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
                        $receiver_id = get_post_meta(get_the_ID(), 'receiver_id', true);
                        $is_read = get_post_meta(get_the_ID(), 'is_read', true);
                        $message_time = get_the_date('D, j M');

                        // If this sender already exists in the array, update the count
                        if (isset($message_data[$sender_id])) {
                            $message_data[$sender_id]['count'] += !$is_read ? 1 : 0;
                        } else {
                            // Otherwise, add a new entry for this sender
                            $message_data[$sender_id] = array(
                                'sender_id' => $sender_id,
                                'sender_name' => get_userdata($sender_id)->display_name,
                                'message_time' => $message_time,
                                'count' => !$is_read ? 1 : 0
                            );
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
                                class="list-group-item list-group-item-action <?php if ($active == $data['sender_id']) {
                                                                                    echo 'active ';
                                                                                } ?>" data-id="<?php echo $sender_id; ?>">
                                <div class="img">
                                    <?php $avatar_url = get_avatar_url($sender_id);

                                    if ($avatar_url) {
                                    ?> <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2"
                                            width="30" alt="<?php echo $data['sender_name']; ?>">
                                    <?php
                                    } else {
                                    ?>
                                        <img src="https://media.istockphoto.com/id/1223671392/vector/default-profile-picture-avatar-photo-placeholder-vector-illustration.jpg?s=612x612&w=0&k=20&c=s0aTdmT5aU6b8ot7VKm11DeID6NctRCpB755rA1BIP0="
                                            class="rounded-circle mr-2" width="30" alt="<?php echo $data['sender_name']; ?>">
                                    <?php
                                    }
                                    ?> <span class="text-truncate">
                                        <?php echo $data['sender_name']; ?>
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
                $profile_id = get_user_meta($sender_id,'profile_picture',true);
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
                        ?> <?php echo $data['sender_name']; ?>
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




                ?>

                <!-- Chat Messages -->
                <div class="chat-messages position-relative">

                    <!-- Example Messages -->
                    <?php
                     

                    if ($messages->have_posts()) {
                        while ($messages->have_posts()) {
                            $messages->the_post();
                            $is_read = get_post_meta(get_the_ID(), 'is_read', true);
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
                                    <div class="message-text"><?php echo esc_html($message_content); ?>
                                        <div class="msg-time " style="font-weight: 300;"><?php echo esc_html($message_time); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="message sent ">
                                    <div class="message-text"><?php echo esc_html($message_content); ?>
                                        <div class="msg-time " style="font-weight: 300;"><?php echo esc_html($message_time); ?>
                                        </div>
                                    </div>
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
                        <button class="btn btn-primary" id="attach-btn"><i class="fas fa-plus"></i>
                            Send Invitation</button>
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
</div>

<?php

                wp_reset_postdata();
                return ob_get_clean();
            }
            add_shortcode('conversation', 'conversation_shortcode');


            /* ---------------------
 *  Message Notification at header
 * -------------------------- */

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


?>

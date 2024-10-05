<?php

/* ---------------------
 * Custom Post Type
 ------------------------
 */

function create_teacher_student_message_cpt()
{
    $labels = array(
        'name' => _x('Messages', 'Post type general name', 'growgoal'),
        'singular_name' => _x('Message', 'Post type singular name', 'growgoal'),
        'menu_name' => _x('Messages', 'Admin Menu text', 'growgoal'),
        'name_admin_bar' => _x('Message', 'Add New on Toolbar', 'growgoal'),
        'add_new' => __('Add New', 'growgoal'),
        'add_new_item' => __('Add New Message', 'growgoal'),
        'new_item' => __('New Message', 'growgoal'),
        'edit_item' => __('Edit Message', 'growgoal'),
        'view_item' => __('View Message', 'growgoal'),
        'all_items' => __('All Messages', 'growgoal'),
        'search_items' => __('Search Messages', 'growgoal'),
        'not_found' => __('No messages found.', 'growgoal'),
        'not_found_in_trash' => __('No messages found in Trash.', 'growgoal'),
        'featured_image' => _x('Message Image', 'Overrides the “Featured Image” phrase for this post type.', 'growgoal'),
        'set_featured_image' => _x('Set message image', 'Overrides the “Set featured image” phrase for this post type.', 'growgoal'),
        'remove_featured_image' => _x('Remove message image', 'Overrides the “Remove featured image” phrase for this post type.', 'growgoal'),
        'use_featured_image' => _x('Use as message image', 'Overrides the “Use as featured image” phrase for this post type.', 'growgoal'),
        'archives' => _x('Message archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'growgoal'),
        'insert_into_item' => _x('Insert into message', 'Overrides the “Insert into post” phrase (used when inserting media into a post).', 'growgoal'),
        'uploaded_to_this_item' => _x('Uploaded to this message', 'Overrides the “Uploaded to this post” phrase (used when viewing media attached to a post).', 'growgoal'),
        'filter_items_list' => _x('Filter messages list', 'Screen reader text for the filter links heading on the post type listing screen.', 'growgoal'),
        'items_list_navigation' => _x('Messages list navigation', 'Screen reader text for the pagination heading on the post type listing screen.', 'growgoal'),
        'items_list' => _x('Messages list', 'Screen reader text for the items list heading on the post type listing screen.', 'growgoal'),
    );


    $args = array(
        'labels' => $labels,
        'public' => false, // Hides it from the front end
        'publicly_queryable' => false, // Disables querying this post type from the front end
        'show_ui' => true,  // Enables the UI for the post type
        'show_in_menu' => true, // Hides it from the admin menu
        'query_var' => true,
        'rewrite' => array('slug' => 'message'),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title', 'editor', 'author', 'custom-fields'),
    );

    register_post_type('message', $args);
}
// Action will be on activate
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
 *  Message Shortcode
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

                <div class="list-group">
                    <?php

                    // Output the aggregated data
                    if (!empty($message_data)) {
                        foreach ($message_data as $data) {

                    ?>
                            <a href="<?php echo home_url('/chat') . '?r=' . $sender_id; ?>"
                                class="list-group-item list-group-item-action active" data-id="<?php echo $sender_id; ?>">
                                <div class="img">
                                    <?php $avatar_url = get_avatar_url($sender_id);

                                    if ($avatar_url) {
                                    ?> <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2"
                                            width="30" alt="<?php echo $data['sender_name']; ?>"> <?php
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
                $current_user_id = get_current_user_id();
            ?>

            <div class="chat-main">
                <!-- Chat Header -->
                <div class="chat-header">
                    <strong><?php $avatar_url = get_avatar_url($sender_id);

                            if ($avatar_url) {
                            ?> <img src="<?php echo $avatar_url; ?>" class="rounded-circle mr-2" width="30"
                                alt="<?php echo $data['sender_name']; ?>"> <?php
                                                                        } else {
                                                                            ?> <img
                                src="https://media.istockphoto.com/id/1223671392/vector/default-profile-picture-avatar-photo-placeholder-vector-illustration.jpg?s=612x612&w=0&k=20&c=s0aTdmT5aU6b8ot7VKm11DeID6NctRCpB755rA1BIP0="
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
                                    <div class="message-text"><?php echo esc_html($message_content); ?></div>
                                    <div class="msg-time " style="font-weight: 300;"><?php echo esc_html($message_time); ?></div>
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
                        <button class="btn btn-secondary" id="attach-btn"><i class="fas fa-paperclip"></i>
                            Attach</button>
                        <input type="file" id="file-upload-input" style="display: none;">
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




            // Ajax
            /*========================================================
* Reply to a message
==========================================================*/
            function send_reply_message()
            {
                $sender_id = get_current_user_id();
                $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
                $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';

                if ($receiver_id <= 0 || empty($message)) {
                    wp_send_json_error('Invalid data.');
                    return;
                }

                // Create a new message post
                $message_id = wp_insert_post(array(
                    'post_type'   => 'message',
                    'post_title'  => 'Reply from ' . get_userdata($sender_id)->display_name,
                    'post_content' => $message,
                    'post_status' => 'publish',
                    'meta_input'  => array(
                        'sender_id'  => $sender_id,
                        'receiver_id' => $receiver_id,
                        'is_read'    => 0, // Default to unread
                        'is_notified'    => 0, // Default to unread
                    ),
                ));

                if ($message_id) {
                    wp_send_json_success('Message sent successfully.');
                } else {
                    wp_send_json_error('Failed to send message.');
                }
            }
            add_action('wp_ajax_send_reply_message', 'send_reply_message');
            add_action('wp_ajax_nopriv_send_reply_message', 'send_reply_message');



            function get_unread_message_notification()
            {
                $myid = get_current_user_id();
                $args = array(
                    'post_type' => 'message',
                    'meta_query' => array(
                        array(
                            'key' => 'receiver_id',
                            'value' => $myid,
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => -1 // Get all messages
                );

                $messages = new WP_Query($args);
                $total = 0;
                $id = 0;
                if ($messages->have_posts()) {
                    while ($messages->have_posts()) {
                        $messages->the_post();
                        $sender_id = get_post_meta(get_the_ID(), 'sender_id', true);
                        $is_notfied = get_post_meta(get_the_ID(), 'is_notified', true);

                        if (! $is_notfied) {
                            $total++;
                            update_post_meta(get_the_ID(), 'is_notified', true);
                            $id = $sender_id;
                        }
                    }
                    wp_reset_postdata();
                }

                if ($total > 0) {
                    if ($total < 2) {
                        $user_info = get_userdata($id); // Get the user data
                        $display_name = $user_info->display_name; // Get the display name

                        wp_send_json([
                            'success' => true,
                            'm' => 'You have a new message from <strong><a href="' . home_url('/chat') . '">' . $display_name . '</a></strong>'
                        ]);
                    } else {
                        wp_send_json([
                            'success' => true,
                            'm' => 'You have ' . $total . 'New Messages <a href="' . home_url('/chat') . '"> Chat </a>'
                        ]);
                    }
                } else {
                    wp_send_json_error('No Unread Message');
                }
            }
            add_action('wp_ajax_get_unread_message_notification', 'get_unread_message_notification');
            add_action('wp_ajax_nopriv_get_unread_message_notification', 'get_unread_message_notification');



            // Scripts
?>

<script>
    jQuery(document).ready(function($) {


        //Message
        $(".reply_message").click(function(e) {
            e.preventDefault(); // Prevent the default form submission
            $(".pre-loading").css("display", "flex");
            // Collect form data
            var replyMessage = $("#reply-message").val();
            var receiverId = $(this).data("receiver-id"); // Pass receiver ID from the form data
            if (replyMessage) {
                // Perform AJAX request
                $.ajax({
                    type: "POST",
                    url: ajax_object.ajax_url, // WordPress AJAX URL provided via wp_localize_script
                    data: {
                        action: "send_reply_message", // Action hook to handle the AJAX request
                        receiver_id: receiverId,
                        message: replyMessage,
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Reload the page to show the new message
                        } else {
                            alert("Failed to send reply.");
                            $(".pre-loading").css("display", "none");
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error("AJAX Error:", textStatus, errorThrown);
                    },
                });
            } else {
                $(".pre-loading").css("display", "none");
                alert("Please input message First");
            }
        });

        //Get New message
        setInterval(function() {
            // Create an invisible audio element and append it to the body
            // Get the audio element by its ID
            const audio = $("#audiomsgesound")[0];

            $.ajax({
                type: "POST",
                url: ajax_object.ajax_url, // WordPress AJAX URL provided via wp_localize_script
                data: {
                    action: "get_unread_message_notification", // Action hook to handle the AJAX request in your functions.php
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        // Handle success response
                        // Reload the window
                        //need to play an audio here. Audio is in the same folder of this js file. audio file name is m.mp3
                        // Create an audio element and set its source

                        // Play the audio when desired
                        audio.play().catch(function(error) {
                            console.error("Playback failed:", error);
                        });

                        $.toast({
                            heading: "New Message",
                            text: response.m,
                            icon: "info",
                            showHideTransition: "slide",
                            position: "bottom-right",
                            loaderBg: "#3b8dbd",
                            hideAfter: 9000, // Hides after 9 seconds
                            stack: false,
                            bgColor: "#A0743B",
                            textColor: "white",
                        });
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle error
                    console.error("Error:", errorThrown);
                },
            });
        }, 3000);
    });
</script>
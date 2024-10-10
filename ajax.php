<?php

function add_new_teacher()
{
    // Validate and sanitize input fields
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // New taxonomy fields
    $expertise = sanitize_text_field($_POST['expertise']);
    $region = sanitize_text_field($_POST['region']);
    $grade = sanitize_text_field($_POST['grade']);

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        wp_send_json_error(['message' => 'Passwords do not match.']);
    }

    // Create the user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
        error_log('message = ' . $user_id->get_error_message());
    }

    // Process profile picture upload
    if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
        $profile_picture = upload_image($_FILES['profile_picture']);
        if ($profile_picture && !is_wp_error($profile_picture)) {
            update_user_meta($user_id, 'profile_picture', $profile_picture);
        } else {
            wp_send_json_error(['message' => 'Profile picture upload failed.']);
        }
    }

    // Process cover image upload
    if (isset($_FILES['cover_image']) && !empty($_FILES['cover_image']['name'])) {
        $cover_image = upload_image($_FILES['cover_image']);
        if ($cover_image && !is_wp_error($cover_image)) {
            update_user_meta($user_id, 'cover_image', $cover_image);
        } else {
            wp_send_json_error(['message' => 'Cover image upload failed.']);
        }
    }

    // Assign the 'teacher' role to the user
    $user = new WP_User($user_id);
    $user->set_role('teacher');

    // Create a new post of type 'teacher'
    $post_data = [
        'post_title'   => $first_name . ' ' . $last_name,
        'post_status'  => 'draft',
        'post_type'    => 'teacher',
        'post_author'  => $user_id
    ];
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Teacher post creation failed.']);
        return;
    }

    // Add the user data as meta fields to the post
    update_post_meta($post_id, 'first_name', $first_name);
    update_post_meta($post_id, 'last_name', $last_name);
    update_post_meta($post_id, 'phone_number', $phone_number);

    // Assign taxonomy terms to the post (Grade, Region, Expertise)
    if (!empty($expertise)) {
        wp_set_post_terms($post_id, $expertise, 'expertise');
    }
    if (!empty($region)) {
        wp_set_post_terms($post_id, $region, 'region');
    }
    if (!empty($grade)) {
        wp_set_post_terms($post_id, $grade, 'grade');
    }

    // Set the profile picture as the post thumbnail (featured image)
    if (isset($profile_picture)) {
        set_post_thumbnail($post_id, $profile_picture);
    }

    // Set the cover image as post meta (if needed for additional display)
    if (isset($cover_image)) {
        update_post_meta($post_id, 'cover_image', $cover_image);
    }

    // Send success response
    wp_send_json_success(['message' => 'User and teacher profile created successfully!']);
}


add_action('wp_ajax_add_new_teacher', 'add_new_teacher');
add_action('wp_ajax_nopriv_add_new_teacher', 'add_new_teacher'); // For non-logged-in users (optional)


// Helping function of add new teacher
function upload_image($file)
{
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Handle the file upload
    $upload = wp_handle_upload($file, ['test_form' => false]);

    // If the upload was successful, proceed to insert the file as an attachment
    if (!isset($upload['error'])) {
        $filetype = wp_check_filetype($upload['file']);
        $attachment = array(
            'guid'           => $upload['url'],
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name($file['name']),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment into the WordPress media library
        $attachment_id = wp_insert_attachment($attachment, $upload['file']);

        // Generate the metadata for the attachment, and update the database record.
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        return $attachment_id; // Return the attachment ID
    }

    // If there was an error, return the error message
    return new WP_Error('upload_failed', $upload['error']);
}

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
        'post_title'  => 'Reply from ' . get_userdata($sender_id)->display_name . ' To: ' . get_userdata($receiver_id)->display_name,
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


function invitation_send_to_teacher()
{
    // Check if the user is logged in
    if ( ! is_user_logged_in() ) {
        wp_send_json_error('You must be logged in to send invitations.');
        return;
    }

    // Sanitize and retrieve form data
    $sender_id = get_current_user_id();

    $student_id = isset($_POST['student']) ? intval($_POST['student']) : 0;
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
    $time = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : '';
    $amount = isset($_POST['amount']) ? sanitize_text_field($_POST['amount']) : '';
    $length = isset($_POST['length']) ? sanitize_text_field($_POST['length']) : '';

    error_log($sender_id);
    error_log($date);
    error_log($time);
    error_log($amount);
    error_log($length);

    // Check if required fields are filled
    if (empty($student_id) || empty($date) || empty($time)) {
        wp_send_json_error('Please fill in all required fields.');
        return;
    }

    // Create a new message post
    $message_id = wp_insert_post(array(
        'post_type'   => 'message',
        'post_title'  => 'Reply from ' . get_userdata($sender_id)->display_name . ' To: ' . get_userdata($student_id)->display_name,
        'post_content' => 'Invitation', // You can modify this to include more info if needed
        'post_status' => 'publish',
        'meta_input'  => array(
            'sender_id'    => $sender_id,
            'receiver_id'  => $student_id,
            'date'         => $date,
            'time'         => $time,
            'amount'       => $amount,
            'length'       => $length,
            'is_read'      => 0, // Default to unread
            'is_notified'  => 0, // Default to unnotified
        ),
    ));

    // Check if the post was created successfully
    if ($message_id) {
        wp_send_json_success('Invitation sent successfully.');
    } else {
        wp_send_json_error('Failed to send invitation.');
    }
}

add_action('wp_ajax_invitation_send_to_teacher', 'invitation_send_to_teacher');
add_action('wp_ajax_nopriv_invitation_send_to_teacher', 'invitation_send_to_teacher');




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
                'm' => 'You have a new message from <strong><a href="' . home_url('/chat?r='.$id) . '">' . $display_name . '</a></strong>'
            ]);
        } else {
            wp_send_json([
                'success' => true,
                'm' => 'You have ' . $total . 'New Messages <a href="' . home_url('/chat?r='.$id) . '"> Chat </a>'
            ]);
        }
    } else {
        wp_send_json_error('No Unread Message');
    }
}
add_action('wp_ajax_get_unread_message_notification', 'get_unread_message_notification');
add_action('wp_ajax_nopriv_get_unread_message_notification', 'get_unread_message_notification');


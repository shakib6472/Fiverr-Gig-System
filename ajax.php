<?php 


function add_new_teacher() {
    // Check nonce for security (if you've set one)
    // check_ajax_referer('your_nonce_action', 'nonce');

    // Validate and sanitize input
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $expertise = sanitize_text_field($_POST['expertise']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    error_log('Sanitization done.');
    
    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        wp_send_json_error(['message' => 'Passwords do not match.']);
        error_log('Passwords do not match.');
        error_log('Password not match.');
    }
    
    // Create the user
    $user_id = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
        error_log('message = '. $user_id->get_error_message());
    }
    
    // Set additional user meta
    update_user_meta($user_id, 'first_name', $first_name);
    update_user_meta($user_id, 'last_name', $last_name);
    update_user_meta($user_id, 'phone_number', $phone_number);
    update_user_meta($user_id, 'expertice', $expertise);
    error_log('Metadata updated.');
    
    // Process profile picture upload
    if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
        $profile_picture = upload_image($_FILES['profile_picture']);
        if ($profile_picture && !is_wp_error($profile_picture)) {
            error_log('Profile picture updated.');
            update_user_meta($user_id, 'profile_picture', $profile_picture); // Save the file URL in user meta
        } else {
            wp_send_json_error(['message' => 'Profile picture upload failed.']);
        }
    }
    
    // Process cover image upload
    if (isset($_FILES['cover_image']) && !empty($_FILES['cover_image']['name'])) {
        $cover_image = upload_image($_FILES['cover_image']);
        if ($cover_image && !is_wp_error($cover_image)) {
            update_user_meta($user_id, 'cover_image', $cover_image); // Save the file URL in user meta
            error_log('Cover picture updated.');
        } else {
            wp_send_json_error(['message' => 'Cover image upload failed.']);
        }
    }

    // Assign the 'teacher' role to the user
    $user = new WP_User($user_id);
    $user->set_role('teacher');

    // Send success response
    wp_send_json_success(['message' => 'User created successfully!']);
}

add_action('wp_ajax_add_new_teacher', 'add_new_teacher');
add_action('wp_ajax_nopriv_add_new_teacher', 'add_new_teacher'); // For non-logged-in users (optional)




function upload_image($file) {
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

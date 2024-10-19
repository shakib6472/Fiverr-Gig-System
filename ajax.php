<?php



add_action('wp_ajax_update_payment_status', 'update_payment_status');
add_action('wp_ajax_nopriv_update_payment_status', 'update_payment_status');

function update_payment_status()
{
    // Capture the PayPal Order ID, amount, and other data from the AJAX request
    $payment_id = sanitize_text_field($_POST['payment_id']);
    $amount = sanitize_text_field($_POST['amount']);
    $all_data = $_POST['all']; // All the data from PayPal
    $actions = $_POST['actions']; // PayPal actions data

    // Log the captured data to WordPress debug.log
    error_log("PayPal Payment ID: " . $payment_id);
    error_log("Payment Amount: " . $amount);
    error_log("All Data from PayPal: " . print_r(json_decode(stripslashes($all_data), true), true)); // Logs all data from PayPal
    error_log("Actions Data from PayPal: " . print_r(json_decode(stripslashes($actions), true), true)); // Logs actions data

    // Respond with a success message
    wp_send_json_success('Payment data logged successfully.');

    wp_die(); // Always call this at the end of WordPress AJAX functions
}

// Handle the AJAX login request
function ajax_fiverr_market_login()
{

    // Get the username and password from the AJAX request
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);

    error_log('username: '. $username);
    error_log('password: '. $password);

    // Try to authenticate the user
    $user = wp_signon(array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true,
    ));

    error_log(print_r($user, true));

    if (is_wp_error($user)) {
        // If authentication fails, send an error message
        wp_send_json_error(array(
            'message' => $user->get_error_message(),
        ));
    } else {
        // On success, send a redirect URL (e.g., to the dashboard)
        wp_send_json_success(array(
            'redirect_url' => home_url(), // Change to desired URL
        ));
    }
    wp_die(); // Always terminate to avoid 0 output in responses
}

// Add the AJAX action for both logged-in and non-logged-in users
add_action('wp_ajax_nopriv_ajax_fiverr_market_login', 'ajax_fiverr_market_login');  // For non-logged-in users
add_action('wp_ajax_ajax_fiverr_market_login', 'ajax_fiverr_market_login');          // For logged-in users (just in case)


function add_new_teacher()
{
    // Log the start of the function
    error_log('Starting add_new_teacher function');

    // Validate and sanitize input fields
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Log form inputs for debugging
    error_log('First Name: ' . $first_name);
    error_log('Last Name: ' . $last_name);
    error_log('Username: ' . $username);
    error_log('Email: ' . $email);
    error_log('Phone Number: ' . $phone_number);
    error_log('Password and Confirm Password match: ' . ($password === $confirm_password ? 'Yes' : 'No'));

    // New taxonomy fields
    $expertise = json_decode(stripslashes($_POST['expertise']), true); // Decode the JSON string into an array
    $region = sanitize_text_field($_POST['region']);
    $grade = json_decode(stripslashes($_POST['grade']), true); // Assuming grade is also an array (like expertise)

    // Log taxonomy fields for debugging
    error_log('Expertise: ' . print_r($expertise, true));
    error_log('Region: ' . $region);
    error_log('Grade: ' . print_r($grade, true));

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        error_log('Passwords do not match.');
        wp_send_json_error(['message' => 'Passwords do not match.']);
        return;
    }

    // Create the user
    $user_id = wp_create_user($username, $password, $email);

    // Log user creation status
    if (is_wp_error($user_id)) {
        error_log('User creation error: ' . $user_id->get_error_message());
        wp_send_json_error(['message' => $user_id->get_error_message()]);
        return;
    } else {
        error_log('User created successfully with ID: ' . $user_id);
    }

    // Process profile picture upload
    if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
        $profile_picture = upload_image($_FILES['profile_picture']);
        if ($profile_picture && !is_wp_error($profile_picture)) {
            update_user_meta($user_id, 'profile_picture', $profile_picture);
            error_log('Profile picture uploaded successfully.');
        } else {
            error_log('Profile picture upload failed.');
            wp_send_json_error(['message' => 'Profile picture upload failed.']);
            return;
        }
    }

    // Process cover image upload
    if (isset($_FILES['cover_image']) && !empty($_FILES['cover_image']['name'])) {
        $cover_image = upload_image($_FILES['cover_image']);
        if ($cover_image && !is_wp_error($cover_image)) {
            update_user_meta($user_id, 'cover_image', $cover_image);
            error_log('Cover image uploaded successfully.');
        } else {
            error_log('Cover image upload failed.');
            wp_send_json_error(['message' => 'Cover image upload failed.']);
            return;
        }
    }

    // Assign the 'teacher' role to the user
    $user = new WP_User($user_id);
    $user->set_role('teacher');
    error_log('Teacher role assigned to user.');

    // Create a new post of type 'teacher'
    $post_data = [
        'post_title'   => $first_name . ' ' . $last_name,
        'post_status'  => 'draft',
        'post_type'    => 'teacher',
        'post_author'  => $user_id
    ];
    $post_id = wp_insert_post($post_data);

    // Log post creation status
    if (is_wp_error($post_id)) {
        error_log('Teacher post creation failed: ' . $post_id->get_error_message());
        wp_send_json_error(['message' => 'Teacher post creation failed.']);
        return;
    } else {
        error_log('Teacher post created successfully with ID: ' . $post_id);
    }

    // Add the user data as meta fields to the post
    update_post_meta($post_id, 'first_name', $first_name);
    update_post_meta($post_id, 'last_name', $last_name);
    update_post_meta($post_id, 'phone_number', $phone_number);

    // Assign taxonomy terms to the post (Grade, Region, Expertise)
    // Assign taxonomy terms to the post (Grade, Region, Expertise)
    if (!empty($expertise) && is_array($expertise)) {
        $expertise = array_map('sanitize_text_field', $expertise);
        $result = wp_set_post_terms($post_id, $expertise, 'expertise');
        error_log('Expertise terms set result: ' . print_r($result, true)); // Log result of setting expertise terms
    }

    if (!empty($grade) && is_array($grade)) {
        $grade = array_map('sanitize_text_field', $grade);
        $result = wp_set_post_terms($post_id, $grade, 'grade');
        error_log('Grade terms set result: ' . print_r($result, true)); // Log result of setting grade terms
    }

    if (!empty($region)) {
        $result = wp_set_post_terms($post_id, $region, 'region');
        error_log('Region terms set result: ' . print_r($result, true)); // Log result of setting region terms
    }

    // Set the profile picture as the post thumbnail (featured image)
    if (isset($profile_picture)) {
        set_post_thumbnail($post_id, $profile_picture);
        error_log('Profile picture set as featured image.');
    }

    // Set the cover image as post meta
    if (isset($cover_image)) {
        update_post_meta($post_id, 'cover_image', $cover_image);
        error_log('Cover image set as post meta.');
    }

    // Send success response
    error_log('User and teacher profile created successfully.');
    wp_send_json_success(['message' => 'User and teacher profile created successfully!']);
}


add_action('wp_ajax_add_new_teacher', 'add_new_teacher');
add_action('wp_ajax_nopriv_add_new_teacher', 'add_new_teacher'); // For non-logged-in users (optional)

function register_student()
{
    // Check for required fields
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        wp_send_json_error(['message' => 'Required fields are missing']);
        return;
    }

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $username   = sanitize_text_field($_POST['username']);
    $email      = sanitize_email($_POST['email']);
    $password   = $_POST['password'];
    $parent_email = isset($_POST['parent_email']) ? sanitize_email($_POST['parent_email']) : '';

    // Ensure username and email are not already taken
    if (username_exists($username) || email_exists($email)) {
        wp_send_json_error(['message' => 'Username or email already exists.']);
        return;
    }

    // Create the user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
        return;
    }

    // Set user role as subscriber (default)
    wp_update_user([
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ]);

    // Set additional user meta if needed (e.g., parent email)
    if (!empty($parent_email)) {
        update_user_meta($user_id, 'parent_email', $parent_email);
    }

    // Assign the user the "subscriber" role
    $user = new WP_User($user_id);
    $user->set_role('subscriber');

    // Log the user in
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true); // Auto-login the user

    // Get the dashboard URL (you can customize this based on user role)
    $dashboard_url = home_url('/dashboard'); // Redirect to the dashboard (you can customize this)

    // Return success response with the dashboard URL
    wp_send_json_success(['dashboard_url' => $dashboard_url]);
}

add_action('wp_ajax_nopriv_register_student', 'register_student');
add_action('wp_ajax_register_student', 'register_student');


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
    if (! is_user_logged_in()) {
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
                'm' => 'You have a new message from <strong><a href="' . home_url('/chat?r=' . $id) . '">' . $display_name . '</a></strong>'
            ]);
        } else {
            wp_send_json([
                'success' => true,
                'm' => 'You have ' . $total . 'New Messages <a href="' . home_url('/chat?r=' . $id) . '"> Chat </a>'
            ]);
        }
    } else {
        wp_send_json_error('No Unread Message');
    }
}
add_action('wp_ajax_get_unread_message_notification', 'get_unread_message_notification');
add_action('wp_ajax_nopriv_get_unread_message_notification', 'get_unread_message_notification');

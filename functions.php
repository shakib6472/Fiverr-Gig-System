<?php 

// Add the 'teacher' role with specific capabilities
add_role('teacher', 'Teacher', array(
    'read' => true, // Can read posts and pages
    'edit_posts' => true, // Can edit their own posts
    'delete_posts' => true, // Can delete their own posts
    'publish_posts' => true, // Can publish their own posts
    'upload_files' => true, // Can upload files
));



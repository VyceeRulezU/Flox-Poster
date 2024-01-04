<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    // Check if title and content are provided
    if (!empty($_POST['post_title']) && !empty($_POST['post_content'])) {
        $post_title = sanitize_text_field($_POST['post_title']);
        $post_content = wp_kses_post($_POST['post_content']);

        // Check if post title already exists
        $existing_post = get_page_by_title($post_title, OBJECT, 'post');

        if (!$existing_post) {
            // Create a new post
            $new_post = array(
                'post_title'   => $post_title,
                'post_content' => $post_content,
                'post_status'  => 'publish',
                'post_author'  => get_current_user_id(),
                'post_type'    => 'post',
            );

            $post_id = wp_insert_post($new_post);

            if ($post_id) {
                // Return link to the new post
                echo '<p>Post created successfully! View it <a href="' . get_permalink($post_id) . '">here</a>.</p>';
            } else {
                echo '<p>Error creating post.</p>';
            }
        } else {
            // Post title already exists
            echo '<p>Error: Post title already exists.</p>';
        }
    } else {
        // Title or content is empty
        echo '<p>Error: Title and content are required.</p>';
    }
}
?>

<script>
    jQuery(document).ready(function ($) {
        // Title input element
        var titleInput = $('#post_title');

        // Submit button
        var submitButton = $('input[type="submit"]');

        // Function to check if the title exists
        function checkTitle() {
            var title = titleInput.val();

            // Perform AJAX request to the REST endpoint
            $.ajax({
                url: '<?php echo rest_url('filoxposter/v1/check-title'); ?>',
                method: 'POST',
                data: {
                    title: title,
                },
                success: function (response) {
                    if (response.exists) {
                        // Title exists, show appropriate message and disable submit button
                        alert('Error: Title already exists.');
                        submitButton.prop('disabled', true);
                    } else {
                        // Title is unique, enable submit button
                        submitButton.prop('disabled', false);
                    }
                },
            });
        }

        // Event listener for title input change
        titleInput.on('input', function () {
            checkTitle();
        });

        // Check title on page load
        checkTitle();
    });
</script>

<form method="post">
    <label for="post_title">Title:</label>
    <input type="text" name="post_title" id="post_title" required>

    <label for="post_content">Content:</label>
    <textarea name="post_content" id="post_content" required></textarea>

    <input type="submit" value="Submit">
</form>


$(document).ready(function () {
  console.log('initiate 1.1');

  
  console.log('initiate 1.10');
  // Custom validation with jQuery
  $("#registration_form").on("submit", function (event) {
    var form = $(this)[0];

    // Prevent submission if form is invalid
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      $('.pre-loader').css('display', 'none');
    } else {
      $('.pre-loader').css('display', 'flex');
      event.preventDefault(); // Stop form submission

      var formData = new FormData();
      formData.append('action', 'add_new_teacher'); // Pass the action
      formData.append('first_name', $("#first_name").val());
      formData.append('last_name', $("#last_name").val());
      formData.append('username', $("#uusername").val());
      formData.append('email', $("#email").val());
      formData.append('phone_number', $("#phone_number").val());
      formData.append('expertise', $("#expertise").val());
      formData.append('password', $("#ppassword").val());
      formData.append('confirm_password', $("#confirm_password").val());
      formData.append('terms_conditions', $("#terms_conditions").is(":checked"));

      // Append file inputs to FormData
      var profilePictureFile = $('#profile_picture')[0].files[0];
      var coverImageFile = $('#cover_image')[0].files[0];

      formData.append('profile_picture', profilePictureFile);
      formData.append('cover_image', coverImageFile);

      console.log(formData);


      $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url, // WordPress AJAX URL provided via wp_localize_script
        data: formData,
        processData: false, // Prevent jQuery from automatically processing the data
        contentType: false, // Prevent jQuery from overriding the content type
        dataType: 'json',
        success: function (response) {
          // Handle success response
          $('.pre-loader').css('display', 'none');
          console.log(response);
          // Reload the window
          // location.reload();
        },
        error: function (xhr, textStatus, errorThrown) {
          // Handle error
          console.error('Error:', errorThrown);
        }
      });

    }

    form.classList.add("was-validated");
  });

  // Image preview for Profile Picture
  $("#profile_picture").change(function () {
    readURL(this, "#profile_preview");
  });

  // Image preview for Cover Image
  $("#cover_image").change(function () {
    readURL(this, "#cover_preview");
  });

  // Function to display the preview - Helping
  function readURL(input, previewElement) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $(previewElement).attr("src", e.target.result);
        $(previewElement).show();
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  //Message
  $(".reply_message").click(function (e) {
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
        success: function (response) {
          if (response.success) {
            location.reload(); // Reload the page to show the new message
          } else {
            alert("Failed to send reply.");
            $(".pre-loading").css("display", "none");
          }
        },
        error: function (xhr, textStatus, errorThrown) {
          console.error("AJAX Error:", textStatus, errorThrown);
        },
      });
    } else {
      $(".pre-loading").css("display", "none");
      alert("Please input message First");
    }
  });

  //Get New message
  setInterval(function () {
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
      success: function (response) {
        console.log(response);
        if (response.success) {
          // Handle success response
          // Reload the window
          //need to play an audio here. Audio is in the same folder of this js file. audio file name is m.mp3
          // Create an audio element and set its source

          // Play the audio when desired
          audio.play().catch(function (error) {
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
      error: function (xhr, textStatus, errorThrown) {
        // Handle error
        console.error("Error:", errorThrown);
      },
    });
  }, 3000);


});

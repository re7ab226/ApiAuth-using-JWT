<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bootstrap Site</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body>
<h1>Hello-Bootstrap</h1>
<form id="form_register" >
    <input type="text" name="name" placeholder="name">
    <br><br>
    <input type="email" name="email" placeholder="email">
    <br><br>

    <input type="password" name="password" placeholder="password">
    <br><br>

    <input type="submit" value="Register">


</form>
<script>
$(document).ready(function() {
    $('form#register').submit(function(event) { 
        event.preventDefault(); // Prevent default form submission
        var formData = $(this).serialize(); // Serialize form data
        
        // AJAX request
        $.ajax({
            url: "http://127.0.0.1:8000/api/register",
            type: "POST",
            data: formData,
            success: function(data) {
                console.log(data); // Logging the response data to the console
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Log error message if AJAX request fails
            }
        }); 
    });
}); 


</script>
</body>
</html>
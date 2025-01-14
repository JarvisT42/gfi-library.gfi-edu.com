<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Preview Popup</title>
    <style>
        /* Style the image container */
        .img-container {
            position: relative;
            display: inline-block;
        }

        /* Style for the popup (modal) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            padding-top: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Modal content (image) */
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;  /* Set a max-width of 90% */
            max-height: 90%; /* Set a max-height of 90% */
            object-fit: contain; /* Ensure the image fits within the max-width and max-height */
        }
    </style>
</head>
<body>

<h2>Click the image to see the preview</h2>

<!-- Image container -->
<div class="img-container">
    <img id="myImg" src="../uploads/2024-12-21_02-41-57_6628.png" alt="Preview Image" style="width:100%;max-width:300px;">
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
    <img class="modal-content" id="img01">
</div>

<script>
    // Get the image and modal elements
    var img = document.getElementById('myImg');
    var modal = document.getElementById('myModal');
    var modalImg = document.getElementById('img01');

    // Ensure the modal is hidden when the page loads
    window.onload = function() {
        modal.style.display = "none";
    }

    // When the user clicks the image, open the modal
    img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = this.src; // Set the modal image source to the clicked image's source
    }

    // Optional: Close modal if clicked outside of the image
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>

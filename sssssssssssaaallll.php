<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support Our Library Development Project</title>
 
</head>
<body>

  <!-- Button to trigger the modal -->
  <button class="open-modal-btn" id="openModalBtn">Support Our Library</button>


  <style>
    /* Basic styles for the page */
    /* Style for the button */
    .open-modal-btn {
      padding: 15px 25px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    .open-modal-btn:hover {
      background-color: #0056b3;
    }

    /* Modal background */
    .modal {
      display: none; /* Hidden by default */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
      justify-content: center;
      align-items: center;
    }

    /* Modal content */
    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      width: 600px; /* Increase the width here */
      max-width: 90%; /* Add responsiveness for smaller screens */
      text-align: center;
      max-height: 80%; /* Limit the height */
      overflow-y: auto; /* Make the content scrollable */
    }

    .modal-content h5 {
      font-size: 20px;
      color: #333;
    }

    .donation-options {
      text-align: left;
      margin-top: 20px;
    }

    .donation-options h3 {
      color: #007bff;
    }

    .qr-options button {
      margin: 5px;
      padding: 10px;
      font-size: 14px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .qr-options button:hover {
      background-color: #218838;
    }

    .qr-code-display {
      margin-top: 15px;
      display: flex;
      justify-content: space-around;
    }

    .qr-code {
      max-width: 100px;
      height: auto;
      display: block;
    }

    .close-btn {
      background-color: #ff0000;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 20px;
    }

    .close-btn:hover {
      background-color: #cc0000;
    }
  </style>
  <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-content">
      <h5>Support Our Library Development Project</h5>
      
      <div class="custom-modal-body">
        <h2>Support Our Library Development Project</h2>
        <p>We are fourth-year students at Gensantos Foundation College, Inc., dedicated to creating a powerful and user-friendly Library Management System to benefit all students. Your support means a lot to us, as it helps us maintain, improve, and expand this project.</p>
        <p>If you'd like to support us, here are several ways to make a contribution:</p>

        <!-- E-Wallet Donation Options -->
        <div class="donation-options">
          <h3>E-Wallet Options:</h3>
          <p>Click on the buttons below to view the QR code for each e-wallet service:</p>
          <div class="qr-options">
            <button onclick="showQRCode('gcash')">Gcash</button>
            <button onclick="showQRCode('paymaya')">Paymaya</button>
            <button onclick="showQRCode('paypal')">PayPal</button>
          </div>
          <div class="qr-code-display">
            <img id="gcashQRCode" src="src/assets/images/gcash.png" alt="Gcash QR Code" class="qr-code">
            <img id="paymayaQRCode" src="./src/images/paymaya.png" alt="Paymaya QR Code" class="qr-code" style="display: none;">
            <img id="paypalQRCode" src="./src/images/paypal.png" alt="PayPal QR Code" class="qr-code" style="display: none;">
          </div>
          <p><strong>Gcash ID:</strong> 09123456789</p>
          <p><strong>Paymaya ID:</strong> 09876543210</p>
          <p><strong>PayPal ID:</strong> yourname@example.com</p>
        </div>

        <!-- Blockchain Donation Options -->
        <div class="donation-options">
          <h3>Blockchain Donation Options:</h3>
          <p>You can also support us via blockchain! Here are our addresses for different chains:</p>
          <p><strong>BNB Chain:</strong> 0x123456789ABCDEF123456789ABCDEF1234567890</p>
          <p><strong>Ethereum Chain:</strong> 0x0987654321ABCDEF0987654321ABCDEF09876543</p>
          <p><strong>Polygon Chain:</strong> 0xABCDE123456789ABCDE123456789ABCDE1234567</p>
        </div>

        <p>Any amount you choose to give is greatly appreciated and will go directly towards making this system the best resource it can be for students like you. Thank you for considering a donation!</p>

        <!-- Close button inside the modal -->
        <button class="close-btn" id="closeModalBtn">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Get elements
    const openModalBtn = document.getElementById('openModalBtn');
    const myModal = document.getElementById('myModal');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Open the modal when the button is clicked
    openModalBtn.addEventListener('click', function() {
      myModal.style.display = 'flex'; // Show the modal
    });

    // Close the modal when the close button is clicked
    closeModalBtn.addEventListener('click', function() {
      myModal.style.display = 'none'; // Hide the modal
    });

    // Close the modal if the user clicks outside the modal content
    window.addEventListener('click', function(event) {
      if (event.target === myModal) {
        myModal.style.display = 'none';
      }
    });

    // Show QR code for the selected wallet
    function showQRCode(wallet) {
      document.getElementById('gcashQRCode').style.display = 'none';
      document.getElementById('paymayaQRCode').style.display = 'none';
      document.getElementById('paypalQRCode').style.display = 'none';

      if (wallet === 'gcash') {
        document.getElementById('gcashQRCode').style.display = 'block';
      } else if (wallet === 'paymaya') {
        document.getElementById('paymayaQRCode').style.display = 'block';
      } else if (wallet === 'paypal') {
        document.getElementById('paypalQRCode').style.display = 'block';
      }
    }
  </script>

</body>
</html>

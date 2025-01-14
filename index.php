<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="" />
  <meta name="author" content="webthemez" />
  <title>GFI-Library</title>
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/animate.min.css" rel="stylesheet" />
  <link href="css/prettyPhoto.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <link rel="shortcut icon" href="images/ico/favicon.ico" />
</head>

<body id="home">

<header id="header">
    <nav id="main-nav" class="navbar navbar-default navbar-fixed-top" role="banner">
      <div class="container-fluid">
        <div class="navbar-header">
          <button
            type="button"
            class="navbar-toggle"
            data-toggle="collapse"
            data-target=".navbar-collapse"
          >
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">
            <img src="./src/assets/images/library.png" alt="logo" class="logo" />
            <img src="./src/assets/images/350861720_596709698913796_561423606268093637_n.png" alt="logo" class="logo" />
            <span class="navbar-title">Gensantos Foundation College, Inc. Library</span>
          </a>
        </div>

        <div class="collapse navbar-collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="scroll active"><a href="#home">Home</a></li>
            <li class="scroll"><a href="#services">Features</a></li>
            <li class="scroll"><a href="#about">About</a></li>
            <li class="scroll"><a href="#contact-us">Contact</a></li>
            <li class="scroll"><a href="https://gfi-edu.com/">Portal</a></li>
            <li class="scroll">
              <a href="login.php" class="btn btn-login">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>



  <section id="hero-banner">
    <!-- Slide 1 -->
    <div
      class="slide active"
      style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./src/assets/images/background.png');">
      <div class="slide-content">
        <h2>Stronger than <b>EVER</b></h2>

        <p>Access our vast collection of books, research materials, and resources online. Learn, explore, and grow from anywhere, anytime.</p>
        <a href="login.php">Start Now</a>
      </div>
    </div>

    <!-- Slide 2 -->
    <div
      class="slide"
      style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),  url('./src/assets/images/Screenshot 2024-08-30 203504.png');
        ">
      <div class="slide-content">
        <h2>Join Our Journey</h2>
        <p>
          Praesent eget risus vitae massa semper aliquam quis mattis quam.
        </p>
        <a href="#">Join Us</a>
      </div>
    </div>

    <!-- Slide 3 -->
    <div
      class="slide"
      style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),  url('./src/assets/images/gfi-library.png')">
      <div class="slide-content">
        <h2>Become Stronger</h2>
        <p>Morbi vitae tortor tempus, placerat leo et, suscipit lectus.</p>
        <a href="#">Learn More</a>
      </div>
    </div>

    <div
      class="slide"
      style="background-image: url('./src/assets/images/mainlib2.upd.png')">

    </div>

    <div
      class="slide"
      style="background-image: url('./src/assets/images/mainlib3.upd.png')"></div>
    <div
      class="slide"
      style="background-image: url('./src/assets/images/mainlib.upd.png')"></div>
  </section>

  <script>
    // JavaScript for Slideshow
    let currentSlide = 0;
    const slides = document.querySelectorAll(".slide");
    const totalSlides = slides.length;
    const slideInterval = 5000; // 3 seconds

    function showSlide(index) {
      slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if (i === index) {
          slide.classList.add("active");
        }
      });
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      showSlide(currentSlide);
    }

    setInterval(nextSlide, slideInterval);
  </script>

  <section id="search-engine">
    <div class="container text-center d-flex justify-content-center">
      <div class="section-header">
        <h2 class="section-title wow fadeInDown" style="color: #9C1414;">

          Search Our Library
        </h2>
        <p class="wow fadeInDown">
          Find books, journals, articles, and resources in our extensive
          online library collection.
        </p>
      </div>

      <form class="search-form d-flex mx-auto" action="search.php" method="GET">
        <select class="form-select" name="table" aria-label="Category">
          <option value="All fields" selected>All Fields</option>
          <!-- Dynamically populate tables -->
          <?php
          include 'connection2.php';
          $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
          $result = $conn2->query($sql);
          $excludedTable = "e-books";

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_array()) {
              if ($row[0] !== $excludedTable) {
                echo '<option value="' . htmlspecialchars($row[0]) . '">' . htmlspecialchars($row[0]) . '</option>';
              }
            }
          }
          ?>
        </select>
        <input type="text" name="search" class="form-control" placeholder="Search by Title, Author, Keyword or ISBN" required>
        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
      </form>
    </div>
  </section>

  <section id="services">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title wow fadeInDown">Library Features</h2>
        <p class="wow fadeInDown">
          Discover our comprehensive online borrowing system designed to make
          your library experience seamless and efficient.
        </p>
      </div>

      <div class="row">
        <!-- Online Borrowing -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="0ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-book"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">Online Borrowing</h4>
              <p>
                Easily borrow books online from the comfort of your home. No
                need to visit the library; simply reserve your book online and
                pick it up when it’s ready.
              </p>
            </div>
          </div>
        </div>

        <!-- View Available Books -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="100ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-eye"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">View Available Books</h4>
              <p>
                Browse our extensive catalog to see which books are currently
                available. Filter by genre, author, or publication date to
                find what you're looking for quickly.
              </p>
            </div>
          </div>
        </div>

        <!-- Account Management -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="200ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-user"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">Account Management</h4>
              <p>
                Manage your borrowing history, track due dates, and view fines
                or fees all from your personal library account dashboard.
              </p>
            </div>
          </div>
        </div>

        <!-- Book Recommendations -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="300ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-thumbs-up"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">Discover New Books
              </h4>
              <p>
                Explore our curated selection of popular titles and genres. Browse through new arrivals, bestsellers, and trending books to find your next great read.
              </p>
            </div>
          </div>
        </div>

        <!-- Digital Library Access -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="400ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-laptop"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">Digital Library Access</h4>
              <p>
                Access a wide range of e-books and digital resources directly
                from your account. Ideal for remote learning and research.
              </p>
            </div>
          </div>
        </div>

        <!-- Notifications and Alerts -->
        <div
          class="col-lg-4 col-md-6 col-sm-12 wow fadeInUp"
          data-wow-duration="300ms"
          data-wow-delay="500ms">
          <div class="media service-box">
            <div class="pull-left">
              <i class="fa fa-bell"></i>
            </div>
            <div class="media-body">
              <h4 class="media-heading">Notifications & Alerts</h4>
              <p>
                Stay updated with in-platform notifications for due dates, new arrivals, and special library events. Easily check reminders and updates right within your library account.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="about" style="font-family: Arial, sans-serif; line-height: 1.8;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; ">
      <div class="section-header" style="text-align: center; margin-bottom: 20px;">
        <h2 class="section-title wow fadeInDown" style="font-size: 2.5em; font-weight: bold; margin-bottom: 10px;">About Us</h2>
        <p class="wow fadeInDown" style="font-size: 1.3em;">
          Welcome to the Gensantos Foundation College, Inc. Library – a center of knowledge, learning, and innovation designed to support the academic and personal growth of our students and faculty.
        </p>
      </div>

      <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px;">
        <!-- Left Column -->
        <div class="col-sm-6 wow fadeInLeft" style="flex: 1; min-width: 300px;">
          <h3 class="column-title" style="font-size: 2em; font-weight: bold;">Our Mission & Vision</h3>
          <p style="font-size: 1.3em;">
            <strong>VISION</strong><br>
            GFI Library envisions becoming a leading College Learning Resource Center in all types of information sources in the fields of Accountancy, Business and Management, Education, and Information and Communication Technology. It aims for reliable, rapid access, easy retrieval, and transfer of relevant information to its users, establishing linkages with other academic libraries globally.<br><br>
            <strong>MISSION</strong><br>
            The College Library exists to support the vision and mission of Gensantos Foundation College Inc. and the goals and objectives of its various curricular programs, providing excellent library services in support of instruction, research, and other scholarly activities.
          </p>
        </div>

        <!-- Right Column -->
        <div class="col-sm-6 wow fadeInRight" style="flex: 1; min-width: 300px;">
          <h3 class="column-title" style="font-size: 2em; font-weight: bold;">What We Offer</h3>
          <ul class="listarrow" style="list-style: none; padding: 0; font-size: 1.3em; color: #555;">
            <li style="margin-bottom: 10px;"><i class="fa fa-angle-double-right" style="margin-right: 8px; color: #007bff;"></i>Extensive Collection: Books, journals, and digital resources across multiple disciplines.</li>
            <li style="margin-bottom: 10px;"><i class="fa fa-angle-double-right" style="margin-right: 8px; color: #007bff;"></i>Modern Borrowing System: Reserve and borrow books online for added convenience.</li>
            <li style="margin-bottom: 10px;"><i class="fa fa-angle-double-right" style="margin-right: 8px; color: #007bff;"></i>Personalized Services: Manage borrowing history, track due dates, and receive tailored services.</li>
            <li style="margin-bottom: 10px;"><i class="fa fa-angle-double-right" style="margin-right: 8px; color: #007bff;"></i>Digital Library Access: 24/7 access to e-books, research articles, and other digital materials.</li>
            <li style="margin-bottom: 10px;"><i class="fa fa-angle-double-right" style="margin-right: 8px; color: #007bff;"></i>Community Engagement: Events, book clubs, and more to promote knowledge-sharing.</li>
          </ul>
        </div>
      </div>
    </div>
  </section>





  <section class="information-area" id="information">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title wow fadeInDown">Project Information</h2>
        <p class="wow fadeInDown">
          The Online Library System is a capstone project developed by the fourth-year students for the academic year 2024/2025. This project serves as part of the fulfillment of the requirements for the degree of Bachelor of Science in Information Systems. The system aims to enhance the accessibility and management of library resources, providing an intuitive interface for both students and staff to manage books, reservations, and borrowing processes.
        </p>
        <p class="wow fadeInDown">
          In partial fulfillment of the requirements for the degree, this project was developed by:
        </p>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="single-information animate_fade_in" style="opacity: 1; right: 0px">
            <div class="row">
              <div class="col-xs-12">
                <blockquote>
                  "Our project aims to simplify the library experience for students while also helping the library staff manage resources more efficiently."
                </blockquote>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3">
                <img src="images/student1.jpg" alt="student" />
              </div>
              <div class="col-xs-9 half-gutter">
                <h5>DABORBOR, KENT JOSHUA</h5>
                <h6>Project Lead / Developer</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="single-information animate_fade_in" style="opacity: 1; right: 0px">
            <div class="row">
              <div class="col-xs-12">
                <blockquote>
                "I focused on quality assurance, ensuring the system was thoroughly tested and performed as intended to deliver a seamless user experience."                </blockquote>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3">
                <img src="images/student2.jpg" alt="student" />
              </div>
              <div class="col-xs-9 half-gutter">
                <h5>ESPINA, DENNIS</h5>
                <h6>Quality Assurance Specialist</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="single-information animate_fade_in" style="opacity: 1; right: 0px">
            <div class="row">
              <div class="col-xs-12">
                <blockquote>
                "As part of the documentation team, I contributed to creating well-organized and detailed research materials to support the capstone project."                </blockquote>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3">
                <img src="images/student3.jpg" alt="student" />
              </div>
              <div class="col-xs-9 half-gutter">
                <h5>GADIA, LOWIE JAY</h5>
                <h6>Documentation Specialist / UI/UX Designer</h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="single-information animate_fade_in" style="opacity: 1; right: 0px">
            <div class="row">
              <div class="col-xs-12">
                <blockquote>
                "As the Documentation Specialist, I ensured the research documentation was comprehensive and accurately represented the capstone project's objectives."
                </blockquote>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3">
                <img src="images/student4.jpg" alt="student" />
              </div>
              <div class="col-xs-9 half-gutter">
                <h5>GEANGA, GEORGE ANTHONY</h5>
                <h6>Documentation Specialist</h6>
              </div>
            </div>
          </div>
        </div>
      </div>

    
      <div class="support-section text-center">
        <h3 class="wow fadeInDown">Support Us</h3>
        <p class="wow fadeInDown">If you appreciate our work and would like to support the development of the Online Library System, please consider donating to help us continue improving and expanding this project.</p>
        <!-- <button type="button" class="btn btn-primary wow fadeInDown" id="openModalBtn" data-toggle="modal" data-target="#exampleModal" data-wow-delay="0.3s" id="openModalBtn">
          Sponsor Our Work
        </button> -->

      </div>
    </div>
  </section>
  
  
  
  
  <style>
    
    /* Basic styles for the page */
/* Style for the button */
.open-modal-btn {
  padding: 15px 25px;
  font-size: 16px;
  background-color: #ffcc00; /* Yellow for button */
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

.open-modal-btn:hover {
  background-color: #ffb900; /* Darker yellow on hover */
}

/* Modal background */
.modal {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6); /* Darker background for contrast */
  justify-content: center;
  align-items: center;
}

/* Modal content */
.modal-content {
  background-color: white; /* White background for content */
  padding: 20px;

  border-radius: 8px;
  width: 800px;
  max-width: 90%;
  text-align: center;
  max-height: 90%;
  overflow-y: auto;
  border: 3px solid #ffcc00; /* Yellow border around modal */
}

/* Heading styles */
.modal-content h5 {
  font-size: 20px;
  color: #ff6f61; /* Red color for heading text */
}

.donation-options {
  text-align: center;
  margin-top: 20px;
}

.donation-options h3 {
  color: #ffcc00; /* Yellow for section titles */
}

.qr-options button {
  margin: 5px;
  padding: 10px;
  font-size: 14px;
  background-color: #960306; /* Red for donation buttons */
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.qr-options button:hover {
  background-color: #e0554e; /* Darker red on hover */
}

.qr-code-display {
  margin-top: 15px;
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
}

.qr-code {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 10px;
}

.qr-no-display {
  font-size: 20px;
  margin-top: 20px;
}

.close-btn {
  background-color: #ff6f61; /* Red color for close button */
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 20px;
}

.close-btn:hover {
  background-color: #e0554e; /* Darker red for hover */
}

/* Media queries for responsiveness */
@media (max-width: 768px) {
  .modal-content {
    width: 90%; /* Make modal content take up more width on smaller screens */
    padding: 15px; /* Reduce padding for smaller screens */
  }

  .modal-content h5 {
    font-size: 18px; /* Slightly smaller font size */
  }

  .donation-options h3 {
    font-size: 16px; /* Adjust font size for better readability */
  }

  .qr-options button {
    padding: 8px 12px; /* Slightly smaller buttons */
    font-size: 12px; /* Adjust button font size */
  }

  .qr-code-display {
    flex-direction: column; /* Stack QR codes vertically on smaller screens */
    align-items: center;
  }

  .qr-code {
    max-width: 80%; /* Ensure QR codes are responsive */
    margin-bottom: 10px;
  }
}

@media (max-width: 480px) {
  .modal-content {
    width: 95%; /* Even narrower on very small screens */
    padding: 10px; /* Even smaller padding */
  }

  .modal-content h5 {
    font-size: 16px; /* Smaller font size for very small screens */
  }

  .donation-options h3 {
    font-size: 14px; /* Smaller font size for section titles */
  }

  .qr-options button {
    padding: 6px 10px; /* Smaller button sizes */
    font-size: 12px; /* Consistent font size */
  }

  .qr-code {
    max-width: 90%; /* Adjust QR code size for very small screens */
  }
}

  </style>



  <div class="modal" id="myModal">
    <div class="modal-content">
      <!-- <h5>Support Our Library Development Project</h5> -->
      
      <div class="custom-modal-body">
        <h2>Support Our Library Development Project</h2>
        <br>
        <p>We are fourth-year students at Gensantos Foundation College, Inc., dedicated to creating a powerful and user-friendly Library Management System to benefit all students. Your support means a lot to us, as it helps us maintain, improve, and expand this project.</p>
        <p>If you'd like to support us, here are several ways to make a contribution:</p>

        <div class="donation-options">
          <h3>E-Wallet Options:</h3>
          <p>Click on the buttons below to view the QR code for each e-wallet service:</p>
          <div class="qr-options">
            <button onclick="showQRCode('gcash')">Gcash</button>
            <button onclick="showQRCode('paymaya')">Paymaya</button>
            <button onclick="showQRCode('paypal')">PayPal</button>
          </div>
          <div class="qr-code-display">
            <img id="gcashQRCode" src="src/assets/images/GCash-MyQR-13122024090615.jpg" alt="Gcash QR Code" class="qr-code">
            <img id="paymayaQRCode" src="./src/assets/images/PAYMAYA-myqr_17340531489.jpg" alt="Paymaya QR Code" class="qr-code" style="display: none;">
            <img id="paypalQRCode" src="./src/images/paypal.png" alt="PayPal QR Code" class="qr-code" style="display: none;">
          </div>
          <div class="qr-no-display">

          <p><strong>Gcash No.:</strong> 09166298647</p>
          <p><strong>Paymaya ID:</strong> None</p>
          <p><strong>PayPal ID:</strong> None</p>
          </div>

        </div>

        <div class="donation-options">
          <h3>Blockchain Donation Options:</h3>
          <p>You can also support us via blockchain! Here are our addresses for different chains:</p>
          <p><strong>BNB Chain: (USDT)</strong> 0xdC92B55ca1cF7C0281D8F4012e31348E6bc92C74</p>
          <p><strong>Ethereum Chain: (USDT)</strong> 0xdC92B55ca1cF7C0281D8F4012e31348E6bc92C74</p>
          <p><strong>Polygon Chain: (USDT)</strong> 0xdC92B55ca1cF7C0281D8F4012e31348E6bc92C74</p>
        </div>

        <p>Any amount you choose to give is greatly appreciated and will go directly towards making this system the best resource it can be for students like you. Thank you for considering a donation!</p>

        <button hidden class="close-btn" id="closeModalBtn">Close</button>
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








  
  <!--/#about-->
  <section id="contact-us">
    <div class="container">
      <div class="section-header text-center">
        <h2 class="section-title wow fadeInDown">Contact Us</h2>
        <p class="wow fadeInDown">
          Reach out to Gensantos Foundation College, Inc. for any assistance or inquiries.
        </p>
      </div>
    </div>
  </section>



  <section id="contact">
    <div class="container">
      <div class="contact-info">
        <div class="row">
          <div class="col-sm-6">
            <h3>Contact Information</h3>
            <address>
              <p><strong><i class="fa fa-university"></i> GENSANTOS FOUNDATION COLLEGE, INC.</strong></p>
              <p><i class="fa fa-map-marker"></i> Bulaong Extension, General Santos City, South Cotabato, Philippines, 9500</p>
              <p><i class="fa fa-envelope"></i> Email: <a href="mailto:gfilibrary2020@gmail.com">gfilibrary2020@gmail.com</a></p>
              <p><i class="fa fa-phone"></i> Phone: 0931 790 6786</p>
              <p><i class="fa fa-facebook"></i> Facebook: <a href="https://www.facebook.com/gfiLibrary" target="_blank">/GFILibrary</a></p>
            </address>
          </div>

          <div class="col-sm-6">
            <h3>Quick Links</h3>
            <ul class="quick-links">
              <li><i class="fa fa-info-circle"></i> <a href="#about">About</a></li>
              <li><i class="fa fa-phone"></i> <a href="#contact">Contact</a></li>
              

              <li><i class="fa fa-gavel"></i> <a href="terms_condition.php">Terms of Service</a></li>

            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--/#bottom-->
  <footer id="footer">
    <div class="container">
      <div class="row">
        <div class="col-sm-6">
          &copy; Copyright © 2024 GFI FOUNDATION COLLEGE, INC. All Rights Reserved.

        </div>
        <div class="col-sm-6">
          <ul class="social-icons">
            <li>
              <a href="https://facebook.com/GFIOfficeOfTheStudentaffairs" target="_blank">
                <i class="fa fa-facebook"></i>
              </a>
            </li>
            <li>
              <a href="https://www.youtube.com/@gensantosfoundationcollege2579" target="_blank">
                <i class="fa fa-youtube"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
  <!--/#footer-->
  <script src="js/jquery.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/mousescroll.js"></script>
  <script src="js/smoothscroll.js"></script>
  <script src="js/jquery.prettyPhoto.js"></script>
  <script src="js/jquery.isotope.min.js"></script>
  <script src="js/jquery.inview.min.js"></script>
  <script src="js/wow.min.js"></script>
  <script src="js/custom-scripts.js"></script>
</body>

</html>
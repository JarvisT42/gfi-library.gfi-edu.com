<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="" />
  <meta name="author" content="webthemez" />
  <title>GFI-Library</title>
  <!-- core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="css/animate.min.css" rel="stylesheet" />
  <link href="css/prettyPhoto.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
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
            class="navbar-toggle collapsed"
            data-toggle="collapse"
            data-target="#navbar-content"
            aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <!-- Logo and Title -->
          <a class="navbar-brand" href="index.html" style="display: flex; align-items: center">
            <img src="./src/assets/images/library.png" alt="logo" />
            <span class="navbar-title">Gensantos Foundation College, Inc. Library</span>
          </a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-content">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php">Home</a></li>


            <!-- Centered Login Button -->
            <li class="scroll center-login">
              <a href="login.php" class="btn btn-login">Login</a>
            </li>
          </ul>

        </div>
      </div>
    </nav>
  </header>






  <section id="result" class="py-5 mt-5">
    <div class="container">
      <div class="section-header text-center mb-5">
        <h2 class="fw-bold">Library Features</h2>
      </div>

      <!-- Filters and Search -->
      <!-- Filters and Search -->
      <div class="row align-items-center ">
        <div class="col-md-4 ">
          <!-- Dropdown Button -->
          <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              <span id="selectedField" class="me-2">All fields</span>
              <!-- Downward Arrow Icon -->
              <i class="bi bi-caret-down-fill"></i> <!-- Bootstrap icon -->
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <li><a class="dropdown-item" href="#" data-table="All fields">All fields</a></li>
              <?php
              // Populate table names dynamically
              require 'connection2.php';
              if ($conn2->connect_error) {
                die("Connection failed: " . $conn2->connect_error);
              }
              $sql = "SHOW TABLES FROM dnllaaww_gfi_library_books_inventory";
              $result = $conn2->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
                  $tableName = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
                  if ($tableName !== 'e-books') {
                    echo '<li><a class="dropdown-item" href="#" data-table="' . $tableName . '">' . $tableName . '</a></li>';
                  }
                }
              } else {
                echo '<li><span class="dropdown-item">No tables found</span></li>';
              }
              ?>
            </ul>
          </div>
        </div>

        <div class="col-md-4">
          <!-- Checkbox -->
          <!-- <div class="form-check">
            <input type="checkbox" class="form-check-input" id="availableCheckbox">
            <label class="form-check-label" for="availableCheckbox">Available Only</label>
          </div> -->
        </div>

        <div class="col-md-4">
          <!-- Search Input and Button -->
          <div class="d-flex search-form">
            <input type="text" id="tableSearchInput" class="form-control1 me-2" placeholder="Search by Title or Author">
            <button class="btn btn-primary" id="searchButton">Search</button>
          </div>
        </div>
      </div>

      <!-- Updated CSS -->
      <style>
        .search-form .form-control1 {
          flex: 1;
          /* Ensures the input takes up available space */
          border: none;
          padding: 2px;
          font-size: 14px;
          color: #555;
        }

        .search-form .btn {
          padding: 5px 5px;
          font-size: 16px;
          font-weight: bold;
        }
      </style>




          <!-- Include Bootstrap CSS and JS -->
          <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

          <!-- Optional: Your own JS -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const dropdownItems = document.querySelectorAll('.dropdown-item');
              const selectedField = document.getElementById('selectedField');

              dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                  e.preventDefault();
                  const tableName = this.getAttribute('data-table');
                  selectedField.textContent = tableName;
                  console.log('Selected table:', tableName);
                });
              });
            });
          </script>





        

      <!-- Display Table Data -->
      <div class="table-responsive">
      <table class="table table-bordered table-hover" style="height: 300px;">
      <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Author</th>
              <th>Published</th>
              <th>Copies</th>
              <th>Status</th>
              <th>Cover</th>
            </tr>
          </thead>
          <tbody id="tableData">
            <!-- Table rows will be dynamically populated here -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-center" id="pagination">
          <!-- Pagination links will be dynamically generated -->
        </ul>
      </nav>
    </div>
  </section>

  <!-- JavaScript Logic -->

  <script>
  $(document).ready(function () {
    let allRecords = [];
    let filteredRecords = [];
    let currentPage = 1;
    const recordsPerPage = 5;

    // Function to load table data
    function loadTableData(tableName, searchTerm = "") {
      $.getJSON(`fetch_table_data.php?table=${encodeURIComponent(tableName)}`, function (data) {
        allRecords = data.data;
        filteredRecords = searchRecords(allRecords, searchTerm);
        displayRecords(filteredRecords);
        setupPagination(filteredRecords.length);
      });
    }

    // Function to display records
    function displayRecords(records) {
      const startIndex = (currentPage - 1) * recordsPerPage;
      const paginatedRecords = records.slice(startIndex, startIndex + recordsPerPage);

      $("#tableData").html(
        paginatedRecords
          .map(
            (record, index) => `
              <tr>
                <td>${startIndex + index + 1}</td>
                <td>${record.title}</td>
                <td>${record.author}</td>
                <td>${record.publicationDate}</td>
                <td>${record.copies}</td>
                <td>${record.copies <= 1 ? '<span class="text-danger">Not Available</span>' : '<span class="text-success">Available</span>'}</td>
                <td><img src="${record.coverImage}" alt="Cover Image" class="img-thumbnail" style="width: 80px; height: auto;"></td>
              </tr>
            `
          )
          .join("")
      );
    }

    // Function to setup pagination
    function setupPagination(totalRecords) {
      const totalPages = Math.ceil(totalRecords / recordsPerPage);
      const $pagination = $("#pagination");
      $pagination.html("");

      // Previous button
      const prevDisabled = currentPage === 1 ? "disabled" : "";
      $pagination.append(
        `<li class="page-item ${prevDisabled}"><a class="page-link" href="#" id="prevPage">Previous</a></li>`
      );

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        const activeClass = i === currentPage ? "active" : "";
        $pagination.append(
          `<li class="page-item ${activeClass}"><a class="page-link page-number" href="#">${i}</a></li>`
        );
      }

      // Next button
      const nextDisabled = currentPage === totalPages ? "disabled" : "";
      $pagination.append(
        `<li class="page-item ${nextDisabled}"><a class="page-link" href="#" id="nextPage">Next</a></li>`
      );
    }

    // Function to search records
    function searchRecords(records, searchTerm) {
      searchTerm = searchTerm.toLowerCase();
      return records.filter(
        (record) =>
          record.title.toLowerCase().includes(searchTerm) ||
          record.author.toLowerCase().includes(searchTerm)
      );
    }

    // Handle URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const initialTable = urlParams.get("table") || "All fields";
    const initialSearch = urlParams.get("search") || "";

    // Set dropdown and input based on URL parameters
    $("#selectedField").text(initialTable);
    $("#tableSearchInput").val(initialSearch);

    // Initial data load with URL parameters
    loadTableData(initialTable, initialSearch);

    // Handle dropdown change
    $(".dropdown-menu a").on("click", function (e) {
      e.preventDefault();
      const tableName = $(this).data("table");
      $("#selectedField").text(tableName);
      loadTableData(tableName);
    });

    // Handle search button click
    $("#searchButton").on("click", function () {
      const searchTerm = $("#tableSearchInput").val();
      filteredRecords = searchRecords(allRecords, searchTerm);
      currentPage = 1; // Reset to first page
      displayRecords(filteredRecords);
      setupPagination(filteredRecords.length);
    });

    // Pagination event handlers
    $(document).on("click", "#prevPage", function (e) {
      e.preventDefault();
      if (currentPage > 1) {
        currentPage--;
        displayRecords(filteredRecords);
        setupPagination(filteredRecords.length);
      }
    });

    $(document).on("click", "#nextPage", function (e) {
      e.preventDefault();
      if (currentPage < Math.ceil(filteredRecords.length / recordsPerPage)) {
        currentPage++;
        displayRecords(filteredRecords);
        setupPagination(filteredRecords.length);
      }
    });

    $(document).on("click", ".page-number", function (e) {
      e.preventDefault();
      currentPage = parseInt($(this).text());
      displayRecords(filteredRecords);
      setupPagination(filteredRecords.length);
    });
  });
</script>










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
              <a href="#"><i class="fa fa-facebook"></i></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-twitter"></i></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-google-plus"></i></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-linkedin"></i></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-youtube"></i></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-github"></i></a>
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
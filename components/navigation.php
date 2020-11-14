</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm position-sticky">
    <div class="container">
      <a class="navbar-brand" href="index.php">Jadon</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item px-2 <?php echo $currentPage === 'index.php' ? 'active' : '' ?>">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item px-2 dropdown <?php echo $currentPage === 'services.php' ? 'active' : '' ?>">
            <a class="nav-link dropdown-toggle" href="services.php">
              Services
            </a>
            <div class="dropdown-menu">
              <?php
              $rows = $db->getData("SELECT * FROM service_categories;");
              if ($rows !== 0) {
                foreach ($rows as $row) {
                  echo "
                    <a class='dropdown-item text-capitalize' href='services.php?category_id=$row[id]'>$row[name]</a>
                  ";
                }
              }
              ?>
            </div>
          </li>
          <li class="nav-item px-2 dropdown <?php echo $currentPage === 'our-work.php' ? 'active' : '' ?>">
            <a class="nav-link dropdown-toggle" href="our-work.php">Our work</a>
            <div class="dropdown-menu">
              <?php
              $rows = $db->getData("SELECT * FROM service_categories;");
              if ($rows !== 0) {
                foreach ($rows as $row) {
                  echo "
                    <a class='dropdown-item text-capitalize' href='our-work.php?category_id=$row[id]'>$row[name]</a>
                  ";
                }
              }
              ?>
            </div>
          </li>
          <li class="nav-item px-2 <?php echo $currentPage === 'our-team.php' ? 'active' : '' ?>">
            <a class="nav-link" href="our-team.php">Our team</a>
          </li>
          <li class="nav-item px-2 <?php echo $currentPage === 'email-us.php' ? 'active' : '' ?>">
            <a class="nav-link" href="email-us.php">Email us</a>
          </li>
          
          <?php if (isset($_SESSION['user_loggedIn'])) { ?>
          <li class="nav-item dropdown" id="user-dropdown">
            <a class="nav-link dropdown-toggle" href="account.php?id=<?php echo $_SESSION['user_loggedIn']['id'] ?>">
              <img src="<?php echo $_SESSION['user_loggedIn']['img_url'] ? $_SESSION['user_loggedIn']['img_url'] : 'img/svg/default-user.svg' ?>" alt="profile picture">
              <?php echo $_SESSION['user_loggedIn']['username'] ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item text-secondary" href="logout.php?prev=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">
                <i class="fas fa-sign-out-alt px-2"></i>
                Logout
              </a>
              <a class="dropdown-item text-secondary" href="account.php?id=<?php echo $_SESSION['user_loggedIn']['id'] ?>">
                <i class="fas fa-user-cog pl-2 pr-1"></i>
                Account
              </a>
            </div>
          </li>
          <?php } else { ?>
          <li class="nav-item px-2 dropdown <?php echo $currentPage === 'login.php' ? 'active' : '' ?>">
            <a class="nav-link dropdown-toggle" href="login.php?prev=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>">Login</a>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item text-secondary" href="register.php">
                <i class="fas fa-sign-out-alt px-2"></i>
                Register
              </a>
            </div>
          </li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </nav>
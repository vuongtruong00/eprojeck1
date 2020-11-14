</head>

<body>
  <div class="d-flex page-wrapper">
    <div class="side-bar">
      <div class="container">
        <div class="row">
          <div class="col-sm-12" id="side-bar-col">
            <a href="index.php" class="brand">Jadon</a>

            <ul class="list-group" id="accordionExample">
              <ul class="list-group-item list-group-item_home <?php echo $currentPage === 'index.php' || $currentPage === 'home.introduction.php' ? 'active' : '' ?>"><a class="homeBtn"><i class="fas fa-home"></i><span>Home</span><i class="fas fa-chevron-right"></i></a>
                <li class="list-group-sub-item <?php echo $currentPage === 'index.php' ? 'active' : '' ?>"><a href="index.php">Slideshow</a></li>
                <li class="list-group-sub-item <?php echo $currentPage === 'home.introduction.php' ? 'active' : '' ?>"><a href="home.introduction.php">Introduction</a></li>
              </ul>
              <li class="list-group-item <?php echo $currentPage === 'services.php' ? 'active' : '' ?>"><a href="services.php"><i class="fas fa-cogs"></i>Services</a></li>
              <li class="list-group-item <?php echo $currentPage === 'our-work.php' ? 'active' : '' ?>"><a href="our-work.php"><i class="fas fa-images"></i>Our work</a></li>
              <li class="list-group-item <?php echo $currentPage === 'our-team.php' ? 'active' : '' ?>"><a href="our-team.php"><i class="fas fa-users"></i>Our Team</a></li>
              <li class="list-group-item <?php echo $currentPage === 'users.php' ? 'active' : '' ?>"><a href="users.php"><i class="fas fa-user"></i>Users</a></li>
              <li class="list-group-item <?php echo $currentPage === 'email-us.php' ? 'active' : '' ?>"><a href="email-us.php"><i class="fas fa-envelope-open-text"></i>Email-us</a></li>
              <li id="managersBtn" class="list-group-item <?php echo $currentPage === 'managers.php' ? 'active' : '' ?>"><a href="managers.php"><i class="fas fa-user-tie"></i>Managers</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="main">
      <nav class="navbar navbar-light bg-light shadow-sm">
        <div class="container">
          <i class="fas fa-bars side-bar-toggler"></i>

          <div class="navbar-collapse justify-content-end d-flex">
            <ul class="navbar-nav">
              <li class="nav-item dropdown" id="user-dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="<?php echo $_SESSION['jadon_loggedIn']['img_url'] ? $_SESSION['jadon_loggedIn']['img_url'] : '../img/svg/default-user.svg' ?>" alt="profile picture">
                  <?php echo $_SESSION['jadon_loggedIn']['username'] ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item text-secondary" href="cms.logout.php">
                    <i class="fas fa-sign-out-alt px-2"></i>
                    Logout
                  </a>
                  <a class="dropdown-item text-secondary" href="cms.account.php?id=<?php echo $_SESSION['jadon_loggedIn']['id']?>">
                    <i class="fas fa-user-cog pl-2 pr-1"></i>
                    Account
                  </a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
<?php $username = $_SESSION['username'] ?>
<body>
    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="dashboard.php">
                            <i class="menu-icon fa fa-laptop"></i>Dashboard
                        </a>
                    </li>

                   <?php
                    $isAdmin = isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1;

                    if ($isAdmin):
                    ?>
                        <li>
                            <a href="roles_table.php">
                                <i class="menu-icon fa fa-users"></i>Roles
                            </a>
                        </li>
                        <li>
                            <a href="role_form.php">
                                <i class="menu-icon fa fa-users"></i>Add Role
                            </a>
                        </li>
                        <li>
                            <a href="user_list.php">
                                <i class="menu-icon fa fa-users"></i>Users
                            </a>
                        </li>
                        <li>
                            <a href="user_form.php">
                                <i class="menu-icon fa fa-user-plus"></i>Add User
                            </a>
                        </li>
                        <li>
                            <a href="task_form.php">
                                <i class="menu-icon fa fa-tasks"></i>Add Tasks
                            </a>
                        </li>
                        <li>
                            <a href="assign_task.php">
                                <i class="menu-icon fa fa-plus-circle"></i>Assign Tasks
                            </a>
                        </li>
                    <?php
                    endif;
                    ?>

                    <li>
                        <a href="all_tasks.php">
                            <i class="menu-icon fa fa-tasks"></i>All Tasks
                        </a>
                    </li>

                    <li>
                        <a href="submit_task.php">
                            <i class="menu-icon fa fa-plus-circle"></i>Submit Task
                        </a>
                    </li>

                    <li>
                        <a href="task_submissions.php">
                            <i class="menu-icon fa fa-plus-circle"></i>Submittes Tasks
                        </a>
                    </li>

                </ul>
            </div>

        </nav>
    </aside>
    <!-- /#left-panel -->

    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <header id="header" class="header">
            <div class="top-left">
                <div class="navbar-header">
                    <a class="navbar-brand" href="./"><img src="images/dlogo.png" alt="Logo"></a>
                    <a class="navbar-brand hidden" href="./"><img src="images/dlogo2.png" alt="Logo"></a>
                    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
                </div>
            </div>

            <div class="top-right">
                <div class="header-menu">
                    <div class="header-left">
                        <!-- <div class="dropdown for-message">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-envelope"></i>
                                <span class="count bg-primary">4</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="message">
                                <p class="red">You have 4 Mails</p>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/1.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Jonathan Smith</span>
                                        <span class="time float-right">Just now</span>
                                        <p>Hello, this is an example msg</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/2.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Jack Sanders</span>
                                        <span class="time float-right">5 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/3.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Cheryl Wheeler</span>
                                        <span class="time float-right">10 minutes ago</span>
                                        <p>Hello, this is an example msg</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/4.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Rachel Santos</span>
                                        <span class="time float-right">15 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                    </div>
                                </a>
                            </div>
                        </div> -->
                    </div>

                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="images/addmin.jpg" alt="User Avatar">
                        </a>
                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="profile.php?username=<?php echo $username; ?>"><i class="fa fa-user"></i>My Profile</a>
                            <a class="nav-link" href="logout.php"><i class="fa fa-power-off"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- /#header -->

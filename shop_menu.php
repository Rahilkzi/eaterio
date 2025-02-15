<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    session_start();
    include("conn_db.php");
    include('head.php');
    if (!isset($_GET["s_id"])) {
        header("location: restricted.php");
        exit(1);
    }
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
    <title>Shop Menu | EATERIO</title>
</head>

<body class="d-flex flex-column h-100">
    <?php include('nav_header.php') ?>

    <?php
    $s_id = $_GET["s_id"];
    $query = "SELECT s_name,s_location,s_openhour,s_closehour,s_status,s_preorderstatus,s_phoneno,s_pic
        FROM shop WHERE s_id = {$s_id} LIMIT 0,1";
    $result = $mysqli->query($query);
    $shop_row = $result->fetch_array();
    ?>

    <div class="container px-5 py-4" id="shop-body">
        <a class="nav nav-item text-decoration-none text-muted mb-3" href="#" onclick="history.back();">
            <i class="bi bi-arrow-left-square me-2"></i>Go back
        </a>

        <?php
        if (isset($_GET["atc"])) {
            if ($_GET["atc"] == 1) {
                ?>
                <!-- START SUCCESSFULLY ADD TO CART -->
                <div class="row row-cols-1 notibar pb-3">
                    <div class="col mt-2 ms-2 p-2 bg-success text-white rounded text-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-check-circle ms-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                        </svg>
                        <span class="ms-2 mt-2">Add item to your cart successfully!</span>
                        <span class="me-2 float-end"><a class="text-decoration-none link-light"
                                href="shop_menu.php?s_id=<?php echo $s_id; ?>">X</a></span>
                    </div>
                </div>
                <!-- END SUCCESSFULLY ADD TO CART -->
            <?php } else { ?>
                <!-- START FAILED ADD TO CART -->
                <div class="row row-cols-1 notibar">
                    <div class="col mt-2 ms-2 p-2 bg-danger text-white rounded text-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-x-circle ms-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg><span class="ms-2 mt-2">Failed to add item to your cart.</span>
                        <span class="me-2 float-end"><a class="text-decoration-none link-light"
                                href="shop_menu.php?s_id=<?php echo $s_id; ?>">X</a></span>
                    </div>
                </div>
                <!-- END FAILED ADD TO CART -->
            <?php }
        } ?>
        <div class="mb-3 text-wrap" id="shop-header">
            <div class="rounded-25 mb-4" id="shop-img" style="
                    background: url(
                        <?php
                        if (is_null($shop_row["s_pic"])) {
                            echo "'img/default.png'";
                        } else {
                            echo "'img/{$shop_row['s_pic']}'";
                        }
                        ?> 
                    ) center; height: 200px;
                    background-size: cover; background-repeat: no-repeat;
                    background-position: center;">
            </div>
            <h1 class="display-5 strong"><?php echo $shop_row["s_name"]; ?></h1>
            <ul class="list-unstyled">
                <li class="my-2">
                    <?php
                    $now = date('H:i:s');
                    if ((($now < $shop_row["s_openhour"]) || ($now > $shop_row["s_closehour"])) || ($shop_row["s_status"] == 0)) {
                        ?>
                        <span class="badge rounded-pill bg-danger">Closed</span>
                    <?php } else { ?>
                        <span class="badge rounded-pill bg-success">Open</span>
                    <?php }
                    if ($shop_row["s_preorderstatus"] == 1) {
                        ?>
                        <span class="badge rounded-pill bg-success">Pre-order avaliable</span>
                    <?php } else { ?>
                        <span class="badge rounded-pill bg-danger">Pre-order Unavaliable</span>
                    <?php } ?>
                </li>
                <li class=""><?php echo $shop_row["s_location"]; ?></li>
                <li class="">Open hours:
                    <?php
                    $open = explode(":", $shop_row["s_openhour"]);
                    $close = explode(":", $shop_row["s_closehour"]);
                    echo $open[0] . ":" . $open[1] . " - " . $close[0] . ":" . $close[1];
                    ?>
                </li>
                <li class="">Telephone number: <?php echo "(+66) " . $shop_row["s_phoneno"]; ?></li>
            </ul>
        </div>

        <!-- GRID MENU SELECTION -->
        <h3 class="border-top py-3 mt-2">Categories</h3>

        <?php
        // Get unique food types for this shop
        $type_query = "SELECT DISTINCT f_type, 
                          (SELECT f_pic FROM food WHERE f_type = f.f_type AND s_id = {$s_id} LIMIT 1) as category_pic,
                          COUNT(*) as item_count 
                          FROM food f 
                          WHERE s_id = {$s_id} AND NOT(f_todayavail = 0 AND f_preorderavail = 0) 
                          GROUP BY f_type 
                          ORDER BY f_type";
        $type_result = $mysqli->query($type_query);

        if ($type_result->num_rows > 0) {
            ?>
            <!-- Category Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 align-items-stretch mb-4">
                <?php
                while ($type_row = $type_result->fetch_array()) {
                    $type_id = "type_" . str_replace(' ', '_', strtolower($type_row["f_type"]));
                    ?>
                    <div class="col">
                        <div class="card rounded-25 mb-4 category-card" data-category="<?php echo $type_id; ?>"
                            style="cursor: pointer;">
                            <div class="card-img-top">
                                <img <?php
                                if (is_null($type_row["category_pic"])) {
                                    echo "src='img/default.png'";
                                } else {
                                    echo "src=\"img/{$type_row['category_pic']}\"";
                                }
                                ?>
                                    style="width:100%; height:150px; object-fit:cover;" class="img-fluid"
                                    alt="<?php echo ($type_row["f_type"] == "null") ? "default" : $type_row["f_type"]; ?>">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title fs-5">
                                    <?php echo ($type_row["f_type"] == "null") ? "default" : $type_row["f_type"]; ?></h5>
                                <p class="card-text"><?php echo $type_row["item_count"] ?> items</p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Menu Items Container (Initially Hidden) -->
            <div id="menuItemsContainer" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0"><span id="selectedCategory"></span></h3>
                    <button class="btn btn-outline-secondary btn-sm" id="backToCategories">
                        <i class="bi bi-arrow-left"></i> Back to Categories
                    </button>
                </div>

                <?php
                // Reset the type result to iterate again
                $type_result->data_seek(0);
                while ($type_row = $type_result->fetch_array()) {
                    $type_id = "type_" . str_replace(' ', '_', strtolower($type_row["f_type"]));
                    ?>
                    <div class="menu-items" id="<?php echo $type_id; ?>_items" style="display: none;">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 align-items-stretch">
                            <?php
                            $food_query = "SELECT * FROM food WHERE s_id = {$s_id} AND f_type = '{$type_row["f_type"]}' 
                                         AND NOT(f_todayavail = 0 AND f_preorderavail = 0)";
                            $food_result = $mysqli->query($food_query);

                            while ($food_row = $food_result->fetch_array()) {
                                ?>
                                <!-- GRID EACH MENU -->
                                <div class="col">
                                    <div class="card rounded-25 mb-4">
                                        <a href="food_item.php?<?php echo "s_id=" . $food_row["s_id"] . "&f_id=" . $food_row["f_id"] ?>"
                                            class="text-decoration-none text-dark">
                                            <div class="card-img-top">
                                                <img <?php
                                                if (is_null($food_row["f_pic"])) {
                                                    echo "src='img/default.png'";
                                                } else {
                                                    echo "src=\"img/{$food_row['f_pic']}\"";
                                                }
                                                ?>
                                                    style="width:100%; height:125px; object-fit:cover;" class="img-fluid"
                                                    alt="<?php echo $food_row["f_name"] ?>">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title fs-5"><?php echo $food_row["f_name"] ?></h5>
                                                <p class="card-text"><?php echo $food_row["f_price"] ?> ₹</p>
                                                <a href="food_item.php?<?php echo "s_id=" . $food_row["s_id"] . "&f_id=" . $food_row["f_id"] ?>"
                                                    class="btn btn-sm mt-3 btn-outline-secondary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                                                        <path
                                                            d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z" />
                                                        <path
                                                            d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                    </svg> Add to cart
                                                </a>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                            $food_result->free_result();
                            ?>
                        </div>
                    </div>
                    <?php
                }
                $type_result->free_result();
        } else {
            ?>
                <div class="row row-cols-1">
                    <div class="col pt-3 px-3 bg-danger text-white rounded text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                            class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                        </svg>
                        <p class="ms-2 mt-2">No menu items available!</p>
                    </div>
                </div>
                <?php
        }
        ?>
        </div>

        <!-- Add this JavaScript before the closing body tag -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const categoryCards = document.querySelectorAll('.category-card');
                const menuItemsContainer = document.getElementById('menuItemsContainer');
                const backButton = document.getElementById('backToCategories');
                const selectedCategoryTitle = document.getElementById('selectedCategory');
                const menuItems = document.querySelectorAll('.menu-items');
                const categoriesGrid = document.querySelector('.row.row-cols-1.row-cols-md-2.row-cols-lg-4');

                categoryCards.forEach(card => {
                    card.addEventListener('click', function () {
                        const categoryId = this.dataset.category;
                        const categoryName = this.querySelector('.card-title').textContent;

                        // Hide all menu items first
                        menuItems.forEach(item => item.style.display = 'none');

                        // Show selected category's menu items
                        document.getElementById(categoryId + '_items').style.display = 'block';

                        // Update category title
                        selectedCategoryTitle.textContent = categoryName;

                        // Hide categories grid and show menu items
                        categoriesGrid.style.display = 'none';
                        menuItemsContainer.style.display = 'block';
                    });
                });

                backButton.addEventListener('click', function () {
                    // Hide menu items and show categories grid
                    menuItemsContainer.style.display = 'none';
                    categoriesGrid.style.display = 'flex';
                });
            });
        </script>
        <!-- END GRID MENU SELECTION -->

    </div>
    <footer
        class="footer d-flex flex-wrap justify-content-between align-items-center px-5 py-3 mt-2 bg-secondary text-light">
        <span class="smaller-font">&copy; 2021 SeriousEater Group<br /><span class="xsmall-font">Paphana Y. Sirada C.
                Thanakit L.</span></span>
        <ul class="nav justify-content-end list-unstyled d-flex">
            <li class="ms-3"><a class="text-light" target="_blank" href="https://github.com/waterthatfrozen/EATERIO"><i
                        class="bi bi-github"></i></a></li>
        </ul>
    </footer>
</body>

</html>
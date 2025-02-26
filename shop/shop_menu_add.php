<!DOCTYPE html>
<html lang="en">

<head>
    <?php 
        session_start(); 
        if($_SESSION["utype"]!="shopowner"){
            header("location: ../restricted.php");
            exit(1);
        }
        include("../conn_db.php"); 
        include('../head.php');
        $s_id = $_SESSION["sid"];
        if(isset($_POST["add_confirm"])){
            if(isset($_POST["f_todayavail"])){$f_todayavail = 1;}else{$f_todayavail = 0;}
            if(isset($_POST["f_preorderavail"])){$f_preorderavail = 1;}else{$f_preorderavail = 0;}
            $f_name = $_POST["f_name"];
            $f_price = $_POST["f_price"];
            $f_type = $mysqli->real_escape_string($_POST["f_type"]);
            $insert_query = "INSERT INTO food (f_name,f_price,s_id,f_todayavail,f_preorderavail,f_type) 
            VALUES ('{$f_name}',{$f_price},{$s_id},{$f_todayavail},{$f_preorderavail},'{$f_type}');";
            $insert_result = $mysqli -> query($insert_query);
            if(!empty($_FILES["f_pic"]["name"]) && $insert_result){
                //Image upload
                $f_id = $mysqli -> insert_id;
                $target_dir = '/img/';
                $temp = explode(".",$_FILES["f_pic"]["name"]);
                $target_newfilename = $f_id."_".$s_id.".".strtolower(end($temp));
                $target_file = $target_dir.$target_newfilename;
                if(move_uploaded_file($_FILES["f_pic"]["tmp_name"],SITE_ROOT.$target_file)){
                    $insert_query = "UPDATE food SET f_pic = '{$target_newfilename}' WHERE f_id = {$f_id};";
                    $insert_result = $mysqli -> query($insert_query);
                }else{
                    $insert_result = false;
                }
            }
            if($insert_result){header("location: shop_menu_list.php?add_fdt=1");}
            else{header("location: shop_menu_list.php?add_fdt=0");}
            exit(1);
        }
    ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/main.css" rel="stylesheet">
    <link href="../css/login.css" rel="stylesheet">
    <title>Shop Add Menu | EATERIO</title>
</head>


<body class="d-flex flex-column h-100">
    <?php include('nav_header_shop.php')?>

    <div class="container form-signin mt-auto w-50">
        <a class="nav nav-item text-decoration-none text-muted" href="#" onclick="history.back();">
            <i class="bi bi-arrow-left-square me-2"></i>Go back
        </a>
        <form method="POST" action="shop_menu_add.php" class="form-floating" enctype="multipart/form-data">
            <h2 class="mt-4 mb-3 fw-normal text-bold"><i class="bi bi-pencil-square me-2"></i>Add New Menu</h2>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="f_todayavail" name="f_todayavail" checked>
                <label class="form-check-label" for="f_todayavail">Menu avaliable for today</label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="f_preorderavail" name="f_preorderavail" checked>
                <label class="form-check-label" for="f_preorderavail">Accepting Pre-order for this menu</label>
            </div>
            <div class="form-floating mb-2">
                <input type="text" class="form-control" id="f_name" placeholder="f_name" name="f_name" required>
                <label for="f_name">Menu Name</label>
            </div>
            <div class="form-floating mb-2">
                <input type="number" step=".25" min="0.00" max="999.75" class="form-control" id="f_price" placeholder="Price (₹)" name="f_price" required>
                <label for="f_price">Price (₹)</label>
            </div>
            <div class="form-floating mb-2">
                <select class="form-select" id="f_type" name="f_type">
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="South Indian">South Indian</option>
                    <option value="Gujarati">Gujarati</option>
                    <option value="Chaats">Chaats</option>
                    <option value="Rolls">Rolls</option>
                    <option value="Desserts">Desserts</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Other">Other</option>    
                </select>
                <label for="f_type">Food Type</label>
            </div>
            <div class="mb-2">
                <label for="formFile" class="form-label">Upload food image</label>
                <input class="form-control" type="file" id="f_pic" name="f_pic" accept="image/*">
            </div>
            <button class="w-100 btn btn-success mb-3" name="add_confirm" type="submit">Add New Menu</button>
        </form>
    </div>

    <footer
        class="footer d-flex flex-wrap justify-content-between align-items-center px-5 py-3 mt-auto bg-secondary text-light">
        <span class="smaller-font">&copy; 2021 SeriousEater Group<br /><span class="xsmall-font">Paphana Y. Sirada C.
                Thanakit L.</span></span>
        <ul class="nav justify-content-end list-unstyled d-flex">
            <li class="ms-3"><a class="text-light" target="_blank" href="https://github.com/waterthatfrozen/EATERIO"><i
                        class="bi bi-github"></i></a></li>
        </ul>
    </footer>
</body>

</html>
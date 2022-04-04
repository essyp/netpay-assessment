<?php
    include './scripts/functions.php';


    $data = mysqli_real_escape_string($dbcon, isset($_GET["search"])?$_GET["search"]:'');
    $search = new search();
    $result = isset($_GET["search"])?$search->query($data):[];

    // create table
    $path = "./file.txt";
    $search->save($path);
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="author" content="Francis Mogbana">
<link rel="stylesheet" href="./assets/css/style.css">
</head>
    <body>
        <main>
            <div class="container">
                <h1>Recursive Search Implementation</h1>
                <h2>Try below!</h2>
                <div class="search-box">
                    <div class="search-icon"><i class="fa fa-search search-icon"></i></div>
                    <form method="get" action="<?=$_SERVER['PHP_SELF'];?>" class="search-form">
                        <input type="text" placeholder="Search" name="search" id="search" autocomplete="off" required>
                    </form>
                    <svg class="search-border" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/" x="0px" y="0px" viewBox="0 0 671 111" style="enable-background:new 0 0 671 111;" xml:space="preserve">
                        <path class="border" d="M335.5,108.5h-280c-29.3,0-53-23.7-53-53v0c0-29.3,23.7-53,53-53h280"/>
                        <path class="border" d="M335.5,108.5h280c29.3,0,53-23.7,53-53v0c0-29.3-23.7-53-53-53h-280"/>
                    </svg>
                    <div class="go-icon"><i class="fa fa-arrow-right"></i></div><br>

                    <div class="suggestion-wrap">
                        <?php foreach($result as $data){ ?>
                        <span><?php echo $data; ?></span><br>
                        <?php } ?>
                    </div>
                </div>

                

            </div>
        </main>

        <script src="./assets/js/main.js"></script>
    </body>
</html>
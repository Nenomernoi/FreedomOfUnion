
<html>
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="ic_launcher.ico"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">

        <style type="text/css">
            td.style{
                size: 12px;
            }
        </style>
        <title>IPTV Admin</title>
        <style type="text/css">
            body{
            }
            div.container{
                width: 400px;
                margin: 0 auto;
                position: relative;
            }
            legend{
                font-size: 30px;
                color: #555;
            }
            .btn_send{
                background: #00bcd4;
            }
            label{
                margin:10px 0px !important;
            }
            textarea{
                resize: none !important;
            }
            .fl_window{
                width: 400px;
                position: absolute;
                left: 500px;
                top:20px;
            }
            pre, code {
                padding:10px 0px;
                box-sizing:border-box;
                -moz-box-sizing:border-box;
                webkit-box-sizing:border-box;
                display:block; 
                white-space: pre-wrap;  
                white-space: -moz-pre-wrap; 
                white-space: -pre-wrap; 
                white-space: -o-pre-wrap; 
                word-wrap: break-word; 
                width:100%; overflow-x:auto;
            }

        </style>

    </head>
    <body>

        <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        require('api/tables/html_table.class.php');


        $username;
        $password;
        $host;
        $db_name;
        $url;
        $port;

        $connection_url = getenv("MONGOLAB_URI");

        if ($_SERVER['SERVER_NAME'] == "freedoom-or-union-9804.herokuapp.com") {
            $url = parse_url($connection_url);
            $host = $url["host"];
            $username = $url["user"];
            $password = $url["pass"];
            $db_name = preg_replace('/\/(.*)/', '$1', $url['path']);
        } else {
            $host = 'ds049288.mongolab.com';
            $db_name = 'heroku_4fxvwmm2';
            $username = 'heroku_4fxvwmm2';
            $password = 'rc59onuvmpm6toh7ti2ht3rfhb';
            $port = '49288';
            $connection_url = "mongodb://heroku_4fxvwmm2:rc59onuvmpm6toh7ti2ht3rfhb@ds049288.mongolab.com:49288/heroku_4fxvwmm2";
        }

        $m = new MongoClient($connection_url);
        $link = $m->selectDB($db_name);

        $langData = array("battles/", "officers/", "othercard/", "polk/");

        if (isset($_POST['button'])) {
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus'])) {

                $date = strtotime($_POST['date']);

                $query = array(
                    "image" => $langData[$_POST['parent']] . $_POST['image'] . ".png",
                    "name_rus" => $_POST['name_rus'] . ".",
                    "name_en" => $_POST['name_en'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus1'])) {

                $date = strtotime($_POST['date1']);

                $query = array(
                    "image" => $langData[$_POST['parent1']] . $_POST['image1'] . ".png",
                    "name_rus" => $_POST['name_rus1'] . ".",
                    "name_en" => $_POST['name_en1'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus2'])) {

                $date = strtotime($_POST['date2']);

                $query = array(
                    "image" => $langData[$_POST['parent2']] . $_POST['image2'] . ".png",
                    "name_rus" => $_POST['name_rus2'] . ".",
                    "name_en" => $_POST['name_en2'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }

            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus3'])) {


                $date = strtotime($_POST['date3']);

                $query = array(
                    "image" => $langData[$_POST['parent3']] . $_POST['image3'] . ".png",
                    "name_rus" => $_POST['name_rus3'] . ".",
                    "name_en" => $_POST['name_en3'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus4'])) {

                $date = strtotime($_POST['date4']);

                $query = array(
                    "image" => $langData[$_POST['parent4']] . $_POST['image4'] . ".png",
                    "name_rus" => $_POST['name_rus4'] . ".",
                    "name_en" => $_POST['name_en4'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }

            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus5'])) {

                $date = strtotime($_POST['date5']);

                $query = array(
                    "image" => $langData[$_POST['parent5']] . $_POST['image5'] . ".png",
                    "name_rus" => $_POST['name_rus5'] . ".",
                    "name_en" => $_POST['name_en5'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus6'])) {

                $date = strtotime($_POST['date6']);

                $query = array(
                    "image" => $langData[$_POST['parent6']] . $_POST['image6'] . ".png",
                    "name_rus" => $_POST['name_rus6'] . ".",
                    "name_en" => $_POST['name_en6'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus7'])) {

                $date = strtotime($_POST['date7']);

                $query = array(
                    "image" => $langData[$_POST['parent7']] . $_POST['image7'] . ".png",
                    "name_rus" => $_POST['name_rus7'] . ".",
                    "name_en" => $_POST['name_en7'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus8'])) {

                $date = strtotime($_POST['date8']);

                $query = array(
                    "image" => $langData[$_POST['parent8']] . $_POST['image8'] . ".png",
                    "name_rus" => $_POST['name_rus8'] . ".",
                    "name_en" => $_POST['name_en8'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus9'])) {

                $date = strtotime($_POST['date9']);

                $query = array(
                    "image" => $langData[$_POST['parent9']] . $_POST['image9'] . ".png",
                    "name_rus" => $_POST['name_rus9'] . ".",
                    "name_en" => $_POST['name_en9'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
            ///////////////////////////////////////////////////////////////////

            if (!empty($_POST['name_rus0'])) {

                $date = strtotime($_POST['date0']);

                $query = array(
                    "image" => $langData[$_POST['parent0']] . $_POST['image0'] . ".png",
                    "name_rus" => $_POST['name_rus0'] . ".",
                    "name_en" => $_POST['name_en0'] . ".",
                    "date" => $date * 1000
                );

                $collectionHistory = $link->selectCollection("history");
                $collectionHistory->insert($query);
            }
        }

        $m->close();
        ?>

        <table border="4" bordercolor="#000000" cellspacing="0" cellpadding="10" frame="Hsides" rules="Cols">
            <caption>Add Content</caption>
            <form method="POST" action=''>
                <tr>
                    <td>

                        <select id="parent" name="parent" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image" placeholder="image">
                        <input type="text" name="name_rus" placeholder="name_rus">
                        <input type="text" name="name_en" placeholder="name_en">
                        <input type="date" name="date" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >



                    </td>
                </tr>

                <tr><td>

                        <select id="parent1" name="parent1" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image1" placeholder="image">
                        <input type="text" name="name_rus1" placeholder="name_rus">
                        <input type="text" name="name_en1" placeholder="name_en">
                        <input type="date" name="date1" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>
                        <select id="parent2" name="parent2" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image2" placeholder="image">
                        <input type="text" name="name_rus2" placeholder="name_rus">
                        <input type="text" name="name_en2" placeholder="name_en">
                        <input type="date" name="date2" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent3" name="parent3" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image3" placeholder="image">
                        <input type="text" name="name_rus3" placeholder="name_rus">
                        <input type="text" name="name_en3" placeholder="name_en">
                        <input type="date" name="date3" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent4" name="parent4" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image4" placeholder="image">
                        <input type="text" name="name_rus4" placeholder="name_rus">
                        <input type="text" name="name_en4" placeholder="name_en">
                        <input type="date" name="date4" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent5" name="parent5" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image5" placeholder="image">
                        <input type="text" name="name_rus5" placeholder="name_rus">
                        <input type="text" name="name_en5" placeholder="name_en">
                        <input type="date" name="date5" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent6" name="parent6" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image6" placeholder="image">
                        <input type="text" name="name_rus6" placeholder="name_rus">
                        <input type="text" name="name_en6" placeholder="name_en">
                        <input type="date" name="date6" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent7" name="parent7" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image7" placeholder="image">
                        <input type="text" name="name_rus7" placeholder="name_rus">
                        <input type="text" name="name_en7" placeholder="name_en">
                        <input type="date" name="date7" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent8" name="parent8" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image8" placeholder="image">
                        <input type="text" name="name_rus8" placeholder="name_rus">
                        <input type="text" name="name_en8" placeholder="name_en">
                        <input type="date" name="date8" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent9" name="parent9" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image9" placeholder="image">
                        <input type="text" name="name_rus9" placeholder="name_rus">
                        <input type="text" name="name_en9" placeholder="name_en">
                        <input type="date" name="date9" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>

                <tr><td>

                        <select id="parent0" name="parent0" >
                            <option value="0"><?php echo $langData[0] ?></option>
                            <option value="1"><?php echo $langData[1] ?></option>
                            <option value="2"><?php echo $langData[2] ?></option>
                            <option value="3"><?php echo $langData[3] ?></option>
                        </select>
                        <input type="text" name="image0" placeholder="image">
                        <input type="text" name="name_rus0" placeholder="name_rus">
                        <input type="text" name="name_en0" placeholder="name_en">
                        <input type="date" name="date0" placeholder="date"value='1863-01-09' min="1861-01-01" max="1865-12-31" >

                    </td></tr>


                <tr><td >
                        <input type="submit" name="button"  value="Add">
                    </td></tr>
            </form>
        </table>

    </body>
</html>

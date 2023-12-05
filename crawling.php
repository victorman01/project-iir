<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Arial", sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 15px;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            text-align: center;
        }

        nav a {
            display: inline-block;
            color: #fff;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #555;
        }

        main {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            overflow: hidden;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
        }

        p {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            width: 60%;
        }

        button {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        /* Animasi tambahan untuk elemen tabel */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        tr {
            animation: fadeIn 0.5s ease-in-out;
        }
        nav a.active {
         border-bottom: 2px solid white;
      }
    </style>
</head>

<body>
    <header>
        <h1>Google Scholar Crawler </h1>
        <h5>Intelligent Information Retrieval </h5>
    </header>

    <nav>
      <a href="#">Home</a>
      <a href="#" class="active">Crawling</a>
   </nav>

    <main>
        <form method="POST" action="">
            <label for="keyword">Input Keyword:</label>
            <input type="text" id="keyword" name="keyword" value="<?php echo isset($_POST['keyword']) ? htmlspecialchars($_POST['keyword']) : ''; ?>">
            <button type="submit" name="crawls">Crawls</button>
        </form>

        <?php
        include_once('simple_html_dom.php');
        require_once __DIR__ . '/vendor/autoload.php';

        if (isset($_POST['keyword'])) {
            if (empty($_POST['keyword'])) {
                echo '<p style="color: red;">Please enter a keyword.</p>';
                return;
            }
            $key = $_POST['keyword'];
            $key = str_replace(' ', '+', $key);
            $html = file_get_html("https://scholar.google.com/scholar?q=$key&hl=en&as_sdt=0,5&as_rr=1");

            echo '<p>CRAWLING RESULT</p>';
            echo '<table>';
            echo '<tr>';
            echo '<th>Title</th>';
            echo '<th>Number Citations</th>';
            echo '<th>Authors</th>';
            echo '<th>Abstract</th>';
            echo '</tr>';
            foreach ($html->find('div[class="gs_r gs_or gs_scl"]') as $article) {
                $title = $article->find('h3[class="gs_rt"]', 0)->find('a', 0)->plaintext;
                $numCitation = 0;
                $authors = "";
                $abstract = "";

                $linkArticle = $article->find('div[class="gs_ri"]', 0)->find('div[class="gs_a"]', 0)->find('a');
                if (count($linkArticle) > 0) {
                    $link = $linkArticle[0]->href;
                    $html2 = file_get_html("https://scholar.google.com$link");
                    foreach ($html2->find('tr[class="gsc_a_tr"]') as $temp) {
                        $temp = $temp->find('td[class="gsc_a_t"]', 0)->find('a', 0);
                        if ($temp->innertext == $title) {
                            $link2 = $temp->href;
                            $link2 = str_replace("amp;", "", $link2);
                            $link2 = str_replace("hl=id", "hl=en", $link2);
                            $html3 = file_get_html("https://scholar.google.com$link2");

                            foreach ($html3->find('div[class="gs_scl"]') as $data) {
                                $key = $data->find('div', 0)->innertext;
                                if ($key == 'Authors') {
                                    $authors = $data->find('div', 1)->innertext;
                                } elseif ($key == 'Description') {
                                    $temp = $data->find('div', 1);
                                    while (count($temp->find('div')) > 0) {
                                        $temp = $temp->find('div', 0);
                                    }
                                    $abstract = $temp->innertext;
                                } elseif ($key == 'Total citations') {
                                    $cited = $data->find('div', 1)->find('div', 0)->find('a', 0)->innertext;
                                    $numCitation = str_replace("Cited by ", "", $cited);
                                }
                            }
                            break;
                        }
                    }
                }

                echo '<tr>';
                echo "<td>" . $title . "</td>";
                echo "<td>" . $numCitation . "</td>";
                echo "<td>" . $authors . "</td>";
                echo "<td>" . $abstract . "</td>";
                echo '</tr>';
            }
            echo '</table>';
        }
        ?>
    </main>
</body>

</html>

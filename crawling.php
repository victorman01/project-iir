<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Scholar Crawler | Crawl</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php
        $page = 'crawling';
    ?>
    <header>
        <h1>Google Scholar Crawler</h1>
        <h5>Intelligent Information Retrieval</h5>
    </header>

    <nav>
        <a href="index.php" data-href="index.php" <?php echo ($page == 'home') ? 'class="active"' : ''; ?>>Home</a>
        <a href="crawling.php" data-href="crawling.php" <?php echo ($page == 'crawling') ? 'class="active"' : ''; ?>>Crawling</a>
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

        if (isset($_POST['crawls'])) {
            $con = mysqli_connect("localhost:3307", "root", "", "project-iir"); // sesuaikan portnya, kalo 3306 hapus aja 3307 nya
            if (empty($_POST['keyword'])) {
                echo '<p style="color: red;">Please enter a keyword.</p>';
                return;
            }
            $key = str_replace(' ', '+', $_POST['keyword']);
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
                if($title = $article->find('h3[class="gs_rt"]', 0)->find('a', 0)){
                    $title = $article->find('h3[class="gs_rt"]', 0)->find('a', 0)->plaintext;
                    $numCitation = $authors = $abstract = "";
                    $linkArticle = $article->find('div[class="gs_ri"]', 0)->find('div[class="gs_a"]', 0)->find('a');
                    if ($linkArticle) {
                        $html2 = file_get_html("https://scholar.google.com".$linkArticle[0]->href);
                        foreach ($html2->find('tr[class="gsc_a_tr"]') as $temp) {
                            $art = $temp->find('td[class="gsc_a_t"]', 0)->find('a', 0);
                            if ($art->innertext == $title) {
                                $link = str_replace(["amp;", "hl=id"], ["", "hl=en"], $art->href);
                                $html3 = file_get_html("https://scholar.google.com$link");
                                foreach ($html3->find('div[class="gs_scl"]') as $data) {
                                    $key = $data->find('div', 0)->innertext;
                                    $value = $data->find('div', 1);
                                    if ($key == 'Authors') {
                                        $authors = $value->innertext;
                                    } elseif ($key == 'Description') {
                                        while (count($value->find('div')) > 0) {
                                            $value = $value->find('div', 0);
                                        }
                                        $abstract = $value->innertext;
                                    } elseif ($key == 'Total citations') {
                                        $numCitation = str_replace("Cited by ", "", $value->find('div', 0)->find('a', 0)->innertext);
                                    }
                                }
                            $query = "SELECT COUNT(*) FROM articles WHERE title = '$title'";
                            $result = mysqli_fetch_all(mysqli_query($con, $query));
                            if ($result[0][0] == 0) {
                                $query1 = "INSERT INTO articles (title, author, citations, abstract) VALUES ('$title', '$authors', $numCitation, '$abstract')";
                                mysqli_query($con, $query1);
                            }

                            echo '<tr>';
                            echo "<td>" . $title . "</td>";
                            echo "<td>" . $numCitation . "</td>";
                            echo "<td>" . $authors . "</td>";
                            echo "<td>" . $abstract . "</td>";
                            echo '</tr>';
                            }
                        }
                    }
                }
            }
                
            echo '</table>';
        }
        ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('nav a');

            navLinks.forEach(link => {
                link.addEventListener('click', event => {
                    event.preventDefault();

                    const targetPage = link.getAttribute('data-href');

                    document.body.style.backgroundColor = '#fff';
                    document.querySelector('main').style.opacity = 0;

                    setTimeout(() => {
                        window.location.href = targetPage;
                    }, 500);
                });
            });
        });
    </script>
</body>

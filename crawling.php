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
            $con = mysqli_connect("localhost", "root", "", "project-iir", 3307); // sesuaikan portnya, kalo 3306 hapus aja 3307 nya
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
                $numCitation = "";
                $authors = "";
                $abstract = "";

                $linkArticle = $article->find('div[class="gs_ri"]', 0)->find('div[class="gs_a"]', 0)->find('a');
                if (count($linkArticle) > 0) {
                    $linkArticle = $linkArticle[0]->href;
                    $html2 = file_get_html("https://scholar.google.com$linkArticle");
                    foreach ($html2->find('tr[class="gsc_a_tr"]') as $temp) {
                        $temp = $temp->find('td[class="gsc_a_t"]', 0)->find('a', 0);
                        if ($temp->innertext == $title) {
                            $linkArticle2 = $temp->href;
                            $linkArticle2 = str_replace("amp;", "", $linkArticle2);
                            $linkArticle2 = str_replace("hl=id", "hl=en", $linkArticle2);
                            $html3 = file_get_html("https://scholar.google.com$linkArticle2");
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
                                    $numCitation = $data->find('div', 1)->find('div', 0)->find('a', 0)->innertext;
                                    $numCitation = str_replace("Cited by ", "", $numCitation);
                                }
                            }
                        $query = mysqli_query($con, "select count(*) from articles where title = '$title'");
                        $x = mysqli_fetch_all($query);

                        if ($x[0][0] == 0) {
                            $query = mysqli_query($con, "insert into articles (title, author, citations, abstract) values ('$title', '$authors', $numCitation, '$abstract')");
                        }
                        echo '<tr>';
                        echo "<td>" . $title . "</td>";
                        echo "<td>" . $numCitation . "</td>";
                        echo "<td>" . $authors . "</td>";
                        echo "<td>" . $abstract . "</td>";
                        echo '</tr>';
                        break;
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

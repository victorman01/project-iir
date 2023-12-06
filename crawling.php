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
            $dbConnection = mysqli_connect("localhost", "root", "", "project-iir", 3307);

            if (empty($_POST['keyword'])) {
                echo '<p style="color: red;">Please enter a keyword.</p>';
                return;
            }

            $searchKeyword = str_replace(' ', '+', $_POST['keyword']);
            $searchUrl = "https://scholar.google.com/scholar?q=$searchKeyword&hl=en&as_sdt=0,5&as_rr=1";

            $html = file_get_html($searchUrl);

            echo '<p>CRAWLING RESULT</p>';
            echo '<table>';
            echo '<tr>';
            echo '<th>Title</th>';
            echo '<th>Number Citations</th>';
            echo '<th>Authors</th>';
            echo '<th>Abstract</th>';
            echo '</tr>';

            foreach ($html->find('div[class="gs_r gs_or gs_scl"]') as $article) {
                $title = $article->find('h3[class="gs_rt"] a', 0)->plaintext;
                $linkArticle = $article->find('div[class="gs_ri"] div[class="gs_a"] a', 0);

                if ($linkArticle) {
                    $linkArticle = str_replace(["amp;", "hl=id"], ["", "hl=en"], $linkArticle->href);
                    $html2 = file_get_html("https://scholar.google.com$linkArticle");

                    foreach ($html2->find('div[class="gs_scl"]') as $data) {
                        $key = $data->find('div', 0)->innertext;
                        $value = $data->find('div', 1)->innertext;

                        if ($key == 'Authors') {
                            $authors = $value;
                        } elseif ($key == 'Description') {
                            $abstract = strip_tags($value);
                        } elseif ($key == 'Total citations') {
                            $numCitations = str_replace("Cited by ", "", $value);
                        }
                    }

                    $query = mysqli_query($dbConnection, "SELECT COUNT(*) FROM articles WHERE title = '$title'");
                    $result = mysqli_fetch_all($query);

                    if ($result[0][0] == 0) {
                        $query = mysqli_query($dbConnection, "INSERT INTO articles (title, author, citations, abstract) VALUES ('$title', '$authors', $numCitations, '$abstract')");
                    }

                    echo '<tr>';
                    echo "<td>$title</td>";
                    echo "<td>$numCitations</td>";
                    echo "<td>$authors</td>";
                    echo "<td>$abstract</td>";
                    echo '</tr>';
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

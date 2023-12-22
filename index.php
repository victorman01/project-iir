<?php
include_once('simple_html_dom.php');
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/language-detector/Text/LanguageDetect.php';
require_once __DIR__ . '/porter2-master/demo/process.inc';

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Math\Distance\Euclidean;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Google Scholar Crawler | Home</title>
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <link rel="stylesheet" href="css/styles.css">
   <style>
      .container {
         display: flex;
         justify-content: space-between;
      }

      .results-container {
         width: 90%;
      }

      .suggestions-container {
         width: 8%;
      }

      .Journal {
         border: 1px solid #ccc;
         padding: 10px;
         margin-bottom: 15px;
      }
   </style>

</head>

<body>
   <?php
   $page = 'home';
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
      <div>
         <form method="GET" action="#" onsubmit="return validateForm()">
            <label for="keyword">Input Keyword:</label>
            <input type="text" id="keyword" name="keyword"
               value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
            <button type="submit" name="search">Search</button>
            <div class="radio-group">
               <label>
                  <input type="radio" name="search-type" value="euclidean" <?= (isset($_GET["search-type"]) && $_GET["search-type"] == "euclidean") ? "checked" : ""; ?>>
                  <span>Euclidean</span>
               </label>
               <label>
                  <input type="radio" name="search-type" value="jaccard" <?= (isset($_GET["search-type"]) && $_GET["search-type"] == "jaccard") ? "checked" : ""; ?>>
                  <span>Jaccard</span>
               </label>
            </div>
            <!-- Combobox untuk memilih jumlah item per halaman -->
            <label for="items-per-page">Items Per Page:</label>
            <select name="items-per-page" id="items-per-page">
               <option value="3" <?= (isset($_GET["items-per-page"]) && $_GET["items-per-page"] == 3) ? "selected" : ""; ?>>3</option>
               <option value="5" <?= (isset($_GET["items-per-page"]) && $_GET["items-per-page"] == 5) ? "selected" : ""; ?>>5</option>
               <option value="10" <?= (isset($_GET["items-per-page"]) && $_GET["items-per-page"] == 10) ? "selected" : ""; ?>>10</option>
            </select>

            <p id="error-message" style="color: red;"></p>

         </form>


         <script>
            function validateForm() {
               var searchTypeEuclidean = document.querySelector('input[name="search-type"][value="euclidean"]');
               var searchTypeJaccard = document.querySelector('input[name="search-type"][value="jaccard"]');
               var errorMessage = document.getElementById('error-message');

               if (!searchTypeEuclidean.checked && !searchTypeJaccard.checked) {
                  errorMessage.textContent = "Please select a search method (Euclidean or Jaccard).";
                  return false;
               } else {
                  errorMessage.textContent = "";
                  return true;
               }
            }
         </script>

         <?php

         function detectLanguage($term)
         {
            $ld = new Text_LanguageDetect();
            $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
            $stemmer = $stemmerFactory->createStemmer();

            $stopwordFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
            $stopword = $stopwordFactory->createStopWordRemover();

            $results = $ld->detect($term, 10);
            $languageFound = false;
            $output = "";

            foreach ($results as $language => $confidence) {
               if ($language == 'indonesian') {
                  $outputStem = $stemmer->stem($term);
                  $output = $stopword->remove($outputStem);
                  $languageFound = true;
               } else if ($language == 'english') {
                  $output = porterstemmer_process($term);
                  $languageFound = true;
               }
            }
            if (!$languageFound) {
               $output = porterstemmer_process($term);
            }
            return $output;
         }

         if (isset($_GET['search'])) {
            $con = mysqli_connect("localhost", "root", "", "project-iir");
            if (empty($_GET['keyword'])) {
               echo '<p style="color: red;">Please enter a keyword.</p>';
               return;
            }
            if (empty($_GET['search-type'])) {
               echo '<p style="color: red;">Please select a type.</p>';
               return;
            }
            $search_type = $_GET['search-type'];

            $sample_data = array();
            $title = array();
            $author = array();
            $citations = array();
            $abstract = array();
            $similarity_data = array();

            $sql = "SELECT title, author, citations, abstract FROM articles";
            $result = mysqli_query($con, $sql);

            $i = 0;

            if (mysqli_num_rows($result) > 0) {
               while ($row = mysqli_fetch_assoc($result)) {
                  $title[$i] = $row["title"];
                  $author[$i] = $row["author"];
                  $citations[$i] = $row["citations"];
                  $abstract[$i] = $row["abstract"];

                  $sample_data[$i] = $row["title"];
                  $i++;
               }
               $sample_data[] = $_GET['keyword'];

               for ($j = 0; $j < count($sample_data); $j++) {
                  $sample_data[$j] = detectLanguage($sample_data[$j]);

               }

            } else {
               echo '<p style="color: red;">Please do crawling first.</p>';
               return;
            }


            // Calculate TF
            $tf = new TokenCountVectorizer(new WhitespaceTokenizer());
            $tf->fit($sample_data);
            $tf->transform($sample_data);
            $vocabulary = $tf->getVocabulary();

            // Calculate TF-IDF
            $tfidf = new TfIdfTransformer($sample_data);
            $tfidf->transform($sample_data);

            $count_data = count($sample_data);

            // Calculate Jaccard Similarity (Custom Function)
            function calculateJaccardSimilarity($set1, $set2)
            {
               $intersection = count(array_intersect($set1, $set2));
               $union = count(array_unique(array_merge($set1, $set2)));

               return $union > 0 ? $intersection / $union : 0;
            }


            // Calculate Euclidean Similarity
            $euclidean = new Euclidean();
            if ($_GET["search-type"] == "euclidean") {
               for ($i = 0; $i < $count_data - 1; $i++) {
                  $similarity = $euclidean->distance($sample_data[$i], $sample_data[$count_data - 1]);
                  array_push($similarity_data, round($similarity, 3));
               }
               array_multisort($similarity_data, SORT_ASC, SORT_NUMERIC, $title, $citations, $author, $abstract);
            } else if ($_GET["search-type"] == "jaccard") {
               for ($i = 0; $i < $count_data - 1; $i++) {
                  $similarity = calculateJaccardSimilarity($sample_data[$i], $sample_data[$count_data - 1]);
                  array_push($similarity_data, round($similarity, 3));
               }
               array_multisort($similarity_data, SORT_DESC, SORT_NUMERIC, $title, $citations, $author, $abstract);
            } else {
               echo '<p style="color: red;">Please select available type.</p>';
               return;
            }


            $itemsPerPage = (isset($_GET["items-per-page"]) && is_numeric($_GET['items-per-page'])) ? intval($_GET["items-per-page"]) : 5;
            $itemsPerPage = ($itemsPerPage > 0) ? $itemsPerPage : 5;

            // Menghitung indeks awal dan jumlah item untuk query
            $startIndex = 0;  // Default indeks awal
            $endIndex = $itemsPerPage;
            $currentPage = 1;

            if (isset($_GET['page']) && is_numeric($_GET['page'])) {
               $currentPage = ($_GET['page'] > 0) ? $_GET['page'] : 1;
               $startIndex = ($currentPage - 1) * $itemsPerPage;
               $endIndex = $startIndex + $itemsPerPage;
            }

            // Print the table
            echo '<p>SEARCH RESULT</p>';
            echo '<table>';
            echo '<tr>';
            echo '<th>Title</th>';
            echo '<th>Number Citations</th>';
            echo '<th>Authors</th>';
            echo '<th>Abstract</th>';
            echo '<th>Similarity Score</th>';
            echo '</tr>';

            for ($i = $startIndex; $i < min($endIndex, count($title)); $i++) {
               echo '<tr>';
               echo "<td>" . $title[$i] . "</td>";
               echo "<td>" . $citations[$i] . "</td>";
               echo "<td>" . $author[$i] . "</td>";
               echo "<td>" . $abstract[$i] . "</td>";
               echo "<td>" . $similarity_data[$i] . "</td>";
               echo '</tr>';
            }

            echo '</table>';

            echo "<div class='page-numbers'>";
            $totalPages = ceil(count($title) / $itemsPerPage);
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);

            if ($currentPage > 1) {
               echo "<a class='page-number' href='?keyword=" . htmlspecialchars($_GET['keyword']) . "&search=&search-type=$search_type&items-per-page=$itemsPerPage&page=1#'>First</a>&emsp;";
               $prevPage = $currentPage - 1;
               echo "<a class='page-number' href='?keyword=" . htmlspecialchars($_GET['keyword']) . "&search=&search-type=$search_type&items-per-page=$itemsPerPage&page=$prevPage#'>Prev</a>&emsp;";
            }

            for ($i = $startPage; $i <= $endPage; $i++) {
               if ($i == $currentPage) {
                  echo "<span style='color: darkblue;'>$i</span>&emsp;";
               } else {
                  echo "<a class='page-number' href='?keyword=" . htmlspecialchars($_GET['keyword']) . "&search=&search-type=$search_type&items-per-page=$itemsPerPage&page=$i#'>$i</a>&emsp;";
               }
            }

            if ($currentPage < $totalPages) {
               $nextPage = $currentPage + 1;
               echo " <a class='page-number' href='?keyword=" . htmlspecialchars($_GET['keyword']) . "&search=&search-type=$search_type&items-per-page=$itemsPerPage&page=$nextPage#'>Next</a>&emsp;";
               echo " <a class='page-number' href='?keyword=" . htmlspecialchars($_GET['keyword']) . "&search=&search-type=$search_type&items-per-page=$itemsPerPage&page=$totalPages#'>Last</a>&emsp;";
            }

            echo "</div>";
         }

         ?>


      </div>
      <?php
      function getQueryExpansions($conn, $query)
      {
         $queryExpansions = [];

         $fetchTopArticlesSQL = "SELECT title, abstract FROM articles";
         $topArticlesResult = $conn->query($fetchTopArticlesSQL);

         if ($topArticlesResult->num_rows > 0) {
            while ($articleRow = $topArticlesResult->fetch_assoc()) {
               // hitung jarak antara keyword dengan setiap judul dan abstrak
               $titleSimilarity = calculateSimilarity($query, $articleRow['title']);
               $abstractSimilarity = calculateSimilarity($query, $articleRow['abstract']);
               $totalSimilarity = ($titleSimilarity + $abstractSimilarity) / 2;

               // simpan ke array
               $queryExpansions[] = [
                  'query' => $query . " " . $articleRow['title'] . " " . $articleRow['abstract'],
                  'similarity' => $totalSimilarity,
               ];
            }
         }

         // Sort by desc
         usort($queryExpansions, function ($a, $b) {
            return $b['similarity'] - $a['similarity'];
         });

         // Return top 3 query expansions
         return array_slice($queryExpansions, 0, 3);
      }

      function calculateSimilarity($string1, $string2)
      {
         similar_text($string1, $string2, $similarity);
         return $similarity;
      }

      function generateFiveWords($query, $numWords)
      {
         $words = explode(" ", $query);
         // akan generate 5 kata
         return array_slice($words, 1, $numWords);
      }

      if (isset($_GET['keyword']) && isset($_GET["search-type"])) {
         $userQuery = isset($_GET['keyword']) ? strtolower($_GET['keyword']) : '';
         $conn = mysqli_connect("localhost", "root", "", "project-iir");
         $queryExpansions = getQueryExpansions($conn, $userQuery);
         // echo "Top 3 Query Expansions:<br>";
         // foreach ($queryExpansions as $queryExpansion) {
         //    echo $queryExpansion['query'] . " - Similarity: " . $queryExpansion['similarity'] . "<br>";
         // }
      
         //buat 5 kata setiap query expansion, hitung jaraknya
         $expandedQueries = [];
         foreach ($queryExpansions as $queryExpansion) {
            for ($i = 2; $i <= 6; $i++) {
               $words = generateFiveWords($queryExpansion['query'], $i);
               $expandedQuery = strtolower(implode(" ", $words));

               $output = detectLanguage($expandedQuery);

               $similarity = calculateSimilarity($userQuery, $output);

               $expandedQueries[] = [
                  'query' => $expandedQuery,
                  'similarity' => $similarity,
               ];
            }
         }

         // Display the top 3 expanded queries
         echo "<br>Top 3 Expanded Queries:<br>";

         usort($expandedQueries, function ($a, $b) {
            return $b['similarity'] - $a['similarity'];
         });
         foreach (array_slice($expandedQueries, 0, 3) as $expandedQuery) {
            $urlParams = http_build_query([
               'keyword' => $expandedQuery['query'],
               'search-type' => $_GET['search-type'],
               'search' => "",
               'items-per-page' => $_GET['items-per-page']
            ]);
            $expandedQuery['query'] = str_replace($userQuery, "<b>$userQuery</b>", $expandedQuery['query']);
            $targetURL = "?" . $urlParams;
            echo "<a href='$targetURL'>" . $expandedQuery['query'] . "</a><br>";
         }
         // Close the database connection
         $conn->close();
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

         const itemsPerPageSelect = document.getElementById('items-per-page');

         // Tambahkan event listener untuk perubahan pada elemen items-per-page
         itemsPerPageSelect.addEventListener('change', () => {
            // Dapatkan nilai items-per-page yang dipilih
            const selectedItemsPerPage = itemsPerPageSelect.value;

            // Dapatkan nilai search dari URL
            const searchParam = getQueryParam('search');

            // Bangun URL baru dengan menyertakan search dan items-per-page
            const newURL = `?keyword=${getQueryParam('keyword')}&search=${searchParam}&search-type=${getQueryParam('search-type')}&items-per-page=${selectedItemsPerPage}`;

            // Redirect ke URL baru
            window.location.href = newURL;
         });

         // Fungsi untuk mendapatkan nilai parameter dari URL
         function getQueryParam(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
         }
      });
   </script>
</body>

</html>
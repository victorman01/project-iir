<?php 
include_once('simple_html_dom.php');
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Math\Distance\Euclidean;

$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer = $stemmerFactory->createStemmer();

$stopwordFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
$stopword = $stopwordFactory->createStopWordRemover();

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Google Scholar Crawler | Home</title>
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
            <input type="text" id="keyword" name="keyword" value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
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
            <p id="error-message" style="color: red;"></p>

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
                  for ($i=2; $i <= 6; $i++) { 
                     $words = generateFiveWords($queryExpansion['query'], $i);
                     $expandedQuery = strtolower(implode(" ", $words));
   
                     $outputStem = $stemmer->stem($expandedQuery);
                     $outputStop = $stopword->remove($outputStem);
   
                     $similarity = calculateSimilarity($userQuery, $outputStop);
   
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
                  $expandedQuery['query'] = str_replace($userQuery, "<b>$userQuery</b>", $expandedQuery['query']);
                  echo $expandedQuery['query'] . " - Similarity: " . $expandedQuery['similarity'] . "<br>";
               }
               // Close the database connection
               $conn->close();
            }

            ?>
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

         if (isset($_GET['search'])) {
            $con = mysqli_connect("localhost", "root", "", "project-iir"); // sesuaikan portnya, kalo 3306 hapus aja 3307 nya
            if (empty($_GET['keyword'])) {
               echo '<p style="color: red;">Please enter a keyword.</p>';
               return;
            }
            if (empty($_GET['search-type'])) {
               echo '<p style="color: red;">Please select a type.</p>';
               return;
            }

            echo '<p>SEARCH RESULT</p>';
            echo '<table>';
            echo '<tr>';
            echo '<th>Title</th>';
            echo '<th>Number Citations</th>';
            echo '<th>Authors</th>';
            echo '<th>Abstract</th>';
            echo '<th>Similarity Score</th>';
            echo '</tr>';

            $sample_data = array();
            $title = array();
            $author = array();
            $citations = array();
            $abstract = array();
            $similarity_data = array();

            $sql = "SELECT title, author, citations, abstract  FROM articles";
            $result = mysqli_query($con, $sql);

            $i = 0;

            if (mysqli_num_rows($result) > 0) {
               while ($row = mysqli_fetch_assoc($result)) {
                  $title[$i] = $row["title"];
                  $author[$i] = $row["author"];
                  $citations[$i] = $row["citations"];
                  $abstract[$i] = $row["abstract"];

                  $stemTitle = $stemmer->stem($row["title"]);
                  $stopTitle = $stopword->remove($stemTitle);
                  $sample_data[$i] = $stopTitle;
                  $i++;
               }
               $outputStem = $stemmer->stem($_GET["keyword"]);
               $outputStop = $stopword->remove($outputStem);

               $sample_data[] = $outputStop;
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


            // Calculate Euclidean Similarity`
            $euclidean = new Euclidean();
            if ($_GET["search-type"] == "euclidean") {
               for ($i = 0; $i < $count_data - 1; $i++) {
                  $similairty = $euclidean->distance($sample_data[$i], $sample_data[$count_data - 1]);
                  array_push($similarity_data, round($similairty, 3));
               }
               array_multisort($similarity_data, SORT_ASC, SORT_NUMERIC, $title, $citations, $author, $abstract);
            } else if ($_GET["search-type"] == "jaccard") {
               for ($i = 0; $i < $count_data - 1; $i++) {
                  $similarity = calculateJaccardSimilarity($sample_data[$i], $sample_data[$count_data - 1]);
                  array_push($similarity_data, round($similarity, 3));
               }

               array_multisort($similarity_data, SORT_DESC, SORT_NUMERIC, $title, $citations, $author, $abstract);
            }

            // Print the table
            for ($i = 0; $i < $count_data - 1; $i++) {
               echo '<tr>';
               echo "<td>" . $title[$i] . "</td>";
               echo "<td>" . $citations[$i] . "</td>";
               echo "<td>" . $author[$i] . "</td>";
               echo "<td>" . $abstract[$i] . "</td>";
               echo "<td>" . $similarity_data[$i] . "</td>";
               echo '</tr>';
            }

            echo '</table>';
         }

         ?>


      </div>
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

</html>
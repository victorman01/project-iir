<?php
include_once('simple_html_dom.php');

echo '<style>
   body {
      font-family: "Arial", sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
   }
   header {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 15px;
   }
   main {
      max-width: 800px;
      margin: 20px auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
   }
   table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
   }
   th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center; /* Center the text */
   }
   th {
      background-color: #333;
      color: #fff;
   }
   p {
      font-size: 18px;
      font-weight: bold;
      text-align: center;
      margin-bottom: 15px;
   }
   form {
      text-align: center;
   }
   input[type="text"] {
      padding: 10px;
      font-size: 16px;
   }
   input[type="submit"] {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #333;
      color: #fff;
      border: none;
      cursor: pointer;
   }
   input[type="submit"]:hover {
      background-color: #555;
   }
</style>';

echo '<header>
         <h1>Google Scholar Crawler</h1>
      </header>';

echo '<main>';
echo '<form method="POST" action="">
      Input Keyword <input type="text" name="keyword">
      <input type="submit" name="crawls" value="Crawls"><br>
      </form>';

if(isset($_POST['keyword'])){
   $key = $_POST['keyword'];
   $key = str_replace(' ', '+', $key);
   $html = file_get_html("https://scholar.google.com/scholar?hl=en&as_sdt=0%2C5&q=".$key."&btnG=");

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
      $numCitation = $article->find('div[class="gs_fl gs_flb"]', 0)->find('a', 2)->plaintext;
      $numCitation = explode(' ', $numCitation);
      $numCitation = (count($numCitation) >= 3 ? $numCitation[2] : '0');
      $authorsFull = $article->find('div[class="gs_a"]', 0)->plaintext;
      $authors = explode('-', $authorsFull)[0];
      $abstract = $article->find('div[class="gs_rs"]', 0)->plaintext;
   
      echo '<tr>';
      echo "<td>" . $title . "</td>";
      echo "<td>" . $numCitation . "</td>";
      echo "<td>" . $authors . "</td>";
      echo "<td>" . $abstract . "</td>";
      echo '</tr>';
   }
   echo '</table>';
}

echo '</main>';
?>

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
         width: 90%; /* Adjust the width as needed */
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
         <form method="GET" action="#">
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
         </form>
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

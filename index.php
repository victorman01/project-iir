<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Google Scholar Crawler | Home</title>
   <link rel="stylesheet" href="css/styles.css">
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
      <h1 style="text-align:center;">WELCOME</h1>
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

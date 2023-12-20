<?php
    require_once __DIR__.'/language-detector/Text/LanguageDetect.php';
    require_once __DIR__.'/porter2-master/demo/process.inc';

    $text = 'manchester united is the best club in the world';

    $ld = new Text_LanguageDetect();
    //3 most probable languages
    $results = $ld->detect($text, 10);

    foreach ($results as $language => $confidence) {
        echo $language . ': ' . number_format($confidence, 2) . "\n";
    }

    $output = porterstemmer_process($text);
    echo $output;

    //output:
    //german: 0.35
    //dutch: 0.25
    //swedish: 0.20

    // if(file_exists('language-detector/Text/LanguageDetect.php')){
    //     echo "ADA";
    // }
    // else echo "tidak ada";
    ?>

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$info = array();
$dir = __DIR__ . "/data/$id";
chdir($dir);
if (is_file("info.json")) {
  $info = json_decode(file_get_contents("info.json"), true);
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Shared Brotli Dynamic Dictionary Tester - Result</title>
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
      }
      table {
          border-collapse: collapse;
      }
      th, td {
        text-align: center;
        padding: 8px;
      }
      tr:nth-child(even) {background: #EEE}
      tr:nth-child(odd) {background: #FFF}
    </style>
  </head>
  <body>
    <h1>Brotli Shared Dictionary Compression Results</h1>
    <p>The test fetched each of the URLs using an anonymous connection with no cookies and the requested user agent string:</p>
    <?php
    $ua = isset($info['ua']) ? $info['ua'] : 'Not set';
    echo "<p><pre>" . htmlspecialchars($ua) . "</pre></p>";
    ?>
    <p>Then, for each HTML response, it generated an HTML dictionary using the responses from all of the other URLs (excluding the URL being evaluated) to see how well a dictionary generated from the other pages would compress the page being tested.</p>
    <h1>Summary</h1>
    <p>Brotli level 5 is the sweet spot where most of the benefits in using an external dictionary are realized.</p>
    <?php
    $count = 0;
    $min = null;
    $max = null;
    $total = 0;
    foreach ($info['results'] as $i => $entry) {
      if (isset($entry['comp']) &&
          is_array($entry['comp']) &&
          isset($entry['comp']['original']) &&
          isset($entry['comp']['br']['5']) &&
          isset($entry['comp']['sbr']['5'])) {
        $br = $entry['comp']['br']['5'];
        $sbr = $entry['comp']['sbr']['5'];
        $s = 100 - intval(round((floatval($sbr) / floatval($br)) * 100.0));
        $total += $s;
        $count += 1;
        if (!isset($min) || $s < $min) {
          $min = $s;
        }
        if (!isset($max) || $s > $max) {
          $max = $s;
        }
      }
    }
    if ($count > 0) {
      $avg = intval(round(floatval($total) / floatval($count)));
      echo("<p>Using a custom {$info['size']} KB compression dictionary for the requested pages resulted in HTML that was $min% to $max% smaller than brotli alone ($avg% average).</p>");
    }
    if (is_file("dictionary.dat")) {
      $size = number_format(filesize("dictionary.dat"));
      $br_size = is_file("dictionary.dat.br") ? number_format(filesize("dictionary.dat.br")) : 0;
      echo "<p>A dictionary was created using all of the provided URLs for future use. You can download it <a href='data/$id/dictionary.dat'>here</a>. It is $size bytes ($br_size compressed with brotli 11).</p>";
    }
    echo '<p><a href="/shared-brotli/static/">Run a new test</a></p>';
    echo "<h1>Details</h1>\n";
    foreach ($info['results'] as $i => $entry) {
      echo "<h2>" . htmlspecialchars($entry['url']) . "</h2>\n";
      if (isset($entry['comp']) && is_array($entry['comp']) && isset($entry['comp']['original']) && isset($entry['comp']['br']) && isset($entry['comp']['sbr'])) {
        echo "<table>\n";
        echo "<tr><th>Compression Level</th><th>Original Size</th><th>Brotli</th><th>Relative Size</th><th>With Dictionary</th><th>Relative Size</th><th>Relative to Brotli</th></tr>\n";
        $original = $entry['comp']['original'];
        $o = number_format($original);
        for ($level = 1; $level <= 11; $level++) {
          if ($original > 0 && isset($entry['comp']['br']["$level"]) && isset($entry['comp']['sbr']["$level"])) {
            $br = $entry['comp']['br']["$level"];
            $sbr = $entry['comp']['sbr']["$level"];
            $br_relative = intval(round((floatval($br) / floatval($original)) * 100.0));
            $sbr_relative = intval(round((floatval($sbr) / floatval($original)) * 100.0));
            $br_sbr = intval(round((floatval($sbr) / floatval($br)) * 100.0));
            $br_sbr_r = 100 - $br_sbr;
            $br = number_format($br);
            $sbr = number_format($sbr);
            echo("<tr><td>$level</td><td>$o</td><td>$br</td><td>$br_relative%</td><td>$sbr</td><td>$sbr_relative%</td><td>$br_sbr% ($br_sbr_r% smaller)</td></tr>\n");
          }
        }
        echo "</table>";
      } else {
        echo("<p>Data Missing</p>\n");
      }
    }
    ?>
  </body>
</html>
<!DOCTYPE html>
<html>
  <head>
    <title>Shared Brotli Dynamic Dictionary Tester</title>
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
      }
    </style>
  </head>
  <body>
    <h1>Shared Brotli Dynamic Dictionary Tester</h1>
    <p>This will evaluate the effectiveness of using a custom dictionary for <a href="https://github.com/google/brotli">brotli compression</a>.</p>
    <p>Given a list of (up to 100) URLs, it will compress each URL with brotli using an external dictionary (brotli -D) generated from the other
      pages in the list (excluding the page to be tested). The dictionaries are generated using
      <a href="https://github.com/google/brotli/blob/master/research/dictionary_generator.cc">dictionary_generator</a>.
      Unless specified otherwise, the generated dictionary will use a target length of 64k.
    </p>
    <p>There is a version of this test for use with static content (i.e. JavaScript bundle updates) <a href="/shared-brotli/static/">here</a>.</p>
    <p>The URLs to be tested will be fetched anonymously (no cookies) using the User Agent string specified so they must be publicly available.</p>
    <p>Please provide full URLs including the scheme (i.e. https://www.example.com/page1)</p>
    <form action="start.php" method="post">
      <p>
        <label for="urls">List of URLs to use (one per line):</label><br>
        <textarea id="urls" name="urls" rows="12" cols="160"></textarea>
      </p>
      <p>
        <label for="size">Dictionary Size (in KB):</label>
        <input type="number" id="size" name="size" min="4" max="4096" value="1024">
      </p>
      <p>
        <label for="ua">User Agent String:</label>
        <?php
        $ua = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
        echo "<input type='text' id='ua' name='ua' size='160' value='$ua'>\n";
        ?>
      </p>
      <input type="submit" value="Submit">
    </form>
  </body>
</html>

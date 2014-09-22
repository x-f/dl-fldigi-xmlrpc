<?php

if (isset($_GET['wf'])) {
  // sleep(1); // testing
  ob_start("ob_gzhandler");
  header('Accept-Encoding: gzip');

  $wf_src = dirname(__FILE__) . "/wf-" . $_GET['wf'] . ".png";

  list($width) = @getimagesize($wf_src);
  if ($width > 100) {
    $wf = file_get_contents($wf_src);
    if (isset($_GET['b'])) {
      echo base64_encode($wf);
    } else {
      header("Content-type: image/png");
      echo $wf;
    }
  } else {
    // invalid image
  }
  // ob_flush();
}

?>
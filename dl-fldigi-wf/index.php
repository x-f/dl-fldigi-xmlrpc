<?php
function png2jpg($originalFile, $outputFile, $quality) {
  $image = imagecreatefrompng($originalFile);
  imagejpeg($image, $outputFile, $quality);
  imagedestroy($image);
}

if (isset($_GET['wf'])) {
  // sleep(1); // testing

  $wf_src = dirname(__FILE__) . "/wf-" . $_GET['wf'] . ".png";

  list($width) = @getimagesize($wf_src);

  if ($width > 100) {
  
    if (!isset($_GET['c'])) {
      $wf = file_get_contents($wf_src);
      ob_start("ob_gzhandler");
      header('Accept-Encoding: gzip');
      if (isset($_GET['b'])) {
        echo base64_encode($wf);
      } else {
        header("Content-type: image/png");
        echo $wf;
      }
      ob_flush();
    } else {
      //copy($wf_src, dirname(__FILE__) . "/wf-" . $_GET['wf'] . "-web.png");
      png2jpg($wf_src, dirname(__FILE__) . "/wf-" . $_GET['wf'] . "-web.jpg", 80);
      if (!isset($_GET['a'])) {
        $wf = file_get_contents(dirname(__FILE__) . "/wf-" . $_GET['wf'] . "-web.jpg");
        header("Content-type: image/jpeg");
        echo $wf;
      } else
        echo ".";
    }
  } else {
    // invalid image
    if (isset($_GET['c'])) {
      if (!isset($_GET['a'])) {
        error_log($_GET['wf'] . ": serving cached");
        $wf = file_get_contents(dirname(__FILE__) . "/wf-" . $_GET['wf'] . "-web.jpg");
        header("Content-type: image/jpeg");
        echo $wf;
      } //else
        // echo "..";
    }
  }
}

?>
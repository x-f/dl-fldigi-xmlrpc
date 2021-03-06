<?php
require_once dirname(__FILE__) . "/includes/config.php";
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>radiostation7</title>

<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cousine"/>
<script src="./assets/js/jquery.min.js"></script>
<script src="./assets/js/jquery.base64.min.js"></script>
<script src="./assets/js/jquery.mousewheel.min.js"></script>
<link rel="stylesheet" href="./assets/css/jquery-ui.css"/>
<script src="./assets/js/jquery-ui.min.js"></script>

<link rel="stylesheet" href="./assets/css/style.css"/>
<script src="./assets/js/lib.js"></script>
</head>
<body>
<div id="dev"></div>

<?php
foreach ($dlfldigi_instances as $dl_name => $dl_params) {
  ?>
<div class="dl-instance" id="<?php echo $dl_name;?>">
  <h2>dl-fldigi: <?php echo $dl_name; ?></h2>
    
    <div class='wf-container'>
      <div class='wf-carrier'></div>
      <img src="./assets/i/loading.gif" class="wf-loading" alt=""/>
      <img src='./dl-fldigi-wf/?c&amp;wf=<?php echo $dl_name;?>' class='wf' data-src='./dl-fldigi-wf/?b&amp;c&amp;wf=<?php echo $dl_name;?>' alt=""/>
    </div>
  
  Frequency:
  <input type='text' class='main_get_frequency' value='--' data-step='100' size='9'/>+<input type='text' readonly='readonly' class='modem_get_carrier' value='--' size='4'/> Hz
  &nbsp;&nbsp;
  
  <a href='#' class='button modem_inc_carrier_ctrl' data-dir='dec'>«</a><select class='modem_inc_carrier_step'>
    <option value='10'>10 Hz</option>
    <option value='100' selected='selected'>100 Hz</option>
    <option value='1000'>1 kHz</option>
    <option value='10000'>10 kHz</option>
    <option value='100000'>100 kHz</option>
    <option value='1000000'>1 MHz</option>
  </select><a href='#' class='button modem_inc_carrier_ctrl' data-dir='inc'>»</a>
    
  <div class="keep-right">
    Modem: 
    <select class='modem_get_name'>
      <option value="NONE">NONE</option>
      <option value="RTTY">RTTY</option>
      <option value="CW">CW</option>
      <option value="CTSTIA">Contestia</option>
      <optgroup label="THOR">
        <option value="THOR4">THOR 4</option>
        <option value="THOR5">THOR 5</option>
        <option value="THOR8">THOR 8</option>
        <option value="THOR11">THOR 11</option>
        <option value="THOR16">THOR 16</option>
        <option value="THOR22">THOR 22</option>
      </optgroup>
      <optgroup label="DominoEX">
        <option value="DOMEX4">DominoEX 4</option>
        <option value="DOMEX5">DominoEX 5</option>
        <option value="DOMEX8">DominoEX 8</option>
        <option value="DOMX11">DominoEX 11</option>
        <option value="DOMX16">DominoEX 16</option>
        <option value="DOMX22">DominoEX 22</option>
      </optgroup>
    </select>
    
    Mode:
    <select class='rig_get_mode'>
      <option value="NONE">NONE</option>
      <option value="USB">USB</option>
      <option value="LSB">LSB</option>
      <option value="AM">AM</option>
      <option value="FM">FM</option>
    </select>
  </div>

  <br/>
  <span href="#" class="text_clear_rx">clear</span>
  <textarea class='rx_get_data' _class='text_get_rx'></textarea>
  
  <input type='text' readonly='readonly' class='main_get_status1' value='--' size='10'/>
  <input type='text' readonly='readonly' class='main_get_status2' value='--' size='10'/>

  &nbsp;&nbsp;&nbsp;
  <a href="#" class="button main_get_rsid" data-enabled="false"><span>✓</span> RSID</a>
  <a href="#" class="button left main_get_afc" data-enabled="false"><span>✓</span> AFC</a>
  

  <div class="keep-right">
    <div class="slider" data-upd-type="wf">
      Waterfall updates
      <div class="slider-range"></div>
      <span class="upd_wf_value">off</span>
      <input type="hidden" class="upd_wf" value="0"/>
    </div>

    <div class="slider" data-upd-type="data">
      Data updates
      <div class="slider-range"></div>
      <span class="upd_data_value">off</span>
      <input type="hidden" class="upd_data" value="0"/>
    </div>
  </div><!-- keep-right -->
  
</div><!-- dl-instance -->
  <?php
  // break;
}
?>
</body>
</html>
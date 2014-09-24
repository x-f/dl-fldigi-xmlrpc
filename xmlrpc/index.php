<?php
require_once dirname(__FILE__) . "/../includes/config.php";
require_once dirname(__FILE__) . "/../includes/phpxmlrpc/lib/xmlrpc.inc";


function dlfldigi_call($instance, $key, $value1 = null, $value2 = null) {
  global $dlfldigi_instances;
  
  $message = new xmlrpcmsg($key);
  // var_dump(is_int(intval($value1)), is_string($value1));
  $type = "int";
  if (in_array($key, array("main.set_wf_sideband", "modem.set_by_name", "rig.set_mode")))
    $type = "string";
  if (in_array($key, array("main.set_rsid", "main.set_afc")))
    $type = "boolean";
  if (in_array($key, array("rig.set_frequency")))
    $type = "double";

  if ($value1 !== false) {
    if ($type == "boolean" && $value1 === "true") $value1 = true;
    if ($type == "boolean" && $value1 === "false") $value1 = false;
    $message->addParam(new xmlrpcval($value1, $type));
  }
  //if ($value2 !== false)
  //  $message->addParam(new xmlrpcval($value2, $type));

  // error_log("dlfldigi_call($key, $value1, $value2)");

  $xmlrpc_server = isset($dlfldigi_instances[$instance]) ? $dlfldigi_instances[$instance][0] : false;
  $xmlrpc_port = isset($dlfldigi_instances[$instance]) ? $dlfldigi_instances[$instance][1] : false;
  // error_log(print_r($dlfldigi_instances[$instance], true));
  if (!$xmlrpc_server || !$xmlrpc_port) {
    $err = "DL-XMLRPC: config for " . $instance . " not found!";
    error_log($err);
    return $err;
  }

  $c = new xmlrpc_client("/RPC2", $xmlrpc_server, $xmlrpc_port);
  if (isset($_GET['dbg'])) {
    $c->setDebug(1);
    echo htmlentities($message->serialize());
  }
  
  $response = &$c->send($message);
  if (!$response->faultCode()) {
    $v = $response->value();
    if (count($v->me)) {
     //echo "me=" . print_r($v->me, true);
      foreach ($v->me as $k => $item) {
         //echo "$k=" . print_r($item, true);
        // error_log($key . "=" . print_r($item, true));
        return $item;
      }
    }
  } else {
    $err = date("H:i:s") . " DL-XMLRPC: " . "Code: " . $response->faultCode() . " Reason: '" . htmlspecialchars($response->faultString()) . "'";
    if ($response->faultCode() == 5) {
      $err .= " (" . $xmlrpc_server . ":" . $xmlrpc_port . ")";
    }
    $err .= " (" . $key . "=" . $value1 . ")";
    error_log($err);
    if (isset($_GET['dbg']))
      echo $err . "<br/>";
    return $err . "\n";
  }
}


$xmlrpc_instance = isset($_GET['inst']) ? $_GET['inst'] : null;
$xmlrpc_key = isset($_GET['key']) ? $_GET['key'] : null;
$xmlrpc_value1 = isset($_GET['value1']) ? $_GET['value1'] : null;
$xmlrpc_value2 = isset($_GET['value2']) ? $_GET['value2'] : null;
//error_log("k=" . $xmlrpc_key . "; v=" . $xmlrpc_value1);

if ($xmlrpc_instance && $xmlrpc_key) {

  $result = array();
  $calls = array();
  if (strpos($xmlrpc_key, ",") === false) {
    $calls = array($xmlrpc_key);
  } else {
    $calls = explode(",", $xmlrpc_key);
  }

  // katrs atsevišķais pieprasījums
  // rezultātu liek masīvā
  foreach ($calls as $xmlrpc_key) {
    $res = dlfldigi_call($xmlrpc_instance, $xmlrpc_key, $xmlrpc_value1);
  
    if ($xmlrpc_key == "rx.get_data") 
      $res = base64_encode($res);
  
    if ($xmlrpc_key == "text.get_rx") {
      // vispirms uzzina, cik tur pavisam ir,
      // tad pieprasa pavisam-jau_saņemto garumu
      $rx_len = dlfldigi_call($xmlrpc_instance, "text.get_rx_length");
      $res = dlfldigi_call($xmlrpc_instance, $xmlrpc_key, $xmlrpc_value1, $rx_len-$xmlrpc_value1);
      $res = base64_encode(rawurlencode($res));
    }
    $result[$xmlrpc_key] = $res;
  }

  // error_log(print_r($result, true));
  $result = json_encode($result);
  // error_log(print_r($result, true));
  echo $result;

}

?>
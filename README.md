Simple dl-fldigi web interface to replace lagging VNC to control radio remotely

requirements
  local webserver with PHP support
  dl-fldigi

inspired by
https://github.com/jamescoxon/dl-fldigi-XMLRPC/

Best when dl-fldigi is paired with the radio using hamlib or rigcat and can control the radio.

mbp:~ x-f$ /Applications/My\ Apps/dl-fldigi.app/Contents/MacOS/dl-fldigi --xmlrpc-server-port 7362 --window-width 500

FIXME:
  Decoded text field isn't multiplayer-friendly – having multiple users will result in partly displayed text, because each user gets only fresh text fragment since the last query.


http://hail2u.net/pub/test/273.html
http://hail2u.net/pub/test/321.html
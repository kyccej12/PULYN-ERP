@ECHO OFF
START c:/wamp/www/tcpl/HL7Listener.exe -Port 5100 -FilePath c:/wamp/www/inbound/in -NoACK
EXIT 0
#!/bin/bash

cd /home/pi/sosa/access-control/

sleep 60
export DISPLAY=:0.0
{ xset s off && xset -dpms && xset s noblank && echo 'screensavar on'; } || { echo 'screensavar failed'; }

python3 dsp_access_control.py >>dspctl.log &
python3 control.py >>ctl.log &

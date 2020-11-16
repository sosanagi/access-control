# init

you might see next error
$ [main] no contactless reader available

you should execute next command if previous successful
$ sudo sh -c 'echo SUBSYSTEM==\"usb\", ACTION==\"add\", ATTRS{idVendor}==\"054c\", ATTRS{idProduct}==\"06c3\", GROUP=\"plugdev\" >> /etc/udev/rules.d/nfcdev.rules'
Insert and remove Felica USB device.


# use apache
files in dsp_web/ move to /var/www/html/

# cron
@reboot bash /home/pi/access-control/main.sh >>/home/pi/access-control/dsp.log 2>&1

# use
[nfcpy](https://github.com/nfcpy/nfcpy)
[jquerry.gantt](https://github.com/taitems/jQuery.Gantt#:~:text=jQuery%20Gantt%20Chart%20is%20a,different%20colours%20for%20each%20task)

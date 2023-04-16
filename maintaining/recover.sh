#!/bin/bash
sudo nano /etc/apt/sources.list
sudo apt-get update
USE="echo" sudo apt list --installed > installed.txt
python3 ./delstring.py
sudo nano ./toinstall.txt
USE="echo" xargs --verbose --interactive -a ./toinstall.txt sudo apt reinstall -y -V --install-suggests --fix-broken --fix-missing --allow-downgrades --show-progress > install.log



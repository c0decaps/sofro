# sofro 📻 🌐 
A web based soco frontend to store shortcuts for webradio streams for an easy sonos control.

As the soco python package is the only way that I could reliably control a sonos with via an API, it is what I used to command the sonos in the backend (see `controller.py`).
The stations, devices and the status of the devices are managed in a SQLite3 database, from which the frontend is generated from. 

### Functionalities
* Add/Remove stations
* Toggle play/stop
* Change volume

### Dependencies
* PHP
* SQLite3
* python3 with the `sqlite3` and `soco` packages

### todo
* Settings page
* Installer
* Automatic device discovery
* Change background s.t. it is not animated (?)

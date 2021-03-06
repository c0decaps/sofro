#!/usr/bin/python3

import soco, sys, sqlite3

try:
    sqliteConnection = sqlite3.connect('data/rad10.db')
    cursor = sqliteConnection.cursor()
    cursor.execute('select * from devices;')
    devices = cursor.fetchall()
    if len(devices) < 1:
        exit("no devices discovered")
    sonos = soco.SoCo(devices[0][1])

    if len(sys.argv) > 1:
        if(sys.argv[1].lower() == 'play'):
            print("playing...")
            sonos.play()
        elif(sys.argv[1].lower() == 'stop'):
            print("stopping...")
            sonos.stop()
        elif(sys.argv[1].lower() == 'disc'):
            print(list(soco.discover()))
        elif(sys.argv[1].lower() == 'url' and len(sys.argv) > 2):
            url = sys.argv[2]
            print("playing from url "+str(url))
            sonos.play_uri(url)
        elif(sys.argv[1].lower() == 'vol' and len(sys.argv) > 2):
            new_vol = int(sys.argv[2])
            if(isinstance(new_vol, int)):
                new_vol = int(new_vol)
                if(new_vol in range(0, 101)):
                    sonos.volume = new_vol
                else:
                    print("volume out of range (0...100)")
            else:
                print("volume is no integer")
        else:
            print("invalid command")
except sqlite3.Error as error:
    print("Error occurred: "+str(error))
finally:
    if sqliteConnection:
        sqliteConnection.close()
        print("SQLite connection closed")

# -*- coding: utf8 -*-
import tkinter as tk
from tkinter import ttk
from tkinter import font

from socket import socket, AF_INET, SOCK_STREAM
from time import *
from datetime import datetime
import threading


HOST = 'localhost'
PORT = 51000
MAX_MESSAGE = 2048
NUM_THREAD = 2

FONT_SIZE = 30

def main_proc():
    global top
    top = tk.Toplevel(root)
    top.deiconify()
    top.attributes("-fullscreen", True)

    set_frame()
    com_start()
    show_time()

def set_frame():
    global var_left,var_right,now_time

    main_frame = ttk.Frame(root,padding=5)
    main_frame.grid()

    var_left = tk.StringVar()
    var_right = tk.StringVar()
    now_occupancy_title = tk.StringVar()
    now_occupancy_title.set('在室')
    now_time = tk.StringVar()

    std_font = font.Font(family='Helvetica', size=FONT_SIZE, weight='bold')

    now_occupancy_label = tk.Label(main_frame,textvariable=now_occupancy_title,font=std_font)
    time_label = tk.Label(main_frame,textvariable=now_time,font=std_font)
    msgl_label = tk.Label(main_frame,textvariable=var_left,font=std_font,
        width=15,height=50,foreground="#ffffff",background='#000000')
    msgr_label = tk.Label(main_frame,textvariable=var_right,font=std_font,
        width=15,height=50,foreground="#ffffff",background='#000000')

    now_occupancy_label.grid(row=0,column=0,sticky=tk.NW)
    time_label.grid(row=0,column=1,sticky=tk.N)
    msgl_label.grid(row=1,column=0,sticky=tk.NW)
    msgr_label.grid(row=1,column=1,sticky=tk.NW)

def com_receive():

    sock = socket(AF_INET, SOCK_STREAM)
    try:
        sock.bind ((HOST, PORT))
    except:
        print("Error: PORT already in use. plz re-execute command")
        sleep(3)
        root.quit()
        sys.exit()

    sock.listen (NUM_THREAD)
    print ('receiver ready, NUM_THREAD = ' + str(NUM_THREAD))

    while True:
        try:
            conn,addr = sock.accept()
            mess = conn.recv(MAX_MESSAGE).decode('utf-8')
            conn.close()
            if "stop" in mess:
                break

            mess_list = mess.splitlines()

            message_left('\n'.join(mess_list[:5]))
            if(len(mess_list)>=5):
                message_right('\n'.join(mess_list[5:]))

        except:
            print('Error: socket message unreceived')

    sock.close()

def message_left(msg):
    var_left.set(msg)

def message_right(msg):
    var_right.set(msg)

def show_time():
     now_time.set(strftime('%m/%d %H:%M:%S '))
     root.after(1000, show_time)

def com_start():
    th=threading.Thread(target=com_receive)
    th.start()


def main():
    global root
    root = tk.Tk()
    root.after(0, main_proc)
    root.mainloop()

if __name__ == '__main__':
    main()

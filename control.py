# -*- coding: utf-8 -*-

import time
import os
import sys
import subprocess
import re
import sqlite3
import datetime
import socket
import json

sys.path.append(os.path.join(os.path.dirname(__file__), './nfcpy/examples'))

import sesame

f = open("module.json", "r")
module = json.load(f)
sql_list = module["sql"]
msg_list = module["message"]

HOST        = 'localhost'
PORT        = 51000
DB_PATH     = '/var/www/html/ACCESS_MANAGE.db'

WAIT_TIME = 2 # n>0
MIN_WAIT_TIME = 1

def com_send(mess):
    while True:
        try:
            # 通信の確立
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.connect((HOST, PORT))

            # メッセージ送信
            sock.send(mess.encode('utf-8'))

            # 通信の終了
            sock.close()
            break

        except:
            print ('Error retry: ' + mess)
            break

def run_and_capture(cmd):
    '''
    :param cmd: str 実行するコマンド.
    :rtype: str
    :return: 標準出力.
    '''
    # ここでプロセスが (非同期に) 開始する.
    proc = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    buf = []

    while True:
        # バッファから1行読み込む.
        line = proc.stdout.readline()
        # print(line)
        buf.append(line.strip())

        #sys.stdout.write(line.decode('utf-8'))

        # バッファが空 + プロセス終了.
        if not line and proc.poll() is not None:
            break

    return str(buf)

def now_occupancy():
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()

    c.execute(sql_list["slt_now_occupancy"])

    login_list2 = c.fetchall()
    login_menbers=[]
    for inlist in login_list2:
        if inlist[1]=='':
            if inlist[2] in '研究室':
                str_inlist2=''
            else:
                str_inlist2=inlist[2]
            tmplist=inlist[0]+" "+str_inlist2
            login_menbers.append(tmplist)
            print(tmplist)
    else:
        if login_menbers:
            com_send('\n'.join(login_menbers))
        else:
            sesame.sesame_control(True)
            com_send(msg_list["nothing_login_members"])

    if len(login_menbers)==1:
        sesame.sesame_control(False)


    con.commit()
    con.close()

def ID_read():
    msg = run_and_capture("python3 ./nfcpy/examples/tagtool.py")
    ID = re.findall('ID=(.*) P',msg)
    # print(ID)

    return ''.join(ID)

def ID_master(ID,flag=False):
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()

    c.execute(sql_list["slt_ID_master"],{'card_ID': ID})
    card_list = c.fetchall()

    if not flag:
        if card_list:
            subscribe()
            con.close()
            return False
    else:
        if card_list:
            com_send(msg_list["no_master_key"])
            time.sleep(WAIT_TIME)
            con.close()
            return False

    return True


def ID_search(ID,flag=False):
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()

    c.execute(sql_list["slt_ID_search"],{'card_ID': ID})
    card_list = c.fetchall()

    if not card_list:
        if not flag:
            com_send(msg_list["not_registration_card"])
            time.sleep(WAIT_TIME)
            con.close()
            return False

    else:
        if flag:
            com_send(msg_list["registration_card"])
            time.sleep(WAIT_TIME)
            con.close()
            return False

    return True

def subscribe():
    print('------------------')
    com_send(msg_list["new_registration_card"])
    #print('登録者名')
    #user_name=input()
    user_name=''
    #print(user_name)
    place_ID=0

    ID=ID_read()
    if not ID_master(ID,True):
        return True
    if not ID_search(ID,True):
        return True
    # print(type(ID))
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()

    c.execute(sql_list["ins_subscribe_user"],{'user': user_name,'card_ID': ID})

    con.commit()

    c.execute(sql_list["ins_subscribe_place"],{'card_ID': ID,'place_ID': place_ID})

    con.commit()
    con.close()

    com_send(msg_list["1"])
    time.sleep(0.5)
    com_send(msg_list["2"])
    time.sleep(0.5)
    com_send(msg_list["3"])
    time.sleep(0.5)
    com_send(msg_list["poof"])
    time.sleep(WAIT_TIME)
    com_send(msg_list["next_rgst_stage"])
    time.sleep(WAIT_TIME)

def login():
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()
    ID=ID_read()
    if not ID_master(ID):
        con.close()
        return True
    if not ID_search(ID):
        con.close()
        return True
    print(datetime.datetime.now())

    c.execute(sql_list["slt_login"],{'card_ID': ID})
    login_list1 = c.fetchall()
    if login_list1:
        com_send(msg_list["processed"])
        con.commit()
        con.close()
        time.sleep(MIN_WAIT_TIME)
        logout(ID)
        return 0

    #user_place初期化部
    c.execute(sql_list["ins_login"],{'card_ID': ID, 'login': datetime.datetime.now() ,'logout': ''})

    #login処理部
    c.execute(sql_list["upd_login"],{'card_ID': ID})
    com_send(msg_list["login"])
    con.commit()
    con.close()

    time.sleep(MIN_WAIT_TIME)



def logout(ID):
    con = sqlite3.connect(DB_PATH)
    c = con.cursor()
    # ID=ID_read()
    # ID_search(ID)
    #print(datetime.datetime.now())

    #logout処理部

    c.execute(sql_list["slt_logout"],{'card_ID': ID})
    login_list1 = c.fetchall()
    print(login_list1)
    if not login_list1:
        com_send(msg_list["login_unregistered"])
        # print("出勤未登録")
    else:
        for list in login_list1:
            c.execute(sql_list["upd_logout"],{'logout': datetime.datetime.now(),'login': list[1]})


    con.commit()
    con.close()
    com_send(msg_list["logout"])
    time.sleep(MIN_WAIT_TIME)

    now_occupancy()

if __name__ == '__main__':
    while True:
        now_occupancy()
        login()

    #subscribe()
    #ID=ID_read()
    #ID_search(ID)
    #logout()
    # now_occupancy()

#!/usr/bin/env python
# -*- coding: utf-8 -*-
import RPi.GPIO as GPIO
import configparser
import time

DELAY_TIME = 4

config = configparser.RawConfigParser()            #воспользуемся конфигом
config.read("/var/www/barrier/web/assets/global_config.conf")         #считаем конфиг
pin_number_3 = config.getint("relay_pins", "relay3") #пина из конфига присвоем переменной pin_number

print ("ON pin:"+str(pin_number_3)+"\n")

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(pin_number_3, GPIO.OUT)   #устанавливаем пин на выходной сигнал
GPIO.output(pin_number_3, GPIO.LOW)  #ставим логический ноль на выходе

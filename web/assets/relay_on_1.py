#!/usr/bin/env python
# -*- coding: utf-8 -*-
import RPi.GPIO as GPIO
import configparser
import time

DELAY_TIME = 4

config = configparser.RawConfigParser()            #воспользуемся конфигом
config.read("/var/www/html/barrier/web/assets/global_config.conf")         #считаем конфиг
pin_number_1 = config.getint("relay_pins", "relay1") #пина из конфига присвоем переменной pin_number

print ("ON pin:"+str(pin_number_1)+"\n")

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)            
GPIO.setup(pin_number_1, GPIO.OUT)   #устанавливаем пин на выходной сигнал
GPIO.output(pin_number_1, GPIO.LOW)  #ставим логический ноль на выходе

time.sleep(DELAY_TIME)

print ("OFF pin:"+str(pin_number_1)+"\n")

GPIO.output(pin_number_1, GPIO.HIGH) #ставим логическую еденицу на выходе
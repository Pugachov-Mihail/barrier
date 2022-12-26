from gpiozero import DigitalOutputDevice
import time

rele = DigitalOutputDevice(27)
A = True

def main():
    global A
    rele.on()
    time.sleep(1)
    A = False



if __name__ in "__main__":
    while A:
        main()
       

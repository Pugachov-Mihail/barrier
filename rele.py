from gpiozero import OutputDevice
import time

def main():
    rele = OutputDevice(4)
    rele.on()
    time.sleep(1)
    rele.close()
    time.sleep(1)



if __name__ in "__main__":
    main()
       

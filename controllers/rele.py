from gpiozero import OutputDevice
import time

def main():
    rele = OutputDevice(4)
    print("Hi")
    rele.on()
    time.sleep(1)
    rele.close()
    time.sleep(1)



if __name__ in "__main__":
    main()
       

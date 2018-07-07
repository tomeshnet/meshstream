# Compiling the LibeMesh or LEDE

Compile requires a special SDK and specific paths to be observed.

## Prepare the build

```
wget http://blog.altermundi.net/media/uploads/lime-sdk-LR.tar.bz
mkdir -p  /home/nicolas/OS_projects/lime-sdk-LR
cd /home/nicolas/OS_projects
wget http://blog.altermundi.net/media/uploads/lime-sdk-LR.tar.bz
tar xvf lime-sdk-LR.tar.bz
cd lime-sdk-LR
./cooker --update-communities --update-feeds
./cooker -b ar71xx/generic
```

## Compile LibreMesh
Build image using
```
./cooker -c ar71xx/generic --flavor=lime_default --profile=librerouter-v1
```

## Compile LEDE 

`dnsmasq` will not build.  Edit `flavours.conf` and change the `led_vanilla` line to 
```
lede_vanilla="-dnsmasq"
```

Build image using
```
./cooker -c ar71xx/generic --flavor=LEDE-vanilla --profile=librerouter-v1
```
## Output

The files will be placed in `/home/nicolas/OS_projects/lime-sdk-LR/output/ar71xx/generic/librerouter-v1`. There will be a folder for each "flavour".

The out put folde will have these files of interest

|File endin in                                         | Function                |
|:-----------------------------------------------------|:------------------------|
|ar71xx-generic-librerouter-v1-squashfs-sysupgrade.bin | Bin used for sysupgrade |
|ar71xx-generic-root.squashfs                          | Root file system        |
|ar71xx-generic-uImage-lzma.bin                        | Kernel                  |

# Flashing

## First flash from factory

To be able to boot the image successfully you will need to change a paramter in U-Boot. From factory bootargs are set to `bootargs=console=ttyS0,115200 root=31:02 rootfstype=squashfs init=/sbin/init mtdparts=ath-nor0:256k(u-boot),64k(u-boot-env),6336k(rootfs),1408k(uImage),8256k(mib0),64k(ART)`

To set them correctly enter the following at the U-Boot `ath>` prompt
** See U-Boot section below for more information**

```
setenv bootargs "board=LIBREROUTERV1 console=ttyS0,115200"
savenev
```


## Sysupgrade
Currently unable to flash stabley

## U-Boot

This method seems to create a stable flash. It requires you to flash two seperate sections, the Kernel and the rootfs. 

To use this method you must first prepare a computer/device that will hold the required files. Configure the computer as follows

* Configure the IP Address to be 192.168.1.10
* Install a tftpd server (windows [tftpd32](http://tftpd32.jounin.net/), linux tftpd-ha, etc)
* Place the required files in the root of the tftpd server
* Connect to the router via ethernet (Can be direct of via a swtich)

## Flash rootfs
```
tftp 0x80060000 openwrt-lime-default-ar71xx-generic-root.squashfs
erase 0x9f050000 +0xE30000
cp.b 0x80060000 0x9f050000 $filesize
```

## Flash kernel
```
tftp 0x80060000 openwrt-lime-default-ar71xx-generic-uImage-lzma.bin
erase 0x9fE80000 +0x170000
cp.b 0x80060000 0x9fE80000 $filesize
```

## Boot

To reboot from U-Boot enter
```
reset
```

Or you can just boot using

```
boot
```

## Flashing - Explentation

Flashing is done with these 3 commands

`tftp <destination address> <filename>`

tftp downloads a file from a predefined tftp server and writes it to a memory address

`destination address` is where you wish to write the file.  For our purposes we use `0x80060000` which is a location in RAM.

`filename` is the file name on the tftp server you wish to get

`erase <destination address> +<length>`

erase will erase information at a specific memory address for a specific length.

`destination address` is the start of the memory we wish to erase

`length` is the amount of memory to erase

`cp.b <from address> <destination address> <length>`

cp.b is a binary copy from one memory location to another

`from address` is where the data will be copied from. We use `0x80060000` which is where we stored the file from tftpd

`length` is the size of the file. We use a variable that comes define `$filesize`


# General Notes

* `eth0` does not seem to work. It may be connected directly to a WAN port that does not seem to be on the board. You want to do everything on `eth0`
* Do not cold boot with serial connected. It will not work
* 2.4 ghz is broken. Seems to work ok for a very local access poing but thats about it.

# OpenWRT/LEDE


Device will boot with the default IP address of 192.168.1.1


## Connect device to internet 

### CLI

Set an ip address on your lan using
``
ifconfig br-lan 192.168.x.x 
```

Set the gateway of your lan using
```
route add -net 0.0.0.0 gw 192.168.x.x
```

Set the DNS server of your lan using
```
echo nameserver 8.8.8.8 > /etc/resolv.conf
```

### via config

Edit the  `/etc/config/network` file. Under `lan` interface

Set your `ipaddr`  and add 
```
option gateway '192.168.40.1
option dns '8.8.8.8'
`

Restart lan by using

```
ifdown lan
ifup lan
```

## Installing LuCI

```
opkg update
opkg install luci
```

Reboot to take effect.

# LibreMesh

Default address is 10.13.0.1. Connect to the WiFi is the easiest way to access.

LuCI is pritty borken on LibreMesh


# U-Boot

## Serial Interface
To access U-Boot you need to use a TTL serial adapter running at 3.3v. This is connected to Header labeled J4 on the board.

```
Header J4
1 - VCC (optional)
2 - RX (TX on your dongle)
3 - TX (RX on your dongle)
4 - Ground
```
Set your serial port to baud 115200

** Note: You cannot have the dongle connected when powering the LibreRouter on. You must disconnected for the first few seconds of cold boot **

## Entering U-Boot

Durring the boot process you will be notified to press any key. This delay is usualy 3 second.  Simply press any key.  If the boot failes it will automatically go into U-Boot.

## Entering commands

Few things to note when entering U-Boot commands
* Enter command exacly. You risk Bricking the board (making it impossible, or nearly impossible, to recover the device)
* U-Boot is very sensative, an extra leading whitespace will make a command not work.
* Do not copy and paste multiple lines, it will not work. One line at a time.

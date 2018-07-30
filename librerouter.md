Configurations for the LibreRouter v1
=====================================

## Compile LibreMesh or LEDE

Compile requires a special SDK and specific paths to be observed.

### Prepare the build

```
apt-get install subversion
mkdir -p  /home/nicolas/OS_projects/lime-sdk-LR
cd /home/nicolas/OS_projects
wget http://blog.altermundi.net/media/uploads/lime-sdk-LR.tar.bz
tar xvf lime-sdk-LR.tar.bz
cd lime-sdk-LR
rm -rf communities
./cooker --update-communities --update-feeds
./cooker -b ar71xx/generic
```

### Compile LibreMesh

Build image using:

```
./cooker -c ar71xx/generic --flavor=lime_default --profile=librerouter-v1
```

### Compile LEDE

`dnsmasq` will not build. Edit `flavours.conf` and change the `led_vanilla` line to:

```
lede_vanilla="-dnsmasq"
```

Build image using:

```
./cooker -c ar71xx/generic --flavor=lede_vanilla --profile=librerouter-v1
```

Compile with env variables `J=1 V=s` for debugging info.

### Outputs

The files will be placed in `/home/nicolas/OS_projects/lime-sdk-LR/output/ar71xx/generic/librerouter-v1`. There will be a folder for each "flavour".

The output folder will have these files of interest:

| File ending in                                        | Function                |
|:------------------------------------------------------|:------------------------|
| ar71xx-generic-librerouter-v1-squashfs-sysupgrade.bin | Bin used for sysupgrade |
| ar71xx-generic-root.squashfs                          | Root file system        |
| ar71xx-generic-uImage-lzma.bin                        | Kernel                  |

## Flash

### First flash from factory

To be able to boot the image successfully you will need to change a parameter in U-Boot. From factory, `bootargs` are set to `bootargs=console=ttyS0,115200 root=31:02 rootfstype=squashfs init=/sbin/init mtdparts=ath-nor0:256k(u-boot),64k(u-boot-env),6336k(rootfs),1408k(uImage),8256k(mib0),64k(ART)`.

To set this paramater you will need to  enter the following at the U-Boot `ath>` prompt (see U-Boot section below for more information):

```
setenv bootargs "board=LIBREROUTERV1 console=ttyS0,115200"
saveenv
```

### Flash using sysupgrade

Currently unable to flash with sysupgrade that will work beyond a single reboot.

`sysupgrade img.bin`

or

`sysupgrade -F img.bin`

### Flash using U-Boot

This method seems to create a stable flash. It requires you to flash two separate sections, the kernel and the rootfs.

To use this method you must first prepare a computer/device that will hold the required files. Configure the computer as follows:

* Configure the IP Address to be `192.168.1.10`
* Install a tftpd server ([tftpd32](http://tftpd32.jounin.net/) for Windows, `tftpd-hpa` for Linux)
* Place the required files in the root of the tftpd server
* Connect to the LibreRouter via ethernet (can be direct of via a network switch)

#### Flash rootfs

```
tftp 0x80060000 openwrt-lime-default-ar71xx-generic-root.squashfs
erase 0x9f050000 +0xE30000
cp.b 0x80060000 0x9f050000 $filesize
```

#### Flash kernel

```
tftp 0x80060000 openwrt-lime-default-ar71xx-generic-uImage-lzma.bin
erase 0x9fE80000 +0x170000
cp.b 0x80060000 0x9fE80000 $filesize
```

### Boot

To reboot from U-Boot enter `reset`, or you can just boot using `boot`.

### Explanations

Flashing is done with these 3 commands:

* `tftp <destination address> <filename>`
  * `tftp` downloads a file from a predefined tftp server and writes it to a memory address
  * `<destination address>` is where you wish to write the file (we use `0x80060000` which is a location in RAM)
  * `<filename>` is the file name on the tftp server you wish to get

* `erase <destination address> +<length>`
  * `erase` will erase information at a specific memory address for a specific length
  * `<destination address>` is the start of the memory we wish to erase
  * `<length>` is the amount of memory to erase

* `cp.b <from address> <destination address> <length>`
  * `cp.b` is a binary copy from one memory location to another
  * `<from address>` is where the data will be copied from (we use `0x80060000` which is where we stored the file from tftpd)
  * `<length>` is the size of the file (we use a predefined variable `$filesize`)

## General notes

### Known issues

* `eth0` does not seem to work. It may be connected directly to a WAN port that does not seem to be on the board. You want to do everything on `eth1`
* Do not cold boot with serial connected. It will not work
* The 2.4 GHz radio is broken. Seems to work only for a very local access point

### Fix fake MAC addresses

Run the following in a console to generate new MAC addresses for all wireless and physical interfaces:

```
rand4bytes=$(head -c 256 /dev/urandom | md5sum | sed 's/\(..\)/\1:/g' | head -c 11)

uci set network.lan.macaddr="02:$rand4bytes":10
uci commit network

for index in 0 1 2; do
uci set wireless.radio$index.macaddr="02:$rand4bytes":5$index
done
```

### OpenWRT/LEDE

Device will boot with the default IP address of `192.168.1.1`.

Full config script found here: https://github.com/tomeshnet/meshstream/issues/9

### Connect device to Internet

#### Connect using commands

Set an IP address on your LAN:

```
ifconfig br-lan 192.168.x.x
```

Set the gateway of your LAN:

```
route add -net 0.0.0.0 gw 192.168.x.x
```

Set the DNS server of your LAN:

```
echo nameserver 8.8.8.8 > /etc/resolv.conf
```

#### Connect using config

Edit the `/etc/config/network` file and under `lan` interface:

Set your `ipaddr` and add:

```
option gateway '192.168.40.1
option dns '8.8.8.8'
```

Restart `lan`:

```
ifdown lan
ifup lan
```

### Install LuCI

```
opkg update
opkg install luci
```

Reboot to take effect.

### LibreMesh

Default address is 10.13.0.1. Connecting to the WiFi is the easiest way to access.

LuCI is pretty broken on LibreMesh.

### U-Boot

#### Serial Interface

To access U-Boot you need to use a TTL serial adapter running at 3.3v. This is connected to the header labeled `J4` on the board.

**Header J4:**

```
1 - VCC (optional)
2 - RX (TX on your dongle)
3 - TX (RX on your dongle)
4 - Ground
```

Set your serial port to baud rate of `115200`.

![Serial connection on LibreRouter v1](librerouter.png?raw=true)

**Note:** You cannot have the dongle connected when powering up the LibreRouter. You must disconnected for the first few seconds from cold boot.

#### Entering U-Boot

During the boot process you will be notified to press any key. This delay is usually 3 seconds. Simply press any key. If the boot failed it will automatically go into U-Boot.

#### Entering commands

Few things to note when entering U-Boot commands:

* Enter commands exactly. You risk bricking the board (making it impossible, or nearly impossible, to recover the device)
* U-Boot is very sensitive, an extra leading whitespace will make a command fail
* Do not copy and paste multiple lines, it will not work. One line at a time

# Appendix

Instructions that are supposed to work

Update U-Boot (seems to work ok)

```
tftp 82000000 openwrt-blah-uboot-bin
erase 1:0-4
cp.b 0x82000000 0x9f000000 0x30000
```

Flash sysupgrade (may be working needs confirmation)
```
tftp 82000000 openwrt-blah-sysupgrade.bin
erase 0x9f050000 +$filesize
cp.b 0x82000000 0x9f050000 $filesize
```

Configurations for the Raspberry Pi 3B
======================================

There are two Raspberry Pis configured slightly differently, one is streaming a video from the camera and the other one is playing the video off of an embedded player in a SSB feed. Let's call them `Streamer Pi` (the one with the Raspberry Pi camera) and `Subscriber Pi`. The steps are almost identical for both:

1. Flash Raspbian

1. Install [prototype](https://github.com/tomeshnet/prototype-cjdns-pi) at commit `9cc421b9e8c9f4a7340e7a4c85208de6a251c18c` (which at the time of writing is identical as `develop`) with the following features enabled:

        WITH_WIFI_AP
        WITH_IPFS
        WITH_IPFS_PI_STREAM (for Streamer Pi only)
        WITH_SSB
        WITH_SSB_WEB
        WITH_EXTRA_TOOLS

1. Run `ipfs id` on one Pi and on the other node, use `ipfs bootstrap add` to add its cjdns-transported IPFS address so they will peer with one another

1. Assign IPv4 addresses to the `eth0` interface of each Pi by adding to `/etc/network/interfaces` with the following, with a different value of `x` for each Pi:

        allow-hotplug eth0
        iface eth0 inet static
            address 192.168.10.x
            netmask 255.255.255.0
            network 192.168.10.0
            broadcast 192.168.10.255

   This allows for SSB discovery because at the time of writing, discovery over IPv6 is only being worked on in sbot

1. Replace `PI_ROOT/var/www/sbot` with `REPO_ROOT/src/sbot` from this repository to get fixes that still need to be upstreamed

1. Change `M3U8_SIZE` to a smaller value in `/usr/bin/process-stream.sh` such as `2` for less latency in the live stream

1. Occassionally it is necessary to check `df` for space and `ipfs repo gc` to prevent using up all the SD card space

## Install ipfs-live-streaming player UI (optional)

1. Download the [ipfs-live-streaming video player](https://github.com/tomeshnet/ipfs-live-streaming/tree/enable-ipns/terraform/shared/video-player) to `PI_ROOT/var/www/video-player`

1. Create `/etc/nginx/site-path-enabled/ipfs-live-streaming.conf` with:

        location /ipfs-live-streaming {
          alias /var/www/video-player;
          index index.html index.htm index.nginx-debian.html index.php;
          try_files $uri $uri/ =404;
          location ~ \.php$ {
            try_files $uri =404;
            fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME /var/www/$fastcgi_script_name;
            include fastcgi_params;
          }
        }

1. Edit `/var/www/video-player/js/common.js` where `<IPNS_ID>` is that of Streamer Pi:

        var ipfs_gateway_self = 'http://10.0.0.1'; // IPFS gateway of this node
        var ipfs_gateway_origin = 'http://10.0.0.1'; // IPFS gateway of origin stream
        //var m3u8_ipfs = 'live.m3u8'; // File path to m3u8 with IPFS content via HTTP server
        var m3u8_ipfs = 'http://10.0.0.1/ipns/<IPNS_ID>'; // URL to m3u8 via IPNS (uncomment to enable)
        var m3u8_http_urls = []; // Optional list of URLs to m3u8 over HTTP

1. Restart nginx
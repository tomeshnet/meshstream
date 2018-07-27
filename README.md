Meshstream
==========

The purpose is to show what can be accomplished with peer-to-peer applications running over a wireless mesh network that is completely isolated from the Internet.

Meshstream demonstrates:

* Live video streaming over content addressable storage (IPFS)
* Sharing of multimedia content over a peer-to-peer social network (SSB)
* Mesh networking over long-range wireless links using open hardware (LibreRouter)

Each physical node consists of a LibreRouter + a Raspberry Pi, running software developed by Toronto Mesh that use IPFS and SSB. One node will stream video off of a Raspberry Pi camera, publishes to the private IPFS and SSB network formed by these devices, then other nodes can view the embedded player on the SSB timeline of the video publisher. The user experience is similar to streaming a YouTube video and sharing the link on your Facebook, then your friends discover that video via their social feed and view the live stream from the embedded player.

## Set up Meshstream

The current iteration is prepared for [Decentralized Web Summit 2018](https://decentralizedweb.net). Things are pieced together in a short time with limited access to hardware, so set up instructions may be incomplete and the software is quite hacky. However, browsing through the instructions will give you a good idea of how the all the pieces fit together.

### Hardware

* _Two_ [LibreRouter](https://github.com/libremesh/librerouter) v1 prototype boards each with _one_ [HPM5G radio assembly](https://github.com/tomeshnet/documents/blob/master/technical/20180530_hpm5g-radio-tests.md)
* _Two_ Raspberry Pi 3B devices
* _One_ Raspberry Pi camera
* Ethernet cables and power

### Software

Follow [Configurations for the LibreRouter v1](librerouter.md) to configure each LibreRouter.

Follow [Configurations for the Raspberry Pi 3B](raspberry-pi-3b.md) to configure each Raspberry Pi.
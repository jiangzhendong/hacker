title: Get Started with Docker 1.12 on Raspberry Pi 
date: 2017-01-05 09:53:29
categories: 技术
tags: docker
---
I have put this guide together to help you get started with Docker 1.12 on your Raspberry Pi. For simplicity we will use the default operating system for the Pi - called Raspbian. This guide currently works with most models of Raspberry Pi and I'd recommending using the Model B 2/3 or Zero.

![](/img/docker/1.jpg)
Raspberry Pi cluster

Raspberry Pi Zero Cluster

Don't know what Docker is yet or what you could use it for? Check out [Understanding Docker](https://docs.docker.com/engine/understanding-docker/) from the offical docs.

## Already using Docker on your Pi?
Head straight over to my Live Swarm Deep Dive video where we connect multiple Raspberry Pis to together to create a Docker Swarm.
<!-- more -->
## Preparing the SD card
Download the latest Raspbian Jessie Lite image from https://www.raspberrypi.org/downloads/raspbian/.

`curl -O https://downloads.raspberrypi.org/raspbian_latest`

The Raspbian Jessie Lite image is the same as the regular image but only contains the bare minimum amount of packages.

Now if you are on Linux or Mac use the dd tool to write the image to an SD card.

Linux:

Use lsblk to list all bulk storage devices. Carefully pick the correct disk and replace /dev/sde with the destination in the following command:
```js
$ sudo dd if=raspbian_latest of=/dev/sde bs=1m
```
Mac:

Check which disk you need with: sudo diskutil list
```js
$ sudo dd if=raspbian_latest of=/dev/rdisk2 BS=1m
```
For Windows use a tool such as Win32DiskImager.

Install Docker
Once you boot up the Raspberry Pi you will be able to locate it on your network through the bonjour/avahi service.

Connect with SSH
```js
$ ssh pi@raspberrypi.local
```
The password is raspberry.

For security reasons it is advisable to change the password of the user pi with the passwd command.

Optional customizations
At this point you may want to edit the hostname of the Pi. Use an editor and change the word raspberrypi to something else in:
```js
/etc/hosts
/etc/hostname
```
If you are using the Pi for a headless application then you can reduce the memory split between the GPU and the rest of the system down to 16mb.

Edit `/boot/config.txt` and add this line:

`gpu_mem=16  `
Start the Docker installer
An automated script maintained by the Docker project will create a systemd service file and copy the relevant Docker binaries into /usr/bin/.

`$ curl -sSL get.docker.com | sh`
Until recently installing Docker on a Pi was a very manual process which often meant having to build Docker from scratch on a very underpowered device (this could take hours). Lots of hard work by ARM enthusiasts Hypriot has helped make .deb packages a first-class citizen in Docker's own CI process.

If you would like to opt into using a testing version then replace get.docker.com with test.docker.com. This will bring down a newer version but it may still have some open issues associated with it.

Beware that this work does not extend to non-Debian distributions such as Arch Linux or Fedora. Arch Linux for ARM currently has Docker 1.11 available in its package manager pacman.

Configure Docker
There are a couple of manual steps needed to get the best experience.

Set Docker to auto-start
`$ sudo systemctl enable docker`
You can now reboot the Pi, or start the Docker daemon with:

`$ sudo systemctl start docker`
Enable Docker client
The Docker client can only be used by root or members of the docker group. Add pi or your equivalent user to the docker group:

`$ sudo usermod -aG docker pi`
After making this change, log out and reconnect with ssh.

Using Docker
Support for ARM and the Raspberry Pi is a work-in-progress item which means there are a few things you should know.

Pulling images from the Hub
If you pull down the busybox image from the Docker hub it will not function correctly. This is because the contents are designed for a regular PC or the x86_64 architecture. There are on-going efforts to fix this in future versions of Docker.

We should only attempt to use images which we know are designed to work on ARM. At present there are no strictly official images but the Docker team maintains a number of experimental images under the prefix 
armfh.

armhf refers to the way the code on the Raspberry Pi is ported - hf means hard float.

An image made by Resin.io is used by the current Docker build process to create a base image which is usable on all supported versions of the Raspberry Pi. It is a light-weight version of Raspbian Jessie which makes it a good choice.

Running your first ARM image
```js
$ docker run -ti resin/rpi-raspbian:jessie-20160831 /bin/bash

root@0c68918c47d3:/# cat /etc/issue  
Raspbian GNU/Linux 8 \n \l

root@0c68918c47d3:/# exit  
Try the image and type exit when you are finished.
```
Build a new image
Build your image like a regular Docker image but use resin/rpi-raspbian as your base. Let's add curl and ca-certificates and create an image which will access docker.com.
```js
FROM resin/rpi-raspbian:jessie-20160831  
RUN apt-get update && \  
    apt-get -qy install curl ca-certificates
CMD ["curl", "https://docker.com"]  
Create a Node.js application
This Dockerfile uses Resin.io's Raspbian image as a base and pulls down Node 4.5 LTS from the official Node.js site.

FROM resin/rpi-raspbian:jessie-20160831  
RUN apt-get update && \  
    apt-get -qy install curl \
                build-essential python \
                ca-certificates
WORKDIR /root/  
RUN curl -O \  
  https://nodejs.org/dist/v4.5.0/node-v4.5.0-linux-armv6l.tar.gz
RUN tar -xvf node-*.tar.gz -C /usr/local \  
  --strip-components=1

CMD ["node"]  
To build and run the image:

$ docker build -t node:arm .
$ docker run -ti node:arm
>
You can now test out a few Node.js instructions or fork my simple hello-world microservice on Github to take it further.

> process.version
'v4.5.0'  
> var fs = require('fs');
> console.log(fs.readFileSync("/etc/hosts", "utf8"));
> process.exit()
$ 
```
Make use of GPIO
In order to make use of additional hardware and add-on boards you will need to access the GPIO pins. These require an additional flag at runtime of --privileged to allow the container to write to the special area of memory managing GPIO.

The standard RPi.GPIO library will work through Docker including the libraries from several hardware manufactures.

This is a sample Dockerfile for using the defacto RPi.GPIO library.
```js
FROM resin/rpi-raspbian:jessie-20160831

RUN apt-get -q update && \  
    apt-get -qy install \
        python python-pip \
        python-dev python-pip gcc make  
RUN pip install rpi.gpio  
Build this image as a basis for adding your GPIO scripts at a later date.
```
`$ docker build -t gpio-base .`
The following Python code can be used to will blink an LED connected to GPIO pin 18.
```js
import RPi.GPIO as GPIO  
import time  
GPIO.setmode(GPIO.BCM)  
led_pin = 17  
GPIO.setup(led_pin, GPIO.OUT)

while(True):  
    GPIO.output(led_pin, GPIO.HIGH)
    time.sleep(1)
    GPIO.output(led_pin, GPIO.LOW)
    time.sleep(1)
app.py

```

Just use ADD to transfer the script into a new image depriving from gpio-base:
```js
FROM gpio-base:latest  
ADD ./app.py ./app.py

CMD ["python", "app.py"]  
```
You will need to run this container in --privileged mode in order to be able to access the GPIO pins.
```js
$ docker build -t blink .
$ docker run -ti --privileged blink
```
If you enjoyed that then how about making an Internet-enabled buzzer or cheerlight? Here's an example of using the Python Flask library to create a web server that controls a GPIO pin:

Github: GPIO web server in Docker

For more on interacting with GPIO head over to the Raspberry Pi Foundation's Getting Started with Physical Computing worksheet.

Feedback & questions
Contact me via Twitter @alexellisuk or in the comments section below. What do you want to see on the blog next? Have you tried it out? Let us know!

Resin.io recently changed their base image so instead of using resin/rpi-raspbian:jessie they are suggesting we use resin/rpi-raspbian:jessie-20160831.

See also:
Star or fork my docker-arm repo on Github for a list of useful Dockerfiles and information on Docker Swarm.

Build your Pi Zero Swarm with OTG networking only - look ma, no Ethernet!

Hosting series - How I self-host this blog on a Pi


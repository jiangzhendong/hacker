title: 'dpkg: warning: files list file for package'
date: 2016-12-21 16:53:29
categories: 技术
tags: [dpkg,apt,linux]
---
dpkg: warning: files list file for package `*****' missing, assuming package has no files currently installed解决办法
<!-- more -->

```js
#!/bin/bash
set -e

# Clean out /var/cache/apt/archives
apt-get clean
# Fill it with all the .debs we need
apt-get --reinstall -dy install $(dpkg --get-selections | grep '[[:space:]]install' | cut -f1)

DIR=$(mktemp -d -t info-XXXXXX)
for deb in /var/cache/apt/archives/*.deb
do
    # Move to working directory
    cd "$DIR"
    # Create DEBIAN directory
    mkdir -p DEBIAN
    # Extract control files
    dpkg-deb -e "$deb"
    # Extract file list, fixing up the leading ./ and turning / into /.
    dpkg-deb -c "$deb" | awk '{print $NF}' | cut -c2- | sed -e 's/^\/$/\/./' > DEBIAN/list
    # Figure out binary package name
    DEB=$(basename "$deb" | cut -d_ -f1)
    # Copy each control file into place
    cd DEBIAN
    for file in *
    do
        cp -a "$file" /var/lib/dpkg/info/"$DEB"."$file"
    done
    # Clean up
    cd ..
    rm -rf DEBIAN
done
rmdir "$DIR"
```
在 Ubuntu 执行 sudo apt-get upgrade 时，出现了如下的报错：
```js
Setting up bluez (4.101-0ubuntu13.1) ...
reload: Job is not running: dbus
invoke-rc.d: initscript dbus, action "force-reload" failed.
start: Job failed to start
invoke-rc.d: initscript bluetooth, action "start" failed.
dpkg: error processing package bluez (--configure):
 subprocess installed post-installation script returned error exit status 1
dpkg: dependency problems prevent configuration of bluez-alsa:i386:
 bluez-alsa:i386 depends on bluez; however:
  Package bluez is not configured yet.

dpkg: error processing package bluez-alsa:i386 (--configure):
 dependency problems - leaving unconfigured
No apport report written because the error message indicates its a followup error from a previous failure.
                          Errors were encountered while processing:
 bluez
 bluez-alsa:i386
E: Sub-process /usr/bin/dpkg returned an error code (1)
```
通过执行下面的命令可以解决该问题：

```js
$ sudo mv /var/lib/dpkg/info/ /var/lib/dpkg/info_old/
$ sudo mkdir /var/lib/dpkg/info/
$ sudo apt-get update
...
$ sudo apt-get -f install
Reading package lists... Done
Building dependency tree
Reading state information... Done
0 upgraded, 0 newly installed, 0 to remove and 0 not upgraded.
7 not fully installed or removed.
After this operation, 0 B of additional disk space will be used.
Setting up bluez (4.101-0ubuntu13.1) ...
Setting up blueman (1.23-git201403102151-1ubuntu1) ...
Setting up bluetooth (4.101-0ubuntu13.1) ...
Setting up bluez-alsa:amd64 (4.101-0ubuntu13.1) ...
Setting up bluez-alsa:i386 (4.101-0ubuntu13.1) ...
Setting up bluez-gstreamer (4.101-0ubuntu13.1) ...
Setting up bluez-utils (4.101-0ubuntu13.1) ...
$ sudo mv /var/lib/dpkg/info/* /var/lib/dpkg/info_old/
$ sudo rm -rf /var/lib/dpkg/info
$ sudo mv /var/lib/dpkg/info_old/ /var/lib/dpkg/info/
```
输入上述命令之后，在执行 sudo apt-get update 和 sudo apt-get upgrade 就不会有问题了。

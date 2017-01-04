title: 'Maven Wrapper' 
date: 2016-11-14 10:53:29
categories: 技术
tags: maven
---
Maven是一个常用的构建工具，但是Maven的版本和插件的配合并不是那么完美，有时候你不得不切换到一个稍微旧一些的版本，以保证所有东西正常工作。

而Gradle提供了一个Wrapper，可以很好解决版本切换的问题，当然更重要的是不需要预安装Gradle。

Maven虽然没有官方的Wrapper，但是有一个第三方的Wrapper可以使用。
<!-- more -->

安装很简单 `mvn -N io.takari:maven:wrapper` ，安装完成如下
![安装](/img/maven/1.png)

使用的时候直接 `./mvnw clean install` 即可，它会自动下载最新版本来执行。
![运行](/img/maven/2.png)

如果需要指定版本,重新生成mvnw文件在运行即可

```js
mvn -N io.takari:maven:wrapper -Dmaven=3.1.0
./mvnw clean install
```
![切换版本](/img/maven/3.png)

Maven是一个常用的构建工具，但是Maven的版本和插件的配合并不是那么完美，有时候你不得不切换到一个稍微旧一些的版本，以保证所有东西正常工作。

而Gradle提供了一个Wrapper，可以很好解决版本切换的问题，当然更重要的是不需要预安装Gradle。

Maven虽然没有官方的Wrapper，但是有一个第三方的Wrapper可以使用。

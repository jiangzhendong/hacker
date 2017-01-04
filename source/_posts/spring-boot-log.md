title: 'Spring Boot 日志管理和收集'
date: 2016-11-14 11:53:29
categories: 技术
tags: [java,springboot,logs]
---
日志对于应用程序的重要性不言而喻，社区也有各种各样的日志框架。

Spring Boot从实现上不一依赖于任何日志框架的实现，只有对于common-logging API的依赖，对应的抽象实现是 LoggingSystem 。

依靠这一层的抽象，对于日志等级的配置可以完全在Spring Boot中实现，比如 application.properties 中
<!-- more -->

```js
logging.level.org.springframework.web=INFO
logging.level.org.hibernate=DEBUG
```
对于具体的日志框架实现可以选择 Java Util Logging, Log4J, Log4J2 或者 Logback。从mvn库的依赖和下载数量来看Logback的使用数量确实有较大的优势。

对于具体实现部分的配置还是由实现框架本身管理，比如Logback对应的logback.xml文件。

大部分日志的输出一般会对应两部分，一部分是stdout，一部分是日志文件。

对于大规模部署的云原生应用，直接输出到stdout，然后从docker中收集日志也是一个不错的选择的。

市面上日志聚合和搜索平台实在太多，有部分需要从代码层面做出修改，但是最简单的办法还是从日志实现本身入手。比如 
loggly 提供了对于logback的支持。

只需要在配置加上对应的appender即可。

```js
<configuration>
    <appender name="STDOUT" class="ch.qos.logback.core.ConsoleAppender">
        <encoder>
            <pattern>%d{HH:mm:ss.SSS} [%thread] %-5level %logger{36} - %msg%n</pattern>
        </encoder>
    </appender>
    <appender name="loggly" class="ch.qos.logback.ext.loggly.LogglyAppender">
        <endpointUrl>http://logs-01.loggly.com/inputs/[your secret key]/tag/logback</endpointUrl>
        <pattern>%d{"ISO8601", UTC} %p %t %c{0}.%M - %m%n</pattern>
    </appender>
    <appender name="loggly-async" class="ch.qos.logback.classic.AsyncAppender">
        <appender-ref ref="loggly" />
    </appender>
    <root level="info">
        <appender-ref ref="loggly-async" />
    </root>
    <root level="ERROR">
        <appender-ref ref="STDOUT"/>
    </root>
</configuration>
```
效果如下图 
DashBoard 
Search

日志对于应用程序的重要性不言而喻，社区也有各种各样的日志框架。

Spring Boot从实现上不一依赖于任何日志框架的实现，只有对于common-logging API的依赖，对应的抽象实现是 LoggingSystem 。

依靠这一层的抽象，对于日志等级的配置可以完全在Spring Boot中实现，比如 application.properties 中
```js
logging.level.org.springframework.web=INFO
logging.level.org.hibernate=DEBUG
```

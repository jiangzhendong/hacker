title:  Dubbo基础篇_04_使用Maven构建Dubbo服务可执行jar包 
date: 2016-09-14 09:53:29
categories: 技术
tags: [java,dubbo,spring]
---
* 服务器:192.168.1.121(edu-provider-01)

## 一、Dubbo服务的运行方式：

### 1、使用Servlet容器运行（Tomcat、Jetty等）----不可取 缺点：增加复杂性（端口、管理）
tomcat/jetty等占用端口,dubbo服务也需要端口
浪费资源（内存）:单独启动tomcat,jetty占用内存大

### 2、自建Main方法类来运行（Spring容器） ----不建议（本地调试可用） 
缺点： Dobbo本身提供的高级特性没用上
自已编写启动类可能会有缺陷

### 3、使用Dubbo框架提供的Main方法类来运行（Spring容器）----建议使用 优点：框架本身提供（com.alibaba.dubbo.container.Main）
<!-- more -->
可实现优雅关机（ShutdownHook）
注意点
spring-context.xml 
<import resource="classpath:spring/xxx.xml" />
官方：服务容器的加载内容可以扩展，内置了spring, jetty, log4j等加载，可通过Container扩展点进行扩展
Dubbo是通过JDK的ShutdownHook来完成优雅停机的，所以如果用户使用"kill -9 PID"等强制关闭指令，是不会执行优雅停机的，只有通过"kill PID"时，才会执行。
原理：
服务提供方停止时，先标记为不接收新请求，新请求过来时直接报错，让客户端重试其它机器。 然后，检测线程池中的线程是否正在运行，如果有，等待所有线程执行完成，除非超时，则强制关闭。
服务消费方停止时，不再发起新的调用请求，所有新的调用在客户端即报错。然后，检测有没有请求的响应还没有返回，等待响应返回，除非超时，则强制关闭。
## 二、Maven构建Dubbo服务可执行Jar包的配置
[html] view plain copy 在CODE上查看代码片派生到我的代码片
```js
<!--MAVEN打包duboo可执行jar begin -->  
    <build>  
        <finalName>edu-service-user</finalName>  
  
        <resources>  
            <resource>  
                <targetPath>${project.build.directory}/classes</targetPath>  
                <directory>src/main/resources</directory>  
                <filtering>true</filtering>  
                <includes>  
                    <include>**/*.xml</include>  
                    <include>**/*.properties</include>  
                </includes>  
            </resource>  
            <!-- 结合com.alibaba.dubbo.container.Main -->  
            <resource>  
                <targetPath>${project.build.directory}/classes/META-INF/spring</targetPath>  
                <directory>src/main/resources/spring</directory>  
                <filtering>true</filtering>  
                <includes>  
                    <include>spring-context.xml</include>  
                </includes>  
            </resource>  
        </resources>  
          
        <pluginManagement>  
            <plugins>  
                <!-- 解决Maven插件在Eclipse内执行了一系列的生命周期引起冲突 -->  
                <plugin>  
                    <groupId>org.eclipse.m2e</groupId>  
                    <artifactId>lifecycle-mapping</artifactId>  
                    <version>1.0.0</version>  
                    <configuration>  
                        <lifecycleMappingMetadata>  
                            <pluginExecutions>  
                                <pluginExecution>  
                                    <pluginExecutionFilter>  
                                        <groupId>org.apache.maven.plugins</groupId>  
                                        <artifactId>maven-dependency-plugin</artifactId>  
                                          
                                        <goals>  
                                            <goal>copy-dependencies</goal>  
                                        </goals>  
                                    </pluginExecutionFilter>  
                                    <action>  
                                        <ignore />  
                                    </action>  
                                </pluginExecution>  
                            </pluginExecutions>  
                        </lifecycleMappingMetadata>  
                    </configuration>  
                </plugin>  
            </plugins>  
        </pluginManagement>  
        <plugins>  
            <!-- 打包jar文件时，配置manifest文件，加入lib包的jar依赖 -->  
            <plugin>  
                <groupId>org.apache.maven.plugins</groupId>  
                <artifactId>maven-jar-plugin</artifactId>  
                <configuration>  
                    <classesDirectory>target/classes/</classesDirectory>  
                    <archive>  
                        <manifest>  
                            <mainClass>com.alibaba.dubbo.container.Main</mainClass>  
                            <!-- 打包时 MANIFEST.MF文件不记录的时间戳版本 -->  
                            <useUniqueVersions>false</useUniqueVersions>  
                            <addClasspath>true</addClasspath>  
                            <classpathPrefix>lib/</classpathPrefix>  
                        </manifest>  
                        <manifestEntries>  
                            <Class-Path>.</Class-Path>  
                        </manifestEntries>  
                    </archive>  
                </configuration>  
            </plugin>  
            <plugin>  
                <groupId>org.apache.maven.plugins</groupId>  
                <artifactId>maven-dependency-plugin</artifactId>  
                <executions>  
                    <execution>  
                        <id>copy-dependencies</id>  
                        <phase>package</phase>  
                        <goals>  
                            <goal>copy-dependencies</goal>  
                        </goals>  
                        <configuration>  
                            <type>jar</type>  
                            <includeTypes>jar</includeTypes>  
                            <useUniqueVersions>false</useUniqueVersions>  
                            <outputDirectory>  
                                ${project.build.directory}/lib  
                            </outputDirectory>  
                        </configuration>  
                    </execution>  
                </executions>  
            </plugin>  
        </plugins>  
  
</build>  
<!--MAVEN打包duboo可执行jar end -->
``` 
## 三、可执行Dubbo服务Jar在linux上的操作
### 1.获取数据库密码密文，使用druid
上传druid-0.2.23.jar包到provider-01服务器192.168.1.121上，使用 Java -cp druid-0.2.23.jar com.alibaba.druid.filter.config.ConfigTools root获取加密的密码
[root@provider-01 ~]# java -cp druid-0.2.23.jar com.alibaba.druid.filter.config.ConfigTools root
bNVOqb7WKLX5Bjnw+LMv92taj25KOxDimXxILPQjw42wgv+1lHzOH8kr97xDwWdhpY67
QuYCS7sWN4W46YbkFA==
[root@provider-01 ~]# 
![jdbc](http://img.blog.csdn.net/20160501225203938?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

### 2.打包provider提供者服务jar
![](http://img.blog.csdn.net/20160417172347973?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)
![](http://img.blog.csdn.net/20160417172012577?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)

### 3.把edu-service-user.jar与其lib上传到provider-01服务器上
[root@provider-01 home]# mkdir /home/yxq/edu/service
[root@provider-01 home]# mkdir /home/yxq/edu/service/user
![](http://img.blog.csdn.net/20160417172742853?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)
### 4.手动执行jar
启动zookeeper注册中心192.168.1.121
[root@provider-01 user]# ls
edu-service-user.jar  lib
[root@provider-01 user]# java -jar edu-service-user.jar 
![](http://img.blog.csdn.net/20160417173038720?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)
dubbo管理控制台
![](http://img.blog.csdn.net/20160417173204408?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQv/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/Center)
[root@provider-01 user]# ps -ef | grep edu-service
root      12391  11678  1 06:46 pts/0    00:00:08 java -jar edu-service-user.jar
root      12423  11678  0 06:59 pts/0    00:00:00 grep edu-service
[root@provider-01 user]# kill 12391
手工维护Dubbo服务 
java -jar edu-service-xxx.jar & 
kill PID 
kill -9 PID


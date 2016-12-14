title: dubbo java 调用服务出错 
date: 2016-12-14 09:53:29
categories: 技术
tags: [dubbo,java]
---
问题：
```js
org.xml.sax.SAXParseException; lineNumber: 8; columnNumber: 12; schema_reference.4: Failed to read schema document 'http://www.springframework.org/schema/beans/spring-beans.xsd', because 1) could not find the document; 2) the document could not be read; 3) the root element of the document is not <xsd:schema>.
[2016-12-13T13:47:54,071][ERROR][c.h.p.l.RunLogspoutJobs  ] Line 8 in XML document from class path resource [applicationContext.xml] is invalid; nested exception is org.xml.sax.SAXParseException; lineNumber: 8; columnNumber: 12; cvc-elt.1: Cannot find the declaration of element 'beans'.
org.springframework.beans.factory.xml.XmlBeanDefinitionStoreException: Line 8 in XML document from class path resource [applicationContext.xml] is invalid; nested exception is org.xml.sax.SAXParseException; lineNumber: 8; columnNumber: 12; cvc-elt.1: Cannot find the declaration of element 'beans'.
```
<!-- more -->
解决方式：
	pom.xml 加入配置，因为是jar读取不到配置，所以添加一个lib。
```js
<plugins>
            <!-- 打包jar文件时，配置manifest文件，加入lib包的jar依赖 -->
            <plugin>
                <groupId>org.apache.maven.plugins</groupId>
                <artifactId>maven-jar-plugin</artifactId>
                <configuration>
                    <classesDirectory>target/classes/</classesDirectory>
                    <archive>
                        <manifest>
                            <mainClass>com.haier.paas.lbip.RunLogspoutJobs</mainClass>
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

            <!-- 把依赖的jar包,打成一个lib文件夹 -->
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
                            <outputDirectory>
                                ${project.build.directory}/lib
                            </outputDirectory>
                        </configuration>
                    </execution>
                </executions>
            </plugin>
        </plugins>

```

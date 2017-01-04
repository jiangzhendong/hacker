title: How to setup Docker Monitoring
date: 2016-12-21 09:53:29
categories: docker
tags: [docker,linux,shell]
---
Docker monitoring of servers and containers is becoming necessary the more Docker hosts and containers we provision. This tutorial will walk you through how to glue together several different components in order to achieve Docker monitoring.

### Components for Docker Monitoring

First things first. We assume that Docker is installed, configured, and running on your host before we begin. Please ensure you can connect to your Docker host with a Web Browser either locally or over a Public IP. The rest of the Tutorial we will refer to this as the DockerIP The below components will be used to create our Docker Monitoring solution.

[cAdvisor](https://registry.hub.docker.com/u/google/cadvisor/) - Google has been using containers for quite sometime and created cAdvisor to help monitor their infrastructure. This single tool alone is an amazing monitoring tool. It not only monitors your Docker containers but the Docker host as well without any configuratio by just running the cAdvisor container on your Docker host. Be sure to check out the [cAdvisor GitHub](https://github.com/google/cadvisor) for more documentation on the API and different configuration options.
<!-- more -->
[InfluxDB](https://registry.hub.docker.com/u/tutum/influxdb/) - InfluxDB is a distributed time series database. cAdvisor only displays realtime information and doesn't store the metrics. We need to store the monitoring information which cAdvisor provides in order to display a time range other than realtime.

[Grafana Metrics Dashboard](https://registry.hub.docker.com/u/grafana/grafana/) - The Grafana Dashboard allows us to pull all the pieces together visually. This powerful Dashboard allows us to run queries against the InfluxDB and chart them accordingly in a very nice layout.

## Installation of Docker Monitoring

Now that we have an overview of the different components involved in our Docker Monitoring setup let's get started pulling it all together.

We will start with the InfluxDB first and work our way towards connecting the cAdvisor.

1) Install the InfluxDb. We use the default settings below and name the container influxsrv which we will use later on for linking. 
```shell
sudo docker run -d
-p 8083:8083
-p 8086:8086
--expose 8090
--expose 8099
--name influxsrv
tutum/influxdb
```
Let's test quickly that our InfluxDB installed correctly. Navigate to your http://DockerIP:8083 Use the credentials below to login to InfluxDB. 
Username - root 
Password - root 
![InfluxDB Login Screen](/docker/InfluxDB_login.png)
2) Create the cadvisor Database 
After logging into InfluxDB click on the Databases link at the top of the screen. Type the name cadvisor for the Database name and click Create Database 
![Create Database](/docker/CreateDB.png)

3) Install the cAdvisor container and link it to the InfluxDB container.
```shell
sudo docker run
--volume=/:/rootfs:ro
--volume=/var/run:/var/run:rw
--volume=/sys:/sys:ro
--volume=/var/lib/docker/:/var/lib/docker:ro
--publish=8080:8080
--detach=true
--link influxsrv:influxsrv
--name=cadvisor
google/cadvisor:latest
-storage_driver_db=influxdb
-storage_driver_host=influxsrv:8086
```
Once the cAdvisor container has been installed and running you can now navigate to the http://DockerIP:8080 For example, http://192.168.10.1:8080 You should now see the cAdvisor gathering statistics on your Docker host and containers. 
![cAdvisor Dashboard](/docker/cAdvisor_Dashboard.png)

4) Install the Grafana Dashboard and link it to the InfluxDB container: 
```shell
sudo docker run -d
-p 3000:3000
-e INFLUXDB_HOST=localhost
-e INFLUXDB_PORT=8086
-e INFLUXDB_NAME=cadvisor
-e INFLUXDB_USER=root
-e INFLUXDB_PASS=root
--link influxsrv:influxsrv
--name grafana
grafana/grafana
```

5) Login to Grafana and configure the Data Sources. 
Navigate to http://DockerIP:3000 
Username - admin 
Password - admin

6) Connect the InfluxDB to the Grafana Dashboard: 
Once logged in click on the Grafana icon(Fireball) in the upper left hand corner of the GUI. This should pop out a sidebar menu. Click on Data Sources.

Next, click on Add New Data Source at the top of the screen. 
Fill in the following information in the Data Source screen:

Data Source Settings

Name: influxdb 
Type: InfluxDB 0.8.x 
Be sure to check default box.

Http settings 
Url: http://influxsrv:8086 (This is the name we specified when createing the link on the Grafana container) 
Access: proxy 
Basic Auth: Enabled 
User: admin 
Password: admin

InfluxDB Details 
Database: cadvisor (Or the name you specified when creating the database in step 2) 
User: root 
Password: root

You should now have an established connection to the InfluxDB which we will test in the next section. 
![Grafana Data Source Configuration](/docker/Grafana_Config.png)

### Configuring Grafana for Docker Monitoring

Now comes the fun part. Let's setup our first Dashboard with Grafana and visualize the data coming from the cAdvisor.

1) Click on the Grafana icon once again (The Fireball icon upper left corner)

2) Open the Dashboard menu --> Expand the Home Menu drop Down --> Click +New

3) We've now created a new Dashboard inside of Grafana. Let's create our first graph inside this Dashboard. Click the green vertical line as seen below in the screenshot circled in Red. This expands the row options for the Dashboard.

4) Click Add Panel --> Graph

![Create New Grafana Dashboard](/docker/New_Dashboard.png)

5) Click the Title area of the new Graph you created where it says "no title (click here)" and click Edit 
![Create Grafana Graph](/docker/Create_Graph.jpg)

6) It's time to write our first query for our graph. We will create a graph displaying the Filesystem storage limit and usage. 
Query 1 - Fill in the following information inside the Graph screen: series: stats 
Click on "value" which will present you a drop down list of available series available inside of the InfluxDB. 
select: mean(fs_limit) 
Alias: Limit

Query 2 - At the Bottom of Graph screen is an +Add Query button which allows us to add another metric to our graph. series: stats 
select: mean(fs_usage) 
Alias: Usage

![Docker Filesystem Graph](/docker/filesystem_graph.png)

7) Click on the General Menu and Change the Title of your Graph

8) Click on the Axis & Grid Menu 
Left Y Unit: Bytes 
Your Chart should now display with the correct units.

9) Once finished like any other project be sure to save your work. Hit the Save icon a the top of the screen.

The queries seen in the sample screenshot can be found here - Docker Monitoring Queries

### Docker Monitoring Conclusion

We have now built a single Grafana Dashboard with a Graph containing our Filesystem statistics. As you can see it's extremely simple to create multiple graphs to monitor our Docker Host and Containers.

Be sure to check out the Grafana Docs to dive deeper with the queries and functionality of Grafana. Take a look below at the screenshot which shows the possibilities for creating some really interesting graphs (Bandwidth, CPU Usage per Container, Memory Usage, and Filesystem Limit/Usage).

![Docker Monitoring with cAdvisor, InfluxDB, and Grafana](/docker/Docker_Monitoring.png)

Leave a comment below if you have any issues or questions.

Good Luck!

Troubleshooting

In the event you have troubles this is for you. It took me quite sometime to figure out all the settings and where the problems were with the connection from Grafana to InfluxDB. In the event you have issues with your Graphs I highly recommend a Development Console in your Browser of choice.

With the development console it is really easy to see problems with your queries or connections to the InfluxDB container. For Example: Chrome Development Tools -> More Tools -> Javascript Console

Another workaround is using the IP address of the containers to resolve connection issues. However, if you restart the container the IP address changes so this is only a temp fix.
```shell
docker inspect <container name>
```

Search the output for the IP address which is under the Network Settings section as seen below: 
```shell
"NetworkSettings": {
"Bridge": "docker0",
"Gateway": "172.17.42.1",
"GlobalIPv6Address": "",
"GlobalIPv6PrefixLen": 0,
"IPAddress": "172.17.0.54",
```
You can then replace the name that we used in Data Source settings we used above as a workaround. This worked for me until I fixed the links between containers.

Update

Thanks to everyone for the overwhelming response to this article. Since this article was published Dale Kate-Murray and Ross Jimenez created a [Docker Monitoring Compose file](http://bit.ly/1LFek7L) to help everyone get started.

Another question that came up in the comments is how to build the different dashboards. So here is the JSON file from the Dashboard - [https://github.com/vegasbrianc/docker-monitoring](https://github.com/vegasbrianc/docker-monitoring)

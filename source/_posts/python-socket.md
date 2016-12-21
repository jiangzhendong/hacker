title: Python socket – chat server and client with code example 
date: 2016-12-16 11:53:29
categories: Python
tags: [python,linux,chat]
---
## Socket based chat application
In our previous article on socket programming in python we learned about the basics of creating a socket server and client in python. In this post we are going to write a very simple chat application in python that is powered by sockets.
The chat application we are going to make will be more like a chat room, rather than a peer to peer chat. So this means that multiple users can connect to the chat server and send their messages. Every message is broadcasted to every connected chat user. The construction is as simple as the theory.

### Code

The code consists of 2 python scripts. First is the server and the other is the chat client.
<!-- more -->
### Chat server
The chat server does the following things

* 1. Accept multiple incoming connections for client.
* 2. Read incoming messages from each client and broadcast them to all other connected clients.

Here is the code of the chat server. It server opens up port 5000 to listen for incoming connections. The chat client must connect to this same port. You can change the port number if you want.

The server handles multiple chat clients with select based multiplexing. The select function monitors all the client sockets and the master socket for readable activity. If any of the client socket is readable then it means that one of the chat client has send a message.

```js
# Get the list sockets which are ready to be read through select
read_sockets,write_sockets,error_sockets = select.select(CONNECTION_LIST,[],[])
```
When the select function returns, the read_sockets will be an array consisting of all socket descriptors that are readable. So if the master socket is readable, the server would accept the new connection. If any of the client socket is readable, the server would read the message, and broadcast it back to all clients except the one who send the message. The following function broadcasts the message to all chat clients.
```js
def broadcast_data (sock, message):
    #Do not send the message to master socket and the client who has send us the message
    for socket in CONNECTION_LIST:
        if socket != server_socket and socket != sock :
            try :
                socket.send(message)
            except :
                # broken socket connection may be, chat client pressed ctrl+c for example
                socket.close()
                CONNECTION_LIST.remove(socket)
```
If the broadcast function fails to send message to any of the client, the client is assumed to be disconnected and the connection is closed and the socket is removed from the connection list.

Rest of the program is quite self explanatory. Here is the full code of the chat client.

```js
# Tcp Chat server
 
import socket, select
 
#Function to broadcast chat messages to all connected clients
def broadcast_data (sock, message):
    #Do not send the message to master socket and the client who has send us the message
    for socket in CONNECTION_LIST:
        if socket != server_socket and socket != sock :
            try :
                socket.send(message)
            except :
                # broken socket connection may be, chat client pressed ctrl+c for example
                socket.close()
                CONNECTION_LIST.remove(socket)
 
if __name__ == "__main__":
     
    # List to keep track of socket descriptors
    CONNECTION_LIST = []
    RECV_BUFFER = 4096 # Advisable to keep it as an exponent of 2
    PORT = 5000
     
    server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    # this has no effect, why ?
    server_socket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    server_socket.bind(("0.0.0.0", PORT))
    server_socket.listen(10)
 
    # Add server socket to the list of readable connections
    CONNECTION_LIST.append(server_socket)
 
    print "Chat server started on port " + str(PORT)
 
    while 1:
        # Get the list sockets which are ready to be read through select
        read_sockets,write_sockets,error_sockets = select.select(CONNECTION_LIST,[],[])
 
        for sock in read_sockets:
            #New connection
            if sock == server_socket:
                # Handle the case in which there is a new connection recieved through server_socket
                sockfd, addr = server_socket.accept()
                CONNECTION_LIST.append(sockfd)
                print "Client (%s, %s) connected" % addr
                 
                broadcast_data(sockfd, "[%s:%s] entered room\n" % addr)
             
            #Some incoming message from a client
            else:
                # Data recieved from client, process it
                try:
                    #In Windows, sometimes when a TCP program closes abruptly,
                    # a "Connection reset by peer" exception will be thrown
                    data = sock.recv(RECV_BUFFER)
                    if data:
                        broadcast_data(sock, "\r" + '<' + str(sock.getpeername()) + '> ' + data)                
                 
                except:
                    broadcast_data(sock, "Client (%s, %s) is offline" % addr)
                    print "Client (%s, %s) is offline" % addr
                    sock.close()
                    CONNECTION_LIST.remove(sock)
                    continue
     
    server_socket.close()
```
Run the server in a console.
``
$ python chat_server.py 
Chat server started on port 5000
``
### Chat client
Now lets code the chat client that will connect to the above chat server. The client is based on the telnet program in python. It connects to a remote server, sends messages and receives messages.

The chat client does the following 2 things :

* 1. Listen for incoming messages from the server.
* 2. Check user input. If the user types in a message then send it to the server.

Now here is something tricky. The client has to actually listen for server message and user input at the same time. To do this, we use the select function. The select function can monitor multiple sockets or file descriptors for some "interesting activity" which is this case is readable. When a message comes from the server on the connected socket, it is readable and when the user types a message and hits enter, the stdin stream is readable.

So the select function has to monitor 2 streams. First is the socket that is connected to the remote webserver, and second is stdin or terminal input stream. The select function blocks till something happens. So after calling select, it will return only when either the server socket receives a message or the user enters a message. If nothing happens it keeps on waiting.

```js
socket_list = [sys.stdin, s]
         
# Get the list sockets which are readable
read_sockets, write_sockets, error_sockets = select.select(socket_list , [], [])
```
We simply create an array of the stdin file descriptor that is available from the sys module, and the server socket s. Then we call the select function passing it the list. The select function returns a list of arrays that are readable, writable or had an error. The readable sockets will be again a list of sockets that is readable.

So in this case, the read_sockets array will contain either the server socket, or stdin or both. Then the next task is to do relevant processing based on which socket is readable. If the server socket is readable, it means that the server has send a message on that socket and so it should be printed. If stdin is readable, it means that the user typed a message and hit enter key, so that message should be read and send to server as a chat message.

Here is the python code that implements the above logic using select function

```js
# telnet program example
import socket, select, string, sys
 
def prompt() :
    sys.stdout.write('<You> ')
    sys.stdout.flush()
 
#main function
if __name__ == "__main__":
     
    if(len(sys.argv) < 3) :
        print 'Usage : python telnet.py hostname port'
        sys.exit()
     
    host = sys.argv[1]
    port = int(sys.argv[2])
     
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.settimeout(2)
     
    # connect to remote host
    try :
        s.connect((host, port))
    except :
        print 'Unable to connect'
        sys.exit()
     
    print 'Connected to remote host. Start sending messages'
    prompt()
     
    while 1:
        socket_list = [sys.stdin, s]
         
        # Get the list sockets which are readable
        read_sockets, write_sockets, error_sockets = select.select(socket_list , [], [])
         
        for sock in read_sockets:
            #incoming message from remote server
            if sock == s:
                data = sock.recv(4096)
                if not data :
                    print '\nDisconnected from chat server'
                    sys.exit()
                else :
                    #print data
                    sys.stdout.write(data)
                    prompt()
             
            #user entered a message
            else :
                msg = sys.stdin.readline()
                s.send(msg)
                prompt()
  
```           
Run the client from multiple consoles.
``
$ python telnet.py localhost 5000
Connected to remote host. Start sending messages
<You> hello
<You> I am fine
<('127.0.0.1', 38378)> ok good
<You>
``
on another console
``
<You> [127.0.0.1:39339] entered room
<('127.0.0.1', 39339)> hello
<('127.0.0.1', 39339)> I am fine
<You> ok good
``
So the messages send by one client are seen on the consoles of other clients. Logic is quite simple. Run it and check it out.

## Note

The above shown chat client is not going to work on windows. It uses the select function to read data from both the socket and the input stream. This works on linux but not on windows.

The python documentation on select mentions this

`File objects on Windows are not acceptable, but sockets are. On Windows, the underlying select() function is provided by the WinSock library, and does not handle file descriptors that don’t originate from WinSock.`
Linux treats sockets and file descriptors in the same manner, therefor the select function is able to read from stdin. On windows the select function will not read anything except sockets created by the winsock socket functions.

There is another drawback that the above shown chat program suffers. If in the chat client a user is typing a message and while typing a message comes from the server, then the server message shall be printed rightaway and the message that the user was typing would be lost. That is the expected behaviour of this program and there is nothing that can be done to fix this properly.

Only solution is to use better terminal libraries like ncurses to keep the user input separate from terminal output. Or write a gui program.


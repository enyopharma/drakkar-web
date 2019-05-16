
// start a web server.
var http = require('http');

var server = http.createServer(function (request, response) {
    response.writeHead(404);
    response.end();
});

server.listen(80, function () {});

// listen to every redis pubsub channels.
var Redis = require('ioredis');

var redis = new Redis({ host: 'redis' });

redis.psubscribe('*', function (err, count) {});

// start a socket server using the web server.
var WebSocketServer = require('websocket').server;

var ws = new WebSocketServer({
    httpServer: server,
    autoAcceptConnections: false,
});

ws.on('request', function (request) {
    var connection = request.accept('app', request.origin);

    // send back every event sent from the client so it knows the connection is ok.
    connection.on('message', event => {
        if (event.type == 'utf8') {
            connection.sendUTF(event.utf8Data);
        }
    })

    // send to the client every message sent to a redis pubsub channel.
    redis.on('pmessage', function (pattern, channel, serialized) {
        var message = JSON.parse(serialized)

        connection.sendUTF(JSON.stringify({
            id: message.id,
            channel: channel,
            payload: message.payload,
        }));
    });
});

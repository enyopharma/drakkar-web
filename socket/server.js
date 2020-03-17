var Redis = require('ioredis');
var Http = require('http');
var WebSocket = require('websocket');

// connect to redis
var redis = new Redis({ host: 'redis' });

// create webserver
var server = Http.createServer(function (request, response) {
    response.writeHead(404);
    response.end();
});

// let the server listen on port 80
server.listen(80, function () { });

// listen to every redis pubsub event
redis.psubscribe('*', function (err, count) { });

// setup websocket server.
var ws = new WebSocket.server({
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
        console.log(channel, serialized)

        connection.sendUTF(JSON.stringify({
            channel: channel,
            payload: JSON.parse(serialized),
        }));
    });
});

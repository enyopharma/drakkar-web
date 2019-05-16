
var http = require('http');
var WebSocketServer = require('websocket').server;

var server = http.createServer(function (request, response) {
    response.writeHead(404);
    response.end();
});

server.listen(80, function () {});

ws = new WebSocketServer({
    httpServer: server,
    autoAcceptConnections: false,
});

ws.on('request', function (request) {
    var connection = request.accept('app', request.origin);

    connection.on('message', event => {
        if (event.type == 'utf8') {
            connection.sendUTF(event.utf8Data);
        }
    })
});

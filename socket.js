var app = require('http').createServer(handler);

var io = require('socket.io')(app);
var Redis = require('ioredis');
var redis = new Redis({
    // host: 'redis'
    host: '127.0.0.1'
});

app.listen(3000, function() {
    console.log('Server is running on port 3000!');
});


function handler(req, res) {
    res.writeHead(200);
    res.end('');
}

io.on('connection', function(socket) {});

redis.psubscribe('*', function(err, count) {});

redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);
    console.log(channel + " " + message.event);
    io.emit(channel, message);
});

redis.on("error", function(err){
    console.log(err);
});
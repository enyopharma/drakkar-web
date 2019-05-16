const uuidv4 = require('uuid/v4');

const handle = (from, handler) => {
    const id = uuidv4()
    const socket = new WebSocket(`ws://${window.location.host}:3000`, 'app')

    // this message will be sent back by the server ensuring the connection is ok.
    socket.onopen = () => socket.send(JSON.stringify({
        payload: `Connected to server with id ${id}.`
    }))

    socket.onmessage = event => {
        const message = JSON.parse(event.data)

        id == message.id && from == message.from
            ? handler(message.payload)
            : console.log(message.payload)
    }

    socket.onclose = event => console.log('Disconnected from server.')

    return () => socket.close()
}

export default handle;

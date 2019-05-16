import { useEffect } from 'react';

const uuidv4 = require('uuid/v4');

const useChannel = (channel, handler, state) => {
    const id = uuidv4()

    useEffect(() => {
        const socket = new WebSocket(`ws://${window.location.host}:3000`, 'app')

        // this message will be sent back by the server ensuring the connection is ok.
        socket.onopen = () => socket.send(JSON.stringify({
            id: id,
            payload: `Connected to server with id ${id}.`
        }))

        socket.onmessage = event => {
            const message = JSON.parse(event.data)

            if (id == message.id) {
                channel == message.channel
                    ? handler(message.payload)
                    : console.log(message.payload)
            }
        }

        socket.onclose = event => console.log('Disconnected from server.')

        return () => socket.close()
    }, state)

    return id
}

export { useChannel };

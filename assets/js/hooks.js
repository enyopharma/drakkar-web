import { useEffect } from 'react';

const useSocket = (id, handler, state) => {
    useEffect(() => {
        if (id == '') return;

        const socket = new WebSocket(`ws://${window.location.host}:3000`, 'app')

        // this message will be sent back by the server ensuring the connection is ok.
        socket.onopen = () => socket.send(JSON.stringify({
            channel: 'echo',
            payload: {
                id: id,
                message: `Listening for job with id '${id}'.`,
            },
        }))

        socket.onmessage = event => {
            const message = JSON.parse(event.data)

            const channel = message.channel;
            const payload = message.payload;

            if (payload.id == id) {
                if (channel == 'echo') {
                    console.log(payload.message)
                    return;
                }

                handler(payload)
                socket.close()
            }
        }

        socket.onclose = event => console.log('Disconnected from server.')

        return () => socket.close()
    }, state)
}

export { useSocket };

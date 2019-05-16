const uuidv4 = require('uuid/v4');

export default class {
    constructor() {
        this.id = uuidv4()
        this.url = ['ws://', window.location.host, ':3000'].join('')
    }

    connect() {
        this.socket = new WebSocket(this.url)

        this.socket.onopen = event => {
            console.log(`Connected to ${this.url} with client id ${this.id}.`)
        }
    }

    onmessage(target, handler) {
        if (! this.socket) this.connect();

        this.socket.onmessage = event => {
            const data = JSON.parse(event.data)

            if (this.id == data.id && this.target == data.target) {
                handler(data.payload)
            }
        }
    }

    close() {
        if (this.socket) this.socket.close()

        console.log(`Disconnected from ${this.url}.`)
    }
}

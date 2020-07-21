import { render } from 'react-dom'

import { Description } from './src/types'

import { App } from './components/App'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    table: (container: string, descriptions: Description[]) => {
        render(App(descriptions), document.getElementById(container))
    }
}

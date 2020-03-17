import { render } from 'react-dom'

import { Description } from './types'

import { Table } from './components/Table'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    table: (container: string, descriptions: Description[]) => {
        render(Table(descriptions), document.getElementById(container))
    }
}

import React from 'react'
import { render } from 'react-dom'

import { Description } from './src/types'

import { Table } from './components/Table'

type InitTable = (container: string, descriptions: Description[]) => ReturnType<typeof render>

export const table: InitTable = (container, descriptions) => {
    render(<Table descriptions={descriptions} />, document.getElementById(container))
}

declare global {
    interface Window {
        table: InitTable;
    }
}

window.table = table

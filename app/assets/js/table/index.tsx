import React from 'react'
import { render } from 'react-dom'

import { Description } from './src/types'

import { Table } from './components/Table'

type InitTableType = (container: string, descriptions: Description[]) => ReturnType<typeof render>

export const table: InitTableType = (container, descriptions) => {
    render(<Table descriptions={descriptions} />, document.getElementById(container))
}

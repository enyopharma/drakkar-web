import React from 'react'
import { render } from 'react-dom'

import Table from './components/Table'

const init = (container, descriptions) => {
    render(<Table descriptions={descriptions} />, document.getElementById(container))
}

window.description = { table: init }

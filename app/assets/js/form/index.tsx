import React from 'react'
import { render } from 'react-dom'

import { DescriptionType, Description } from './src/types'

import { App } from './components/App'

type InitForm = (container: string, type: DescriptionType, run_id: number, pmid: number, description: Description | null) => ReturnType<typeof render>

export const form: InitForm = (container, type, run_id, pmid, description) => {
    render(<App type={type} run_id={run_id} pmid={pmid} description={description} />, document.getElementById(container))
}

declare global {
    interface Window {
        form: InitForm;
    }
}

window.form = form

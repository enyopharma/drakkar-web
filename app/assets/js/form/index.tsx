import React from 'react'
import { render } from 'react-dom'

import { DescriptionType, Description } from './src/types'

import { App } from './components/App'

type InitFormType = (container: string, type: DescriptionType, run_id: number, pmid: number, description: Description | null) => ReturnType<typeof render>

export const form: InitFormType = (container, type, run_id, pmid, description) => {
    render(<App type={type} run_id={run_id} pmid={pmid} description={description} />, document.getElementById(container))
}

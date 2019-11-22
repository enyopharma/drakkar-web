import { render } from 'react-dom'

import { AppState, init } from './src/state'
import { Description, DescriptionType } from './src/types'

import { App } from './components/App'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    form: (container: string, type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
        render(App(type, run_id, pmid, initialState(description)), document.getElementById(container))
    }
}

const initialState = (description: Description | null): AppState => ({
    ui: init.ui,
    description: description == null ? init.description : description,
})

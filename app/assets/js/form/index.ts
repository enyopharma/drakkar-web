import { render } from 'react-dom'

import { DescriptionType, Description } from './src/types'

import { App } from './components/App'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    form: (container: string, type: DescriptionType, run_id: number, pmid: number, description: Description | null) => {
        render(App(type, run_id, pmid, description), document.getElementById(container))
    }
}

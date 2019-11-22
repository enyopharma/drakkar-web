import { render } from 'react-dom'

import { AppState } from './src/state'
import { Description, DescriptionType } from './src/types'

import { App } from './components/App'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    form: (container: string, type: DescriptionType, run_id: number, pmid: number, description) => {
        render(App(type, run_id, pmid, initialState(description)), document.getElementById(container))
    }
}

const initialState = (description: Description): AppState => ({
    ui: init.ui,
    description: description == null ? init.description : description,
})

const init: AppState = {
    ui: {
        query: '',
        method: null,
        interactor1: {
            query: '',
            protein: null,
            editing: false,
            processing: false,
            alignment: null,
        },
        interactor2: {
            query: '',
            protein: null,
            editing: false,
            processing: false,
            alignment: null,
        },
        saving: false,
        feedback: null,
    },
    description: {
        method: { psimi_id: null },
        interactor1: {
            protein: { accession: null },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
        interactor2: {
            protein: { accession: null },
            name: '',
            start: null,
            stop: null,
            mapping: [],
        },
    },
}

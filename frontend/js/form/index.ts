import { render } from 'react-dom'

import { AppState, Description, DescriptionType } from './types'

import { Form } from './components/Form'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    form: (wrapper: string, container: string, type: DescriptionType, run_id: number, pmid: number, description) => {
        render(Form(wrapper, type, run_id, pmid, initialState(description)), document.getElementById(container))
    }
}

const initialState = (description: Description): AppState => ({
    ui: init.ui,
    description: description == null ? init.description : description,
})

const init: AppState = {
    ui: {
        method: { query: '' },
        interactor1: {
            protein: { query: '' },
            editing: false,
            processing: false,
            alignment: null,
        },
        interactor2: {
            protein: { query: '' },
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

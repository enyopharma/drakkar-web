import { render } from 'react-dom'

import { AppState, DescriptionType } from './types'

import { Form } from './components/Form'

declare global {
    interface Window { descriptions: any; }
}

window.descriptions = {
    form: (wrapper: string, container: string, type: DescriptionType, run_id: number, pmid: number, state) => {
        render(Form(wrapper, type, run_id, pmid, initialState(state)), document.getElementById(container))
    }
}

const initialState = (state): AppState => ({
    method: {
        query: '',
        psimi_id: state == null ? null : state.method.psimi_id,
    },
    interactor1: {
        i: 1,
        protein: {
            query: '',
            accession: state == null ? null : state.interactor1.protein.accession,
        },
        name: state == null ? '' : state.interactor1.name,
        start: state == null ? null : state.interactor1.start,
        stop: state == null ? null : state.interactor1.stop,
        mapping: state == null ? '' : state.interactor1.mapping,
        ui: {
            editing: false,
            processing: false,
            alignment: {
                query: '',
                current: null,
            },
        },
    },
    interactor2: {
        i: 2,
        protein: {
            query: '',
            accession: state == null ? null : state.interactor2.protein.accession,
        },
        name: state == null ? '' : state.interactor2.name,
        start: state == null ? null : state.interactor2.start,
        stop: state == null ? null : state.interactor2.stop,
        mapping: state == null ? '' : state.interactor2.mapping,
        ui: {
            editing: false,
            processing: false,
            alignment: {
                query: '',
                current: null,
            },
        },
    },
    ui: {
        saving: false,
        feedback: null,
    },
})

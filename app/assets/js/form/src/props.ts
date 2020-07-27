import * as redux from 'react-redux'

import { AppState, InteractorUI, DescriptionType, InteractorI, Interactor, ProteinType } from './types'

type OwnProps = { type: DescriptionType, run_id: number, pmid: number }
export type AppProps = ReturnType<typeof mapStateToProps>
export type InteractorProps = ReturnType<typeof mapStateToInteractorProps>

export const connect = (component: any) => redux.connect(mapStateToProps)(component);

// mapStateToProps
const mapStateToProps = (state: AppState, { type, run_id, pmid }: OwnProps) => {
    const type1 = 'h'
    const type2 = type == 'hh' ? 'h' : 'v'

    return {
        type: type,
        run_id: run_id,
        pmid: pmid,
        method: {
            psimi_id: state.description.method.psimi_id,
        },
        interactor1: mapStateToInteractorProps(1, type1, state.description.interactor1, state.interactorUI1),
        interactor2: mapStateToInteractorProps(2, type2, state.description.interactor2, state.interactorUI2),
        feedback: state.feedback,
        saving: state.saving,
        resetable: !state.interactorUI1.processing && !state.interactorUI2.processing,
        savable: !state.interactorUI1.editing && !state.interactorUI2.editing
            && !state.interactorUI1.processing && !state.interactorUI2.processing
            && state.description.method.psimi_id != null
            && state.description.interactor1.protein.accession != null
            && state.description.interactor1.name != ''
            && state.description.interactor1.start != null
            && state.description.interactor1.stop != null
            && state.description.interactor2.protein.accession != null
            && state.description.interactor2.name != ''
            && state.description.interactor2.start != null
            && state.description.interactor2.stop != null,
    }
}

const mapStateToInteractorProps = (i: InteractorI, type: ProteinType, interactor: Interactor, ui: InteractorUI) => ({
    i: i,
    type: type,
    accession: interactor.protein.accession,
    name: interactor.name,
    start: interactor.start,
    stop: interactor.stop,
    mapping: interactor.mapping,
    editing: ui.editing,
    processing: ui.processing,
    alignment: ui.alignment,
})

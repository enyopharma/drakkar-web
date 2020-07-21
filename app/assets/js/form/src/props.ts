import * as redux from 'react-redux'

import * as creators from './creators'
import { AppState, InteractorUI, DescriptionType, InteractorI, Interactor, ProteinType, Mature, Sequences, Alignment } from './types'

type OwnProps = { type: DescriptionType, run_id: number, pmid: number }
export type AppProps = ReturnType<typeof mapStateToProps> & ReturnType<typeof mapDispatchToProps>
export type InteractorProps = ReturnType<typeof mapStateToInteractorProps> & ReturnType<typeof mapDispatchToInteractorProps>

export const connect = (component: any) => redux.connect(mapStateToProps, mapDispatchToProps)(component);

// mapStateToProps
const mapStateToProps = (state: AppState, { type }: OwnProps) => {
    const type1 = 'h'
    const type2 = type == 'hh' ? 'h' : 'v'

    return {
        type: type,
        method: {
            psimi_id: state.description.method.psimi_id,
        },
        interactor1: mapStateToInteractorProps(1, type1, state.description.interactor1, state.interactor1),
        interactor2: mapStateToInteractorProps(2, type2, state.description.interactor2, state.interactor2),
        feedback: state.feedback,
        saving: state.saving,
        resetable: !state.interactor1.processing && !state.interactor2.processing,
        savable: !state.interactor1.editing && !state.interactor2.editing
            && !state.interactor1.processing && !state.interactor2.processing
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

// mapDispatchToProps
const mapDispatchToProps = (dispatch: any, { run_id, pmid }: OwnProps) => ({
    actions: {
        save: () => dispatch(creators.fireSave(run_id, pmid)),
        reset: () => dispatch(creators.resetForm()),
        method: {
            select: (psimi_id: string) => dispatch(creators.selectMethod(psimi_id)),
            unselect: () => dispatch(creators.unselectMethod()),
        },
        interactor1: mapDispatchToInteractorProps(1, dispatch),
        interactor2: mapDispatchToInteractorProps(2, dispatch),
    },
})

const mapDispatchToInteractorProps = (i: InteractorI, dispatch: any) => ({
    actions: {
        protein: {
            select: (accession: string) => dispatch(creators.selectProtein(i, accession)),
            unselect: () => dispatch(creators.unselectProtein(i)),
        },
        sequence: {
            edit: () => dispatch(creators.editMature(i)),
            update: (mature: Mature) => dispatch(creators.updateMature(i, mature)),
        },
        mapping: {
            fire: (query: string, sequences: Sequences) => dispatch(creators.fireAlignment(i, query, sequences)),
            add: (alignment: Alignment) => dispatch(creators.addAlignment(i, alignment)),
            remove: (index: number) => dispatch(creators.removeAlignment(i, index)),
            cancel: () => dispatch(creators.cancelAlignment(i)),
        },
    },
})

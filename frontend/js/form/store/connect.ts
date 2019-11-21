import * as redux from 'react-redux'
import * as creators from './creators'

import { DescriptionType, ProteinType, InteractorI, Mature, Sequences, Alignment } from '../types'

type AppStateProps = ReturnType<typeof mapStateToProps>
type AppDispatchProps = ReturnType<typeof mapDispatchToProps>
type OwnProps = { wrapper: string, type: DescriptionType, run_id: number, pmid: number }

export type AppProps = ReturnType<typeof mergeProps>

const mapStateToProps = (state) => state

const mapDispatchToProps = (dispatch, { run_id, pmid }: OwnProps) => ({
    updateMethod: (query: string) => dispatch(creators.updateMethodQuery(query)),
    selectMethod: (psimi_id: string) => dispatch(creators.selectMethod(psimi_id)),
    unselectMethod: () => dispatch(creators.unselectMethod()),
    interactor1: mapDispatchToInteractorProps(1, dispatch),
    interactor2: mapDispatchToInteractorProps(2, dispatch),
    save: () => dispatch(creators.fireSave(run_id, pmid)),
    reset: () => dispatch(creators.resetForm()),
})

const mergeProps = (props: AppStateProps, actions: AppDispatchProps, { wrapper, type }: OwnProps) => {
    const [type1, type2] = type == 'hh'
        ? ['h' as ProteinType, 'h' as ProteinType]
        : ['h' as ProteinType, 'v' as ProteinType]

    return {
        method: {
            query: props.ui.method.query,
            psimi_id: props.description.method.psimi_id,
            update: actions.updateMethod,
            select: actions.selectMethod,
            unselect: actions.unselectMethod,
        },
        interactor1: mergeInteractorProps(1, type1, props.description.interactor1, props.ui.interactor1, actions.interactor1),
        interactor2: mergeInteractorProps(2, type2, props.description.interactor2, props.ui.interactor2, actions.interactor2),
        actions: {
            top: wrapper,
            saving: props.ui.saving,
            feedback: props.ui.feedback,
            resetable: !props.ui.interactor1.processing && !props.ui.interactor2.processing,
            savable: !props.ui.interactor1.editing && !props.ui.interactor2.editing
                && !props.ui.interactor1.processing && !props.ui.interactor2.processing
                && props.description.method.psimi_id != null
                && props.description.interactor1.protein.accession != null
                && props.description.interactor1.name != ''
                && props.description.interactor1.start != null
                && props.description.interactor1.stop != null
                && props.description.interactor2.protein.accession != null
                && props.description.interactor2.name != ''
                && props.description.interactor2.start != null
                && props.description.interactor2.stop != null,
            save: actions.save,
            reset: actions.reset,
        },
    }
}

export const connect = redux.connect(mapStateToProps, mapDispatchToProps, mergeProps);


// interactor
import { Interactor, InteractorUI } from '../types'

type InteractorDispatchProps = ReturnType<typeof mapDispatchToInteractorProps>

const mapDispatchToInteractorProps = (i: InteractorI, dispatch) => ({
    updateProtein: (query: string) => dispatch(creators.updateProteinQuery(i, query)),
    selectProtein: (accession: string) => dispatch(creators.selectProtein(i, accession)),
    unselectProtein: () => dispatch(creators.unselectProtein(i)),
    editSequence: () => dispatch(creators.editMature(i)),
    updateSequence: (mature: Mature) => dispatch(creators.updateMature(i, mature)),
    updateAlignment: (query: string) => dispatch(creators.updateAlignmentQuery(i, query)),
    fireAlignment: (query: string, sequences: Sequences) => dispatch(creators.fireAlignment(i, query, sequences)),
    addAlignment: (alignment: Alignment) => dispatch(creators.addAlignment(i, alignment)),
    removeAlignment: (index: number) => dispatch(creators.removeAlignment(i, index)),
    cancelAlignment: () => dispatch(creators.cancelAlignment(i)),
});

const mergeInteractorProps = (i: InteractorI, type: ProteinType, interactor: Interactor, ui: InteractorUI, actions: InteractorDispatchProps) => ({
    i: i,
    type: type,
    query: ui.protein.query,
    accession: interactor.protein.accession,
    name: interactor.name,
    start: interactor.start,
    stop: interactor.stop,
    mapping: interactor.mapping,
    editing: ui.editing,
    processing: ui.processing,
    alignment: ui.alignment,
    actions: {
        protein: {
            update: actions.updateProtein,
            select: actions.selectProtein,
            unselect: actions.unselectProtein,
        },
        sequence: {
            edit: actions.editSequence,
            update: actions.updateSequence,
        },
        mapping: {
            fire: actions.fireAlignment,
            add: actions.addAlignment,
            remove: actions.removeAlignment,
            cancel: actions.cancelAlignment,
        }
    }
})

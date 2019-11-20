import { Dispatch } from 'redux'
import * as redux from 'react-redux'
import * as creators from './actions'

import { AppState, DescriptionType, ProteinType, InteractorI } from '../types'

// props received by the component.
type OwnProps = { wrapper: string, type: DescriptionType, run_id: number, pmid: number }

// The app props.
export type AppProps = ReturnType<typeof mergeProps>

// interactor state to props.
const mapDispatchToInteractorProps = (i: InteractorI, dispatch: Dispatch<any>) => ({
    uniprot: {
        update: query => dispatch(creators.updateProteinQuery(i, query)),
        select: accession => dispatch(creators.selectProtein(i, accession)),
        unselect: () => dispatch(creators.unselectProtein(i)),
    },
    sequence: {
        edit: () => dispatch(creators.editMature(i)),
        update: mature => dispatch(creators.updateMature(i, mature)),
    },
    mapping: {
        update: query => dispatch(creators.updateAlignmentQuery(i, query)),
        fire: (query, sequences) => dispatch(creators.fireAlignment(i, query, sequences)),
        add: alignment => dispatch(creators.addAlignment(i, alignment)),
        remove: index => dispatch(creators.removeAlignment(i, index)),
        cancel: () => dispatch(creators.cancelAlignment(i)),
    },
});

const mergeInteractorProps = (i: InteractorI, type: ProteinType, props: any, actions: any) => ({
    i: i,
    type: type,
    accession: props.protein.accession,
    editing: props.ui.editing,
    uniprot: Object.assign({
        type: type,
        query: props.protein.query,
        editable: !props.ui.processing,
    }, actions.uniprot),
    sequence: Object.assign({
        type: type,
        current: {
            name: props.name,
            start: props.start,
            stop: props.stop,
        },
        valid: !props.ui.editing,
        editing: props.ui.editing,
        editable: type == 'v' && !props.ui.editing && !props.ui.processing,
    }, actions.sequence),
    mapping: Object.assign({
        i: i,
        type: type,
        query: props.ui.alignment.query,
        name: props.name,
        start: props.start,
        stop: props.stop,
        mapping: props.mapping,
        selecting: props.ui.alignment.current != null,
        processing: props.ui.processing,
        alignment: props.ui.alignment.current,
    }, actions.mapping),
})

// whole state to props.
const mapStateToProps = (state: AppState) => state

const mapDispatchToProps = (dispatch: Dispatch<any>, { run_id, pmid }: OwnProps) => ({
    method: {
        update: query => dispatch(creators.updateMethodQuery(query)),
        select: psimi_id => dispatch(creators.selectMethod(psimi_id)),
        unselect: () => dispatch(creators.unselectMethod()),
    },
    interactor1: mapDispatchToInteractorProps(1, dispatch),
    interactor2: mapDispatchToInteractorProps(2, dispatch),
    save: () => dispatch(creators.fireSave(run_id, pmid)),
    reset: () => dispatch(creators.resetForm()),
})

const mergeProps = (props, actions, { wrapper, type }: OwnProps) => {
    const [type1, type2] = type == 'hh'
        ? ['h' as ProteinType, 'h' as ProteinType]
        : ['h' as ProteinType, 'v' as ProteinType]

    return {
        method: {
            query: props.method.query,
            psimi_id: props.method.psimi_id,
            update: actions.method.update,
            select: actions.method.select,
            unselect: actions.method.unselect,
        },
        interactor1: mergeInteractorProps(1, type1, props.interactor1, actions.interactor1),
        interactor2: mergeInteractorProps(2, type2, props.interactor2, actions.interactor2),
        actions: {
            top: wrapper,
            saving: props.ui.saving,
            feedback: props.ui.feedback,
            resetable: !props.interactor1.ui.processing && !props.interactor2.ui.processing,
            savable: !props.interactor1.ui.editing && !props.interactor2.ui.editing
                && !props.interactor1.ui.processing && !props.interactor2.ui.processing
                && props.method.psimi_id != null
                && props.interactor1.protein.accession != null
                && props.interactor1.name != ''
                && props.interactor1.start != null
                && props.interactor1.stop != null
                && props.interactor2.protein.accession != null
                && props.interactor2.name != ''
                && props.interactor2.start != null
                && props.interactor2.stop != null,
            save: actions.save,
            reset: actions.reset,
        },
    }
}

export const connect = redux.connect(mapStateToProps, mapDispatchToProps, mergeProps);

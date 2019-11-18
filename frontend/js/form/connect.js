import * as redux from 'react-redux'
import * as creators from './creators'

const mergeMethodProps = (props, actions) => Object.assign(props, actions)

const mapDispatchToMethodProps = dispatch => ({
    update: query => dispatch(creators.updateMethodQuery(query)),
    select: psimi_id => dispatch(creators.selectMethod(psimi_id)),
    unselect: () => dispatch(creators.unselectMethod()),
})

const mergeInteractorProps = (i, type, props, actions) => ({
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

const mapDispatchToInteractorProps = (i, dispatch) => ({
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

const mapStateToProps = state => state

const mapDispatchToProps = (dispatch, { run_id, pmid }) => ({
    method: mapDispatchToMethodProps(dispatch),
    interactor1: mapDispatchToInteractorProps(1, dispatch),
    interactor2: mapDispatchToInteractorProps(2, dispatch),
    save: () => dispatch(creators.saveDescription(run_id, pmid)),
    reset: () => dispatch(creators.resetForm()),
})

const mergeProps = (props, actions, { wrapper, type }) => {
    const [type1, type2] = type == 'hh' ? ['h', 'h'] : ['h', 'v']

    return {
        method: mergeMethodProps(props.method, actions.method),
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
                && props.interactor1.start != ''
                && props.interactor1.stop != ''
                && props.interactor2.protein.accession != null
                && props.interactor2.name != ''
                && props.interactor2.start != ''
                && props.interactor2.stop != '',
            save: actions.save,
            reset: actions.reset,
        },
    }
}

export const connect = redux.connect(mapStateToProps, mapDispatchToProps, mergeProps);

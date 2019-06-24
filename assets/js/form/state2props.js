import creators from './creators'

const mapStateToUniprotProps = (i, type, state) => {
    return {
        type: type,
        query: state.protein.query,
        editable: ! state.ui.processing,
    }
}

const mapStateToSequenceProps = (i, type, state) => {
    return {
        type: type,
        current: {
            name: state.name,
            start: state.start,
            stop: state.stop,
        },
        valid: ! state.ui.editing,
        editing: state.ui.editing,
        editable: type == 'v' && ! state.ui.editing && ! state.ui.processing,
    }
}

const mapStateToMappingProps = (i, type, state) => {
    return {
        i: i,
        type: type,
        query: state.ui.alignment.query,
        start: state.start,
        stop: state.stop,
        mapping: state.mapping,
        selecting: state.ui.alignment.current != null,
        processing: state.ui.processing,
        alignment: state.ui.alignment.current,
    }
}

const mapDispatchToUniprotProps = (i, type, dispatch) => {
    return {
        update: query => dispatch(creators.protein.update(i, query)),
        select: protein => dispatch(creators.protein.select(i, protein)),
        unselect: () => dispatch(creators.protein.unselect(i)),
    }
}

const mapDispatchToSequenceProps = (i, type, dispatch) => {
    return {
        edit: () => dispatch(creators.mature.edit(i)),
        update: mature => dispatch(creators.mature.update(i, mature)),
    }
}

const mapDispatchToMappingProps = (i, type, dispatch) => {
    return {
        update: query => dispatch(creators.alignment.update(i, query)),
        fire: (query, sequences) => dispatch(creators.alignment.fire(i, query, sequences)),
        add: alignment => dispatch(creators.alignment.add(i, alignment)),
        remove: index => dispatch(creators.alignment.remove(i, index)),
        cancel: () => dispatch(creators.alignment.cancel(i)),
    }
}

const mapStateToProps = (state, { wrapper, type }) => {
    const [type1, type2] = type == 'hh' ? ['h', 'h'] : ['h', 'v']

    return {
        method: {
            query: state.method.query,
            psimi_id: state.method.psimi_id,
        },
        interactor1: {
            i: 1,
            type: type1,
            accession: state.interactor1.protein.accession,
            editing: state.interactor1.ui.editing,
            uniprot: mapStateToUniprotProps(1, type1, state.interactor1),
            sequence: mapStateToSequenceProps(1, type1, state.interactor1),
            mapping: mapStateToMappingProps(1, type1, state.interactor1),
        },
        interactor2: {
            i: 2,
            type: type2,
            accession: state.interactor2.protein.accession,
            editing: state.interactor2.ui.editing,
            uniprot: mapStateToUniprotProps(2, type2, state.interactor2),
            sequence: mapStateToSequenceProps(2, type2, state.interactor2),
            mapping: mapStateToMappingProps(2, type2, state.interactor2),
        },
        actions: {
            top: wrapper,
            saving: state.ui.saving,
            feedback: state.ui.feedback,
            resetable: ! state.interactor1.ui.processing && ! state.interactor2.ui.processing,
            savable: ! state.interactor1.ui.editing && ! state.interactor2.ui.editing
                    && ! state.interactor1.ui.processing && ! state.interactor2.ui.processing
                    && state.method.psimi_id != null
                    && state.interactor1.protein.accession != null
                    && state.interactor1.name != ''
                    && state.interactor1.start != ''
                    && state.interactor1.stop != ''
                    && state.interactor2.protein.accession != null
                    && state.interactor2.name != ''
                    && state.interactor2.start != ''
                    && state.interactor2.stop != '',
        },
    }
}

const mapDispatchToProps = (dispatch, { type, run_id, pmid }) => {
    const [type1, type2] = type == 'hh' ? ['h', 'h'] : ['h', 'v']

    return {
        method: {
            update: query => dispatch(creators.method.update(query)),
            select: psimi_id => dispatch(creators.method.select(psimi_id)),
            unselect: () => dispatch(creators.method.unselect()),
        },
        interactor1: {
            uniprot: mapDispatchToUniprotProps(1, type1, dispatch),
            sequence: mapDispatchToSequenceProps(1, type1, dispatch),
            mapping: mapDispatchToMappingProps(1, type1, dispatch),
        },
        interactor2: {
            uniprot: mapDispatchToUniprotProps(2, type2, dispatch),
            sequence: mapDispatchToSequenceProps(2, type2, dispatch),
            mapping: mapDispatchToMappingProps(2, type2, dispatch),
        },
        actions: {
            save: () => dispatch(creators.save(run_id, pmid)),
            reset: () => dispatch(creators.reset()),
        },
    }
}

const mergeProps = (props1, props2) => {
    return {
        method: Object.assign(props1.method, props2.method),
        interactor1: Object.assign(props1.interactor1, props2.interactor1, {
            uniprot: Object.assign(props1.interactor1.uniprot, props2.interactor1.uniprot),
            sequence: Object.assign(props1.interactor1.sequence, props2.interactor1.sequence),
            mapping: Object.assign(props1.interactor1.mapping, props2.interactor1.mapping),
        }),
        interactor2: Object.assign(props1.interactor2, props2.interactor2, {
            uniprot: Object.assign(props1.interactor2.uniprot, props2.interactor2.uniprot),
            sequence: Object.assign(props1.interactor2.sequence, props2.interactor2.sequence),
            mapping: Object.assign(props1.interactor2.mapping, props2.interactor2.mapping),
        }),
        actions: Object.assign(props1.actions, props2.actions),
    }
}

export { mapStateToProps, mapDispatchToProps, mergeProps }

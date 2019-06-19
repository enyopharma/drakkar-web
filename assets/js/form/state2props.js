import creators from './creators'

const inject = (alignment, sequences) => {
    return Object.assign(alignment, {
        maxwidth: Math.max(...Object.values(sequences).map(sequence => {
            return sequence.length
        })),
        isoforms: alignment.isoforms.map(isoform => {
            return Object.assign(isoform, {
                sequence: sequences[isoform.accession]
            })
        }),
    })
}

const state2protein = ({ protein }) => {
    if (protein == null) {
        return { sequence: '', chains: [], matures: [] }
    }

    return {
        sequence: protein.sequence,
        chains: protein.chains,
        matures: protein.matures,
    }
}

const state2mature = ({ name, start, stop, protein, mapping, alignment }) => {
    if (name == '' || start == '' || stop == '' || protein == null) {
        return {
            name: '',
            start: '',
            stop: '',
            sequence: '',
            sequences: [],
            domains: [],
            mapping: [],
            alignment: null,
        }
    }

    const sequence = protein.sequence.slice(start - 1, stop)

    const canonical = { [protein.accession]: sequence }

    const sequences = start == 1 && stop == protein.sequence.length
        ? Object.assign(canonical, protein.isoforms)
        : canonical

    const domains = protein.domains.map(domain => {
        return {
            key: domain.key,
            description: domain.description,
            start: domain.start - start + 1,
            stop: domain.stop - start + 1,
            valid: domain.start >= start && domain.stop <= stop,
        }
    })

    return Object.assign({ name, start, stop, sequence, sequences, domains }, {
        mapping: mapping.map(alignment => inject(alignment, sequences)),
        alignment: alignment == null ? null : inject(alignment, sequences),
    })
}

const mapStateToInteractorProps = (i, type, state) => {
    const protein = state2protein(state)
    const mature = state2mature(state)

    return {
        protein: {
            type: type,
            selecting: state.protein == null,
            editable: ! state.processing,
            query: state.qprotein,
            selected: state.protein,
        },
        sequence: {
            editing: state.editing,
            display: {
                name: mature.name,
                start: mature.start,
                stop: mature.stop,
                sequence: protein.sequence,
                valid: ! state.editing,
            },
            toggle: {
                type: type,
                start: mature.start,
                stop: mature.stop,
                width: protein.sequence.length,
                editable: type == 'v' && ! state.editing && ! state.processing,
            },
            editor: {
                sequence: protein.sequence,
                mature: { name: mature.name, start: mature.start, stop: mature.stop },
                matures: protein.matures,
                chains: protein.chains,
            }
        },
        mapping: {
            selecting: state.alignment != null,
            display: {
                type: type,
                mapping: mature.mapping,
            },
            editor: {
                query: state.qalignment,
                mapped: state.mapping.map(alignment => alignment.sequence),
                sequence: mature.sequence,
                domains: mature.domains,
                processing: state.processing,
            },
            modal: {
                i: i,
                type: type,
                alignment: mature.alignment,
            },
        }
    }
}

const mapDispatchToInteractorProps = (i, type, dispatch) => {
    return {
        protein: {
            update: query => dispatch(creators.protein.update(i, query)),
            select: protein => dispatch(creators.protein.select(i, protein)),
            unselect: () => dispatch(creators.protein.unselect(i)),
        },
        sequence: {
            toggle: {
                edit: () => dispatch(creators.mature.edit(i)),
            },
            editor: {
                update: mature => dispatch(creators.mature.update(i, mature)),
            },
        },
        mapping: {
            display: {
                remove: index => dispatch(creators.alignment.remove(i, index)),
            },
            editor: {
                update: query => dispatch(creators.alignment.update(i, query)),
                fire: () => dispatch(creators.alignment.fire(i)),
            },
            modal: {
                add: alignment => dispatch(creators.alignment.add(i, alignment)),
                cancel: () => dispatch(creators.alignment.cancel(i)),
            }
        }
    }
}

const mergeInteractorProps = (i, type, props1, props2) => {
    return {
        i: i,
        type: type,
        protein: Object.assign(props1.protein, props2.protein),
        sequence: {
            editing: props1.sequence.editing,
            display: Object.assign(props1.sequence.display, props2.sequence.display),
            toggle: Object.assign(props1.sequence.toggle, props2.sequence.toggle),
            editor: Object.assign(props1.sequence.editor, props2.sequence.editor),
        },
        mapping: {
            selecting: props1.mapping.selecting,
            display: Object.assign(props1.mapping.display, props2.mapping.display),
            editor: Object.assign(props1.mapping.editor, props2.mapping.editor),
            modal: Object.assign(props1.mapping.modal, props2.mapping.modal),
        }
    }
}

const mapStateToProps = (state, { wrapper, run_id, pmid, type }) => {
    const [type1, type2] = ['h', type == 'hh' ? 'h' : 'v']

    return {
        method: {
            query: state.qmethod,
            selected: state.method,
        },
        interactor1: mapStateToInteractorProps(1, type1, state.interactor1),
        interactor2: mapStateToInteractorProps(2, type2, state.interactor2),
        actions: {
            top: wrapper,
            saving: state.saving,
            feedback: state.feedback,
            resetable: ! state.interactor1.processing && ! state.interactor2.processing,
            savable: ! state.interactor1.editing && ! state.interactor2.editing
                    && ! state.interactor1.processing && ! state.interactor2.processing
                    && state.method != null
                    && state.interactor1.protein != null
                    && state.interactor1.name != ''
                    && state.interactor1.start != ''
                    && state.interactor1.stop != ''
                    && state.interactor2.protein != null
                    && state.interactor2.name != ''
                    && state.interactor2.start != ''
                    && state.interactor2.stop != '',
        },
    }
}

const mapDispatchToProps = (dispatch, { run_id, pmid, type }) => {
    const [type1, type2] = ['h', type == 'hh' ? 'h' : 'v']

    return {
        method: {
            update: query => dispatch(creators.method.update(query)),
            select: method => dispatch(creators.method.select(method)),
            unselect: () => dispatch(creators.method.unselect()),
        },
        interactor1: mapDispatchToInteractorProps(1, type1, dispatch),
        interactor2: mapDispatchToInteractorProps(2, type2, dispatch),
        actions: {
            save: () => dispatch(creators.save(run_id, pmid)),
            reset: () => dispatch(creators.reset()),
        },
    }
}

const mergeProps = (props1, props2, { run_id, pmid, type }) => {
    const [type1, type2] = ['h', type == 'hh' ? 'h' : 'v']

    return {
        method: Object.assign(props1.method, props2.method),
        interactor1: mergeInteractorProps(1, type1, props1.interactor1, props2.interactor1),
        interactor2: mergeInteractorProps(2, type2, props1.interactor2, props2.interactor2),
        actions: Object.assign(props1.actions, props2.actions),
    }
}

export { state2mature, mapStateToProps, mapDispatchToProps, mergeProps }

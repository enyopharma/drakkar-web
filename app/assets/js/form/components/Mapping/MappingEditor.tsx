import React, { useState, useCallback } from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCogs } from '@fortawesome/free-solid-svg-icons/faCogs'

import { fireAlignment } from '../../src/reducer'
import { useAction, useInteractorSelector } from '../../src/hooks'
import { InteractorI, Protein, Isoform, Domain, Sequences, ScaledDomain } from '../../src/types'

import { DomainsFormGroup } from './DomainsFormGroup'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { CoordinatesFormGroup } from '../Shared/CoordinatesFormGroup'

type MappingEditorProps = {
    i: InteractorI
    start: number
    stop: number
    protein: Protein
}

export const MappingEditor: React.FC<MappingEditorProps> = ({ i, start, stop, protein }) => {
    const [query, setQuery] = useState<string>('')

    const sequences = start == 1 && stop == protein.sequence.length
        ? protein.isoforms.reduce(reduceSequence, {})
        : matureSequences(protein, start, stop)

    const domains = scaledDomains(protein, start, stop)

    const sequence = sequences[protein.accession]

    const reset = () => setQuery('')

    const selectCoordinates = useCallback((start: number, stop: number) => {
        setQuery(sequence.slice(start - 1, stop))
    }, [sequence])

    return (
        <React.Fragment>
            <DomainsFormGroupToggle i={i} domains={domains} update={selectCoordinates}>
                Extract feature sequence
            </DomainsFormGroupToggle>
            <CoordinatesFormGroupToggle i={i} sequence={sequence} update={setQuery} >
                Extract sequence to map
            </CoordinatesFormGroupToggle>
            <ExtractFormGroupToggle i={i} sequence={sequence} update={selectCoordinates}>
                Extract sequence to map
            </ExtractFormGroupToggle>
            <div className="row">
                <div className="col">
                    <SequenceInput i={i} query={query} update={setQuery} />
                </div>
            </div>
            <div className="row">
                <div className="col-3 offset-9">
                    <StartAlignmentButton i={i} query={query} sequences={sequences} reset={reset}>
                        <ProcessingIcon i={i} /> Start alignment
                    </StartAlignmentButton>
                </div>
            </div>
        </React.Fragment>
    )
}

type DomainsFormGroupToggle = {
    i: InteractorI
    domains: ScaledDomain[]
    update: (start: number, stop: number) => void
}

const DomainsFormGroupToggle: React.FC<DomainsFormGroupToggle> = ({ i, domains, update, children }) => {
    const processing = useInteractorSelector(i, state => state.processing)

    const selectDomain = useCallback((domain: Domain) => {
        update(domain.start - 1, domain.stop)
    }, [update])

    return (
        <DomainsFormGroup domains={domains} enabled={!processing} select={selectDomain}>
            {children}
        </DomainsFormGroup>
    )
}

type CoordinatesFormGroupToggle = {
    i: InteractorI
    sequence: string
    update: (query: string) => void
}

const CoordinatesFormGroupToggle: React.FC<CoordinatesFormGroupToggle> = ({ i, sequence, update, children }) => {
    const processing = useInteractorSelector(i, state => state.processing)

    return (
        <CoordinatesFormGroup sequence={sequence} enabled={!processing} set={update}>
            {children}
        </CoordinatesFormGroup>
    )
}

type ExtractFormGroupToggle = {
    i: InteractorI
    sequence: string
    update: (start: number, stop: number) => void
}

const ExtractFormGroupToggle: React.FC<ExtractFormGroupToggle> = ({ i, sequence, update, children }) => {
    const processing = useInteractorSelector(i, state => state.processing)

    return (
        <ExtractFormGroup sequence={sequence} enabled={!processing} set={update}>
            {children}
        </ExtractFormGroup>
    )
}

type SequenceInputProps = {
    i: InteractorI
    query: string
    update: (query: string) => void
}

const SequenceInput: React.FC<SequenceInputProps> = ({ i, query, update }) => {
    const processing = useInteractorSelector(i, state => state.processing)

    return (
        <textarea
            className="form-control"
            placeholder="Sequence to map"
            value={query}
            onChange={e => update(e.target.value)}
            readOnly={processing}
        />
    )
}

type StartAlignmentButtonProps = {
    i: InteractorI
    query: string
    sequences: Sequences
    reset: () => void
}

const StartAlignmentButton: React.FC<StartAlignmentButtonProps> = ({ i, query, sequences, reset, children }) => {
    const mapping = useInteractorSelector(i, state => state.mapping)
    const processing = useInteractorSelector(i, state => state.processing)
    const fire = useAction(fireAlignment)

    const isQueryValid = query.trim().length >= 4 && mapping.filter(alignment => {
        return query.toUpperCase().trim() == alignment.sequence.toUpperCase().trim()
    }).length == 0

    const fireAndReset = useCallback(() => {
        fire({ i, query, sequences })
        reset()
    }, [i, query, sequences, fire, reset])

    return (
        <button
            type="button"
            className="btn btn-block btn-primary"
            onClick={() => fireAndReset()}
            disabled={processing || !isQueryValid}
        >
            {children}
        </button>
    )
}

type ProcessingIconProps = {
    i: InteractorI
}

const ProcessingIcon: React.FC<ProcessingIconProps> = ({ i }) => {
    const processing = useInteractorSelector(i, state => state.processing)

    return processing
        ? <span className="spinner-border spinner-border-sm"></span>
        : <FontAwesomeIcon icon={faCogs} />
}

const reduceSequence = (sequences: Sequences, isoform: Isoform) => {
    sequences[isoform.accession] = isoform.sequence
    return sequences
}


const matureSequences = (protein: Protein, start: number, stop: number) => {
    const sequence = protein.sequence.slice(start - 1, stop)

    return { [protein.accession]: sequence }
}

const scaledDomains = (protein: Protein, start: number, stop: number) => {
    return protein.domains.map(domain => {
        return {
            type: domain.type,
            description: domain.description,
            start: domain.start - start + 1,
            stop: domain.stop - start + 1,
            valid: domain.start >= start && domain.stop <= stop,
        }
    })
}

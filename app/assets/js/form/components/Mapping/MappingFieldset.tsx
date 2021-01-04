import React, { useMemo } from 'react'

import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'
import { InteractorI, Resource, Protein, Isoform, Domain, Sequences } from '../../src/types'

const MappingModal = React.lazy(() => import('./MappingModal').then(m => ({ default: m.MappingModal })))
const MappingEditor = React.lazy(() => import('./MappingEditor').then(m => ({ default: m.MappingEditor })))
const MappingDisplay = React.lazy(() => import('./MappingDisplay').then(m => ({ default: m.MappingDisplay })))

type MappingFieldsetProps = {
    i: InteractorI
}

export const MappingFieldset: React.FC<MappingFieldsetProps> = ({ i }) => {
    const { protein_id, start, stop } = useInteractorSelector(i, state => state)

    const resource = protein_id === null ? null : api.select(protein_id)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Mapping</legend>
                {resource == null || start == null || stop == null
                    ? <p>Please select a sequence first.</p>
                    : <MappingSection i={i} resource={resource} start={start} stop={stop} />
                }
            </fieldset>
        </React.Suspense>
    )
}

type MappingSectionProps = {
    i: InteractorI
    resource: Resource<Protein>
    start: number
    stop: number
}

const MappingSection: React.FC<MappingSectionProps> = ({ i, resource, start, stop }) => {
    const protein = resource.read()

    const sequences = useMemo(() => sequencesMap(protein, start, stop), [protein, start, stop])
    const domains = useMemo(() => scaledDomains(protein, start, stop), [protein, start, stop])

    return (
        <React.Fragment>
            <MappingModalToggle i={i} sequences={sequences} />
            <MappingEditor i={i} accession={protein.accession} sequences={sequences} domains={domains} />
            <MappingDisplay i={i} sequences={sequences} />
        </React.Fragment>
    )
}

type MappingModalToggleProps = {
    i: InteractorI
    sequences: Sequences
}

const MappingModalToggle: React.FC<MappingModalToggleProps> = ({ i, sequences }) => {
    const alignment = useInteractorSelector(i, state => state.alignment)

    if (!alignment) return null

    return <MappingModal i={i} sequences={sequences} alignment={alignment} />
}

const reduceSequences = (reduced: Sequences, isoform: Isoform) => {
    reduced[isoform.accession] = isoform.sequence
    return reduced
}

const sequencesMap = (protein: Protein, start: number, stop: number) => {
    return start == 1 && stop == protein.sequence.length
        ? protein.isoforms.reduce(reduceSequences, {})
        : { [protein.accession]: protein.sequence.slice(start - 1, stop) }
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

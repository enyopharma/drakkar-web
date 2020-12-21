import React, { useState } from 'react'

import { proteins as api } from '../../src/api'
import { useInteractorSelector } from '../../src/hooks'
import { InteractorI, Resource, Protein, Isoform, Alignment, Sequences, Coordinates } from '../../src/types'

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

    const coordinates = start == 1 && stop == protein.sequence.length
        ? protein.isoforms.reduce(reduceCoordinate, {})
        : matureCoordinates(protein, start, stop)

    return (
        <React.Fragment>
            <MappingModalToggle i={i} coordinates={coordinates} />
            <MappingEditor i={i} protein={protein} start={start} stop={stop} />
            <MappingDisplay i={i} coordinates={coordinates} />
        </React.Fragment>
    )
}

type MappingModalToggleProps = {
    i: InteractorI
    coordinates: Coordinates
}

const MappingModalToggle: React.FC<MappingModalToggleProps> = ({ i, coordinates }) => {
    const alignment = useInteractorSelector(i, state => state.alignment)

    if (!alignment) return null

    return <MappingModal i={i} coordinates={coordinates} alignment={alignment} />
}

const reduceCoordinate = (reduced: Coordinates, isoform: Isoform) => {
    reduced[isoform.accession] = {
        start: 1,
        stop: isoform.sequence.length,
        length: isoform.sequence.length,
    }
    return reduced
}

const matureCoordinates = (protein: Protein, start: number, stop: number) => {
    const length = stop - start + 1
    return {
        [protein.accession]: { start, stop, length }
    }
}

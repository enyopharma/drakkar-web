import React, { useRef, useCallback } from 'react'
import { proteins as api } from '../src/api'
import { selectProtein, unselectProtein } from '../src/reducer'
import { useInteractorSelector, useAction } from '../src/hooks'
import { InteractorI, ProteinType, SearchType, Resource, Protein } from '../src/types'

import { SearchInput } from './Shared/SearchInput'

const types: Record<ProteinType, SearchType> = {
    'h': 'human',
    'v': 'virus',
}

const classes: Record<ProteinType, string> = {
    'h': 'alert alert-primary',
    'v': 'alert alert-danger',
}

const placeholders: Record<ProteinType, string> = {
    'h': 'Search a human uniprot entry',
    'v': 'Search a viral uniprot entry',
}

const helps: Record<ProteinType, string> = {
    'h': 'You may use + to perform queries with multiple search terms (eg: bile acid + transport)',
    'v': 'You may use + to perform queries with multiple search terms (eg: influenza A + swine + thailand)',
}

const useProtein = (i: InteractorI): [Resource<Protein> | null, (protein_id: number) => void, () => void] => {
    const protein_id = useInteractorSelector(i, state => state.protein_id)

    const select = useAction(selectProtein)
    const unselect = useAction(unselectProtein)

    const resource = useRef<Resource<Protein> | null>(null)

    resource.current = protein_id === null ? null : api.select(protein_id)

    const sselect = useCallback((protein_id: number) => {
        resource.current = api.select(protein_id)
        select({ i, id: protein_id })
    }, [i, select])

    const sunselect = useCallback(() => unselect({ i }), [i, unselect])

    return [resource.current, sselect, sunselect]
}

type ProteinFieldsetProps = {
    i: InteractorI
}

export const ProteinFieldset: React.FC<ProteinFieldsetProps> = ({ i }) => {
    const [resource, select, unselect] = useProtein(i)

    return (
        <React.Suspense fallback={null}>
            <fieldset>
                <legend>Protein</legend>
                <div className="row">
                    <div className="col">
                        {resource === null
                            ? <ProteinSearchInput i={i} select={select} />
                            : <ProteinAlert i={i} resource={resource} unselect={unselect} />
                        }
                    </div>
                </div>
            </fieldset>
        </React.Suspense>
    )
}

type ProteinSearchInputProps = {
    i: InteractorI
    select: (protein_id: number) => void
}

const ProteinSearchInput: React.FC<ProteinSearchInputProps> = ({ i, select }) => {
    const type = useInteractorSelector(i, state => state.type)

    const factory = useCallback((query: string) => api.search(type, query), [type])

    return (
        <SearchInput
            type={types[type]}
            factory={factory}
            select={select}
            placeholder={placeholders[type]}
            help={helps[type]}
        />
    )
}

type ProteinAlertProps = {
    i: InteractorI
    resource: Resource<Protein>
    unselect: () => void
}

const ProteinAlert: React.FC<ProteinAlertProps> = ({ i, resource, unselect }) => {
    const protein = resource.read()

    const processing = useInteractorSelector(i, state => state.processing)

    const label = [protein.current_version ?? protein.version, protein.taxon, protein.name, protein.description].join(' - ')

    return (
        <React.Fragment>
            {protein.obsolete && <ObsoleteProteinAlert protein={protein} />}
            <div className={classes[protein.type]}>
                <strong>{protein.accession}</strong> - {label}
                <button type="button" className="close" onClick={() => unselect()} disabled={processing}>
                    <span>&times;</span>
                </button>
            </div>
        </React.Fragment>
    )
}

type ObsoleteProteinAlertProps = {
    protein: Protein
}

const ObsoleteProteinAlert: React.FC<ObsoleteProteinAlertProps> = ({ protein }) => (
    <div className="alert alert-warning">
        <strong>{protein.accession}</strong> - {protein.version} is now obsolete!
        Please select an up to date protein below:
    </div>
)

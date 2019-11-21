import React, { useState } from 'react'

import { ScaledDomain, Alignment } from '../../src/types'

import { DomainsFormGroup } from './DomainsFormGroup'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { CoordinatesFormGroup } from '../Shared/CoordinatesFormGroup'

type Props = {
    sequence: string,
    domains: ScaledDomain[],
    mapping: Alignment[],
    processing: boolean,
    fire: (query: string) => void,
}

export const MappingEditor: React.FC<Props> = ({ sequence, domains, mapping, processing, fire }) => {
    const [query, setQuery] = useState<string>('')

    const isQueryValid = query.trim() != '' && mapping.filter(alignment => {
        return query.toUpperCase().trim() == alignment.sequence.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        setQuery(sequence.slice(start - 1, stop))
    }

    const selectDomain = domain => {
        setCoordinates(domain.start, domain.stop)
    }

    return (
        <React.Fragment>
            <DomainsFormGroup domains={domains} enabled={!processing} select={selectDomain}>
                Extract feature sequence
            </DomainsFormGroup>
            <CoordinatesFormGroup sequence={sequence} enabled={!processing} set={setQuery}>
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup sequence={sequence} enabled={!processing} set={setCoordinates}>
                Extract sequence to map
            </ExtractFormGroup>
            <div className="row">
                <div className="col">
                    <textarea
                        className="form-control"
                        placeholder="Sequence to map"
                        value={query}
                        onChange={e => setQuery(e.target.value)}
                        readOnly={processing}
                    />
                </div>
            </div>
            <div className="row">
                <div className="col-3 offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => fire(query)}
                        disabled={processing || !isQueryValid}
                    >
                        <span className={processing ? 'spinner-border spinner-border-sm' : 'fas fa-cogs'}></span>
                        &nbsp;
                        Start alignment
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

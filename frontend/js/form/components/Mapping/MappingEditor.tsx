import React from 'react'
import { FaCogs } from 'react-icons/fa'

import { Domain, ScaledDomain, Alignment } from '../../src/types'

import { DomainsFormGroup } from './DomainsFormGroup'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { CoordinatesFormGroup } from '../Shared/CoordinatesFormGroup'

type Props = {
    query: string,
    sequence: string,
    domains: ScaledDomain[],
    mapping: Alignment[],
    processing: boolean,
    update: (query: string) => void,
    fire: (query: string) => void,
}

export const MappingEditor: React.FC<Props> = ({ query, sequence, domains, mapping, processing, update, fire }) => {
    const isQueryValid = query.trim().length >= 4 && mapping.filter(alignment => {
        return query.toUpperCase().trim() == alignment.sequence.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start: number, stop: number) => {
        update(sequence.slice(start - 1, stop))
    }

    const selectDomain = (domain: Domain) => {
        setCoordinates(domain.start, domain.stop)
    }

    return (
        <React.Fragment>
            <DomainsFormGroup domains={domains} enabled={!processing} select={selectDomain}>
                Extract feature sequence
            </DomainsFormGroup>
            <CoordinatesFormGroup sequence={sequence} enabled={!processing} set={update}>
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
                        onChange={e => update(e.target.value)}
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
                        {processing
                            ? <span className="spinner-border spinner-border-sm"></span>
                            : <FaCogs />
                        }
                        &nbsp;
                        Start alignment
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

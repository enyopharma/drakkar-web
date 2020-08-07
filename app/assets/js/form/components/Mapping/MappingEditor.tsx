import React from 'react'
import { FaCogs } from 'react-icons/fa'
import { useAction } from '../../src/hooks'
import { fireAlignment } from '../../src/reducer'
import { Domain, ScaledDomain, Alignment, InteractorI } from '../../src/types'

import { DomainsFormGroup } from './DomainsFormGroup'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { CoordinatesFormGroup } from '../Shared/CoordinatesFormGroup'

type Props = {
    i: InteractorI
    query: string
    canonical: string
    sequences: Record<string, string>
    domains: ScaledDomain[]
    mapping: Alignment[]
    processing: boolean
    update: (sequence: string) => void
}

export const MappingEditor: React.FC<Props> = ({ i, query, canonical, sequences, domains, mapping, processing, update }) => {
    const fire = useAction(fireAlignment)

    const isQueryValid = query.trim().length >= 4 && mapping.filter(alignment => {
        return query.toUpperCase().trim() == alignment.sequence.toUpperCase().trim()
    }).length == 0

    const disabled = processing || !isQueryValid

    const setCoordinates = (start: number, stop: number) => {
        update(canonical.slice(start - 1, stop))
    }

    const selectDomain = (domain: Domain) => {
        setCoordinates(domain.start, domain.stop)
    }

    return (
        <React.Fragment>
            <DomainsFormGroup domains={domains} enabled={!processing} select={selectDomain}>
                Extract feature sequence
            </DomainsFormGroup>
            <CoordinatesFormGroup sequence={canonical} enabled={!processing} set={update}>
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup sequence={canonical} enabled={!processing} set={setCoordinates}>
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
                        onClick={e => fire({ i, query, sequences })}
                        disabled={disabled}
                    >
                        <ProcessingIcon processing={processing} /> Start alignment
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

const ProcessingIcon: React.FC<{ processing: boolean }> = ({ processing }) => processing
    ? <span className="spinner-border spinner-border-sm"></span>
    : <FaCogs />

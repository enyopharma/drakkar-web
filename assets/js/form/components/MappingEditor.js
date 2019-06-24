import React from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import DomainsFormGroup from './DomainsFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ query, sequence, domains, processing, mapping, update, fire }) => {
    const isQueryValid = query.trim() != '' && mapping.filter(alignment => {
        return query.toUpperCase().trim() == alignment.sequence.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        update(sequence.slice(start - 1, stop))
    }

    const selectDomain = domain => {
        setCoordinates(domain.start, domain.stop)
    }

    return (
        <React.Fragment>
            <DomainsFormGroup
                domains={domains}
                enabled={! processing}
                select={selectDomain}
            >
                Extract feature sequence
            </DomainsFormGroup>
            <CoordinatesFormGroup
                sequence={sequence}
                enabled={! processing}
                set={update}
            >
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup
                sequence={sequence}
                enabled={! processing}
                set={setCoordinates}
            >
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
                        onClick={e => fire()}
                        disabled={processing || ! isQueryValid}
                    >
                        {processing
                            ? <span className="spinner-border spinner-border-sm"></span>
                            : <i className="fas fa-cogs" />
                        }
                        &nbsp;
                        Start alignment
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default MappingEditor;

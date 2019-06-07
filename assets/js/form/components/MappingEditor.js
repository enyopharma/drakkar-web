import React, { useState } from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import DomainsFormGroup from './DomainsFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ processing, protein, fire }) => {
    const [query, setQuery] = useState('')

    const isQueryValid = query.trim() != '' && protein.mapping.filter(mapping => {
        return query.toUpperCase().trim() == mapping.sequence.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        setQuery(protein.sequence.slice(start - 1, stop))
    }

    const selectFeature = feature => {
        setCoordinates(feature.start, feature.stop)
    }

    return (
        <React.Fragment>
            <DomainsFormGroup
                domains={protein.domains}
                enabled={! processing}
                select={selectFeature}
            >
                Extract feature sequence
            </DomainsFormGroup>
            <CoordinatesFormGroup
                sequence={protein.sequence}
                enabled={! processing}
                set={setQuery}
            >
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup
                sequence={protein.sequence}
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
                        onChange={e => setQuery(e.target.value)}
                        readOnly={processing}
                    />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => fire(query)}
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

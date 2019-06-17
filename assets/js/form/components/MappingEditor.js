import React from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import DomainsFormGroup from './DomainsFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ query, processing, protein, update, fire }) => {
    const isQueryValid = query.trim() != '' && protein.mapping.filter(mapping => {
        return query.toUpperCase().trim() == mapping.sequence.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        update(protein.sequence.slice(start - 1, stop))
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
                set={update}
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
                        onClick={e => fire(protein.sequences)}
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

import React, { useState } from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ processing, mature, features, mapped, fire }) => {
    const [query, setQuery] = useState('')

    const isQueryValid = query.trim() != ''
        && ! mapped.includes(query.toUpperCase().trim())

    const setCoordinates = (start, stop) => {
        setQuery(mature.sequence.slice(start - 1, stop))
    }

    const selectFeature = feature => {
        setCoordinates(feature.start - mature.start + 1, feature.stop - mature.start + 1)
    }

    return (
        <React.Fragment>
            <FeaturesFormGroup
                start={mature.start}
                stop={mature.stop}
                features={features}
                enabled={! processing}
                select={selectFeature}
            >
                Extract feature sequence
            </FeaturesFormGroup>
            <CoordinatesFormGroup
                sequence={mature.sequence}
                enabled={! processing}
                set={setQuery}
            >
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup
                sequence={mature.sequence}
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

import React, { useState } from 'react'

import AlignmentList from './AlignmentList'
import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingSection = ({ start, stop, protein, mapping, processing, fire, remove }) => {
    const [query, setQuery] = useState('')

    const sequence = protein.sequence.slice(start - 1, stop)

    const canonical = { [protein.accession]: sequence }

    const subjects = start == 1 && stop == protein.sequence.length
        ? Object.assign({}, canonical, protein.isoforms)
        : canonical

    const isQueryValid = query.trim() != '' && mapping.filter(alignment => {
        return alignment.sequence.toUpperCase() == query.toUpperCase().trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        setQuery(sequence.slice(start - 1, stop))
    }

    const selectFeature = feature => {
        setCoordinates(feature.start - start + 1, feature.stop - start + 1)
    }

    const handleClick = () => {
        fire(query.toUpperCase().trim(), subjects)
    }

    return (
        <React.Fragment>
            <FeaturesFormGroup
                start={start}
                stop={stop}
                features={protein.features}
                enabled={! processing}
                select={selectFeature}
            >
                Extract feature sequence
            </FeaturesFormGroup>
            <CoordinatesFormGroup
                sequence={sequence}
                enabled={! processing}
                set={setQuery}
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
                        onClick={handleClick}
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
            <AlignmentList
                type={protein.type}
                subjects={subjects}
                alignments={mapping}
                remove={remove}
            />
        </React.Fragment>
    )
}

export default MappingSection;

import React, { useState } from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ start, stop, protein, mapping, processing, fire }) => {
    const [query, setQuery] = useState('')

    const sequence = protein.sequence.slice(start - 1, stop)

    const canonical = { [protein.accession]: sequence }

    const subjects = start == 1 && stop == protein.sequence.length
        ? Object.assign({}, canonical, protein.isoforms)
        : canonical

    const isQueryValid = query.trim() != '' && mapping.filter(alignment => {
        return alignment.sequence == query.trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        setQuery(sequence.slice(start - 1, stop))
    }

    const selectFeature = feature => {
        setCoordinates(feature.start - start + 1, feature.stop - start + 1)
    }

    const handleClick = () => {
        fire(query, subjects)
    }

//    const fireAlignment = () => {
//        const request = fetch('/jobs/alignments', {
//            method: 'POST',
//            headers: {
//                'accept': 'application/json',
//                'content-type': 'application/json',
//            },
//            body: JSON.stringify({
//                query: query.trim(),
//                subjects: subjects,
//            })
//        })
//
//        request
//            .then(response => response.json(), error => console.log(error))
//            .then(json => setId(json.data.id))
//    }

    return (
        <React.Fragment>
            <FeaturesFormGroup
                start={start}
                stop={stop}
                features={protein.features}
                select={selectFeature}
            >
                Extract feature sequence
            </FeaturesFormGroup>
            <CoordinatesFormGroup sequence={sequence} set={setQuery}>
                Extract sequence to map
            </CoordinatesFormGroup>
            <ExtractFormGroup sequence={sequence} set={setCoordinates}>
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
                        {! processing
                            ? <i className="fas fa-cogs" />
                            : <span className="spinner-border spinner-border-sm"></span>
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

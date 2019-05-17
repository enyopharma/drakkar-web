import fetch from 'cross-fetch'
import { useSocket } from '../../hooks'
import React, { useState } from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ type, interactor, processing, setProcessing, add }) => {
    const [id, setId] = useState('')
    const [query, setQuery] = useState('')

    useSocket(id, (payload) => {
        setId('')
        setProcessing(false)
        console.log(payload)
        add(payload.alignment)
    }, [id])

    const sequence = interactor.protein.sequence.slice(
        interactor.start - 1,
        interactor.stop,
    )

    const canonical = { [interactor.protein.accession]: sequence }

    const subjects = interactor.start == 1 && interactor.stop == interactor.protein.sequence.length
        ? Object.assign({}, canonical, interactor.protein.isoforms)
        : canonical

    const isQueryValid = query.trim() != '' && interactor.mapping.filter(alignment => {
        return alignment.sequence == query.trim()
    }).length == 0

    const setCoordinates = (start, stop) => {
        setQuery(sequence.slice(start - 1, stop))
    }

    const setFeature = feature => {
        setCoordinates(
            feature.start - interactor.start + 1,
            feature.stop - interactor.start + 1,
        )
    }

    const handleClick = () => {
        setProcessing(true)

        const request = fetch('/jobs/alignments', {
            method: 'POST',
            headers: {
                'accept': 'application/json',
                'content-type': 'application/json',
            },
            body: JSON.stringify({
                query: query.trim(),
                subjects: subjects,
            })
        })

        request
            .then(response => response.json(), error => console.log(error))
            .then(json => setId(json.data.id))
    }

    return (
        <React.Fragment>
            <FeaturesFormGroup
                interactor={interactor}
                set={setFeature}
            >
                Extract sequence to map
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

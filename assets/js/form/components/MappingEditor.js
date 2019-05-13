import React, { useState } from 'react'

import MappingList from './MappingList'
import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ type, interactor, processing, setProcessing }) => {
    const sequence = interactor.protein.sequence.slice(
        interactor.start - 1,
        interactor.stop,
    )

    const [target, setTarget] = useState('')

    const setCoordinates = (start, stop) => {
        setTarget(sequence.slice(start - 1, stop))
    }

    const setFeature = feature => {
        setCoordinates(
            feature.start - interactor.start + 1,
            feature.stop - interactor.start + 1,
        )
    }

    const handleClick = () => {
        setProcessing(true)

        setTimeout(() => setProcessing(false), 5000)
    }

    return (
        <React.Fragment>
            <FeaturesFormGroup
                interactor={interactor}
                set={setFeature}
            >
                Extract sequence to map
            </FeaturesFormGroup>
            <CoordinatesFormGroup sequence={sequence} set={setTarget}>
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
                        value={target}
                        onChange={e => setTarget(e.target.value)}
                    />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleClick}
                        disabled={processing || target.trim() == ''}
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
            <MappingList type={type} interactor={interactor} />
        </React.Fragment>
    )
}

export default MappingEditor;

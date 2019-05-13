import React, { useState } from 'react'

import MappingList from './MappingList'
import ExtractFormGroup from './ExtractFormGroup'
import FeaturesFormGroup from './FeaturesFormGroup'
import CoordinatesFormGroup from './CoordinatesFormGroup'

const MappingEditor = ({ type, interactor, processing, setProcessing }) => {
    const max = interactor.stop - interactor.start + 1
    const sequence = interactor.protein.sequence

    const [target, setTarget] = useState('')
    const [error, setError] = useState('')

    const setCoordinates = (start, stop) => {
        if (start == 0) { setError('Invalid coordinates'); return }
        if (start > stop) { setError('Invalid coordinates'); return }
        if (start < interactor.start) { setError('Invalid coordinates'); return }
        if (stop > interactor.stop) { setError('Invalid coordinates'); return }

        const target = sequence.slice(start - 1, stop);

        setTarget(target)
        setError('')
    }

    const setCoordinatesWithOffset = (start, stop) => {
        setCoordinates(
            interactor.start + start -1,
            interactor.start + stop -1,
        )
    }

    const handleClick = () => {
        setProcessing(true)

        setTimeout(() => setProcessing(false), 5000)
    }

    return (
        <React.Fragment>
            {error == ''
                ? <p>Sequence mapping tool</p>
                : <p className="text-danger">{error}</p>
            }
            <FeaturesFormGroup interactor={interactor} select={setCoordinates}>
                Extract sequence
            </FeaturesFormGroup>
            <CoordinatesFormGroup max={max} select={setCoordinatesWithOffset}>
                Extract sequence
            </CoordinatesFormGroup>
            <ExtractFormGroup sequence={sequence} update={setCoordinates}>
                Extract sequence
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

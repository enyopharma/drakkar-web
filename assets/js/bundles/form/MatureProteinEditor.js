import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const MatureProteinEditor = ({ interactor, update, cancel }) => {
    const sequence = interactor.protein.sequence
    const matures = interactor.protein.matures

    const [error, setError] = useState('')
    const [name, setName] = useState(matures.length == 0 ? interactor.protein.name : '')
    const [start, setStart] = useState(matures.length == 0 ? 1 : '')
    const [stop, setStop] = useState(matures.length == 0 ? sequence.length : '')

    const isValid = name.trim() != ''
        && start != ''
        && stop != ''
        && start <= stop
        && matures.filter(m => m.name == name).length == 0
        && matures.filter(m => m.start == start && m.stop == stop).length == 0

    const selectMature = (mature) => {
        update(mature)
        cancel()
    }

    const setCoordinates = (start, stop) => {
        if (start == 0) {
            setError('Invalid subsequence')
            return
        }

        setStart(start)
        setStop(stop)
        setError('')
    }

    const handleValidate = () => {
        selectMature({name: name.trim(), start: start, stop: stop})
    }

    const handleReset = () => {
        setCoordinates(1, sequence.length)
    }

    return (
        <React.Fragment>
            {matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                <React.Fragment>
                    <p>
                        Existing sequences on this uniprot entry:
                    </p>
                    <div className="row">
                        <div className="col">
                            <MatureProteinList matures={matures} select={selectMature} />
                        </div>
                    </div>
                </React.Fragment>
            )}
            {error == ''
                ? <p>Sequence selection tool:</p>
                : <p className="text-danger">{error}</p>
            }
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Name"
                        value={name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={start}
                        update={setStart}
                        max={sequence.length}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={stop}
                        update={setStop}
                        max={sequence.length}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleValidate}
                        disabled={! isValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            <SubsequenceFormGroup sequence={sequence} update={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} update={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={handleReset}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default MatureProteinEditor;

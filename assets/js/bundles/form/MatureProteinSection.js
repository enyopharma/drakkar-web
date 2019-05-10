import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const MatureProteinSection = ({ interactor, processing, update }) => {
    const sequence = interactor.protein.sequence
    const matures = interactor.protein.matures

    const [editing, setEditing] = useState(true)
    const [name, setName] = useState(matures.length == 0 ? interactor.protein.name : '')
    const [start, setStart] = useState(matures.length == 0 ? 1 : '')
    const [stop, setStop] = useState(matures.length == 0 ? sequence.length : '')
    const [error, setError] = useState('')

    const isValid = name.trim() != ''
        && start != ''
        && stop != ''
        && start <= stop
        && matures.filter(m => m.name == name).length == 0
        && matures.filter(m => m.start == start && m.stop == stop).length == 0

    const selectMature = (mature) => {
        update(mature)
        setEditing(false)
    }

    const selectCoordinates = (start, stop) => {
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

    const handleEdit = () => {
        const name = interactor.name
        const start = interactor.start
        const stop = interactor.stop
        update({name: '', start: '', stop: ''})
        setEditing(true)
        setName(name)
        setStart(start)
        setStop(stop)
        setError('')
    }

    const handleReset = () => {
        selectCoordinates(1, sequence.length)
    }

    return ! editing ? (
        <div className="row">
            <div className="col offset-9">
                <button
                    className="btn btn-sm btn-block btn-outline-warning"
                    onClick={handleEdit}
                    disabled={processing}
                >
                    <i className="fas fa-edit" />&nbsp;Edit sequence
                </button>
            </div>
        </div>
    ) : (
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
            <SubsequenceFormGroup sequence={sequence} update={selectCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} update={selectCoordinates}>
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

export default MatureProteinSection;

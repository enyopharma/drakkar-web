import React, { useState } from 'react'

import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const MatureProteinSection = ({ interactor, update }) => {
    const sequence = interactor.protein.sequence
    const matures = interactor.protein.matures

    const [editing, setEditing] = useState(true)
    const [name, setName] = useState(matures.length == 0 ? interactor.protein.name : '')
    const [start, setStart] = useState(matures.length == 0 ? 1 : '')
    const [stop, setStop] = useState(matures.length == 0 ? sequence.length : '')
    const [error, setError] = useState('')

    const isValid = name.trim().length > 0
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
        setStart(start)
        setStop(stop)
        setError('')
    }

    const handleValidate = () => {
        selectMature({name: name.trim(), start: start, stop: stop})
    }

    const handleEdit = () => {
        setEditing(true)
        setName(interactor.name)
        setStart(interactor.start)
        setStop(interactor.stop)
        setError('')
    }

    const handleStartChange = (e) => {
        if (e.target.value == '') { setStart(''); return }

        let value = parseInt(e.target.value)
        if (value < 1) value = 1
        if (value > sequence.length) value = sequence.length
        setStart(value)
    }

    const handleStopChange = (e) => {
        if (e.target.value == '') { setStop(''); return }

        let value = parseInt(e.target.value)
        if (value < 1) value = 1
        if (value > sequence.length) value = sequence.length
        setStop(value)
    }

    const handleReset = () => {
        setStart(1)
        setStop(sequence.length)
        setError('')
    }

    return ! editing ? (
        <div className="row">
            <div className="col offset-9">
                <button
                    className="btn btn-sm btn-block btn-outline-warning"
                    onClick={handleEdit}
                >
                    <i className="fas fa-edit" />&nbsp;Edit sequence
                </button>
            </div>
        </div>
    ) : (
        <React.Fragment>
            <MatureProteinList matures={matures} select={selectMature} />
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
                    <input
                        type="number"
                        min="1"
                        max={sequence.length}
                        className="form-control"
                        placeholder="Start"
                        value={start}
                        onChange={handleStartChange}
                    />
                </div>
                <div className="col-3">
                    <input
                        type="number"
                        min="1"
                        max={sequence.length}
                        className="form-control"
                        placeholder="Stop"
                        value={stop}
                        onChange={handleStopChange}
                    />
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
            <SubsequenceFormGroup sequence={sequence} update={selectCoordinates} error={setError} />
            <ExtractFormGroup sequence={sequence} update={selectCoordinates} error={setError} />
            <div className="row">
                <div className="col-9">
                {error == '' ? null : (
                <p className="text-danger form-control-plaintext">{error}</p>
                )}
                </div>
                <div className="col-3">
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

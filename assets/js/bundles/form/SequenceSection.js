import React, { useState } from 'react'

import Conditional from './Conditional'
import MappingImg from './MappingImg'
import ResetFormGroup from './Mature/ResetFormGroup'
import ExtractFormGroup from './Mature/ExtractFormGroup'
import MatureProteinList from './Mature/MatureProteinList'
import SubsequenceFormGroup from './Mature/SubsequenceFormGroup'

const validations = (protein) => [
    mature => [mature.name.length > 0, 'name is empty'],
    mature => [mature.start >= 1, 'start is lower than 1'],
    mature => [mature.start <= mature.stop, 'start is greater than stop'],
    mature => [mature.stop <= protein.sequence.length, [
        'stop is greater than', protein.sequence.length, '(full length)'
    ].join(' ')],
    mature => {
        const matches = protein.matures.filter(m => {
            return mature.start == m.start && mature.stop == m.stop
        })

        if (matches.length == 0) return [true]
        if (matches[0].name == mature.name) return [true]

        return [false, [
            'sequence with positions', mature.start, '-', mature.stop, 'is named', matches[0].name
        ].join(' ')]
    },
    mature => {
        const matches = protein.matures.filter(m => {
            return mature.name == m.name
        })

        if (matches.length == 0) return [true]
        if (matches[0].start == mature.start && matches[0].stop == mature.stop) return [true]

        return [false, [
            'sequence named', mature.name, 'has positions', matches[0].start, '-', matches[0].stop
        ].join(' ')];
    },
]

const MatureProtein = ({ type, interactor, update }) => {
    const vs = validations(interactor.protein)

    const [editing, setEditing] = useState(false)
    const [error, setError] = useState('')
    const [name, setName] = useState(interactor.name)
    const [start, setStart] = useState(interactor.start)
    const [stop, setStop] = useState(interactor.stop)

    const setCoordinates = (start, stop) => {
        setStart(start)
        setStop(stop)
    }

    const select = mature => {
        setEditing(false)
        setError('')
        setName(mature.name)
        setStart(mature.start)
        setStop(mature.stop)
        update(mature)
    }

    const isCancelActive = () => {
        return name != interactor.protein.name
            || start != 1
            || stop != interactor.protein.sequence.length
    }

    const handleEdit = () => {
        setEditing(true)

        if (name == interactor.protein.name) {
            setName('')
        }
    }

    const handleValidate = () => {
        const mature = {name: name.trim(), start: parseInt(start), stop: parseInt(stop)}

        for (let i = 0; i < vs.length; i++) {
            const v = vs[i](mature)
            if (! v[0]) { setError(v[1]); return; }
        }

        setEditing(false)
        setError('')
        update(mature)
    }

    const handleCancel = () => {
        select({
            name: interactor.protein.name,
            start: 1,
            stop: interactor.protein.sequence.length,
        })
    }

    return (
        <React.Fragment>
            <h4>Sequence</h4>
            <Conditional state={type == 'v'}>
                <Conditional state={error != ''}>
                    <p className="text-danger">{error}</p>
                </Conditional>
                <div className="form-group row">
                    <div className="col-3">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Name"
                            value={name}
                            onChange={e => setName(e.target.value)}
                            readOnly={! editing}
                        />
                    </div>
                    <div className="col-3">
                        <input
                            type="number"
                            min="1"
                            max={stop}
                            className="form-control"
                            placeholder="Start"
                            value={start}
                            onChange={e => setStart(e.target.value)}
                            readOnly={! editing}
                        />
                    </div>
                    <div className="col-3">
                        <input
                            type="number"
                            min={start}
                            max={interactor.protein.sequence.length}
                            className="form-control"
                            placeholder="Stop"
                            value={stop}
                            onChange={e => setStop(e.target.value)}
                            readOnly={! editing}
                        />
                    </div>
                    <div className="col">
                        <Conditional state={! editing}>
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={handleEdit}
                            >
                                Edit sequence
                            </button>
                        </Conditional>
                        <Conditional state={editing}>
                            <button
                                type="button"
                                className="btn btn-block btn-primary"
                                onClick={handleValidate}
                            >
                                Validate
                            </button>
                        </Conditional>
                    </div>
                    <div className="col">
                        <button
                            type="button"
                            className="btn btn-block btn-warning"
                            onClick={handleCancel}
                            disabled={! isCancelActive()}
                        >
                            Cancel
                        </button>
                    </div>
                </div>
                <Conditional state={editing}>
                    <Conditional state={interactor.protein.matures.length > 0}>
                        <MatureProteinList matures={interactor.protein.matures} select={select} />
                    </Conditional>
                    <SubsequenceFormGroup
                        sequence={interactor.protein.sequence}
                        update={setCoordinates}
                        error={setError}
                    />
                    <ExtractFormGroup
                        sequence={interactor.protein.sequence}
                        update={setCoordinates}
                        error={setError}
                    />
                    <ResetFormGroup
                        sequence={interactor.protein.sequence}
                        update={setCoordinates}
                    />
                </Conditional>
            </Conditional>
            <div className="form-group row">
                <div className="col">
                    <textarea
                        className="form-control"
                        value={interactor.protein.sequence.slice(start - 1, stop)}
                        rows={5}
                        readOnly
                    />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <MappingImg
                        type={type}
                        start={start}
                        stop={stop}
                        length={interactor.protein.sequence.length}
                    />
                </div>
            </div>
        </React.Fragment>
    )
}

export default MatureProtein;

import React, { useState, useEffect } from 'react'

import CoordinateField from './CoordinateField'

const CoordinatesFormGroup = ({ sequence, enabled = true, set, children }) => {
    const [start, setStart] = useState('')
    const [stop, setStop] = useState('')
    const [valid, setValid] = useState(true)

    useEffect(() => setValid(true), [start, stop])

    const submit = () => {
        start <= stop
            ? set(sequence.slice(start - 1, stop))
            : setValid(false)
    }

    return (
        <div className="row">
            <div className="col">
                <CoordinateField value={start} set={setStart} max={sequence.length} valid={valid}>
                    Start
                </CoordinateField>
            </div>
            <div className="col">
                <CoordinateField value={stop} set={setStop} max={sequence.length} valid={valid}>
                    Stop
                </CoordinateField>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={!enabled || start == '' || stop == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default CoordinatesFormGroup

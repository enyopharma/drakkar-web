import React, { useState } from 'react'

import CoordinateField from './CoordinateField'

const CoordinatesFormGroup = ({ max, select, children }) => {
    const [start, setStart] = useState('')
    const [stop, setStop] = useState('')

    const handleClick = () => {
        select(start, stop)
    }

    return (
        <div className="row">
            <div className="col">
                <CoordinateField value={start} update={setStart} max={max}>
                    Start
                </CoordinateField>
            </div>
            <div className="col">
                <CoordinateField value={stop} update={setStop} max={max}>
                    Stop
                </CoordinateField>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={handleClick}
                    disabled={start == '' || stop == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default CoordinatesFormGroup

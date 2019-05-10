import React, { useState } from 'react'

import extract from '../extract.js'

const ExtractFormGroup = ({ sequence, update, children }) => {
    const [from, setFrom] = useState('')
    const [to, setTo] = useState('')

    const handleClick = () => {
        if (from.trim() == '' || to.trim() == '') return

        const [start1, stop1] = extract(sequence, from.trim())
        const [start2, stop2] = extract(sequence, to.trim())

        stop1 >= start2 ? update(0, 0) : update(start1, stop2)
    }

    return (
        <div className="row">
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="From..."
                    value={from}
                    onChange={e => setFrom(e.target.value)}
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="To..."
                    value={to}
                    onChange={e => setTo(e.target.value)}
                />
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={handleClick}
                    disabled={from.trim() == '' || to.trim() == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default ExtractFormGroup

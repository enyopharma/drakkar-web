import React, { useState } from 'react'

import extract from '../extract.js'

const ExtractFormGroup = ({ sequence, update, error, children }) => {
    const [from, setFrom] = useState('')
    const [to, setTo] = useState('')

    const handleClick = () => {
        if (from.trim() == '' || to.trim() == '') return

        const [start1, stop1] = extract(from, sequence)
        const [start2, stop2] = extract(to, sequence)

        if (start1 == 0 || start2 == 0 || stop1 >= start2) {
            error('subsequence not found')
            return
        }

        update(start1, stop2)
        setFrom('')
        setTo('')
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
                    disabled={from == '' || to == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default ExtractFormGroup

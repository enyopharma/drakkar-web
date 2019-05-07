import React, { useState } from 'react'

const ExtractFormGroup = ({ sequence, update, error }) => {
    const [from, setFrom] = useState('')
    const [to, setTo] = useState('')

    const extract = () => {
        const source = sequence.toLowerCase()
        const target1 = from.trim().toLowerCase()
        const target2 = to.trim().toLowerCase()
        const start1 = source.indexOf(target1) + 1
        const start2 = source.indexOf(target2) + 1
        const stop1 = start1 + target1.length - 1
        const stop2 = start2 + target2.length - 1

        start1 == 0 || start2 == 0 || stop1 >= start2
            ? error('subsequence not found')
            : update(start1, stop2)
    }

    return (
        <div className="form-group row">
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
                <button type="button" className="btn btn-block btn-info" onClick={e => extract()}>
                    Extract coordinates
                </button>
            </div>
        </div>
    )
}

export default ExtractFormGroup

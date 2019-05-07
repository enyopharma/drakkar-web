import React, { useState } from 'react'

const SubsequenceFormGroup = ({ sequence, update, error }) => {
    const [sub, setSub] = useState('')

    const extract = () => {
        const source = sequence.toLowerCase()
        const target = sub.trim().toLowerCase()
        const start = source.indexOf(target) + 1
        const stop = start + target.length - 1

        start == 0
            ? error('subsequence not found')
            : update(start, stop)
    }

    return (
        <div className="form-group row">
            <div className="col-9">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Subsequence..."
                    value={sub}
                    onChange={e => setSub(e.target.value)}
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

export default SubsequenceFormGroup

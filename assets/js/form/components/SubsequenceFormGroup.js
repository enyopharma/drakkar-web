import React, { useState } from 'react'

import { extract } from '../shared'

const SubsequenceFormGroup = ({ sequence, update, children }) => {
    const [subsequence, setSubsequence] = useState('')

    const handleClick = () => {
        if (subsequence.trim() == '') return

        const [start, stop] = extract(sequence, subsequence.trim())

        update(start, stop)
    }

    return (
        <div className="row">
            <div className="col-9">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Subsequence..."
                    value={subsequence}
                    onChange={e => setSubsequence(e.target.value)}
                />
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={handleClick}
                    disabled={subsequence.trim() == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default SubsequenceFormGroup

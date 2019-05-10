import React, { useState } from 'react'

import extract from '../extract.js'

const SubsequenceFormGroup = ({ sequence, update, error, children }) => {
    const [subsequence, setSubsequence] = useState('')

    const handleClick = () => {
        if (subsequence.trim() == '') return

        const [start, stop] = extract(subsequence, sequence)

        if (start == 0) {
            error('subsequence not found')
            return
        }

        update(start, stop)
        setSubsequence('')
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
                    disabled={subsequence == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default SubsequenceFormGroup

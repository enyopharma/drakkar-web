import React, { useState, useEffect } from 'react'
import { extract } from '../../src/shared'

type Props = {
    sequence: string,
    enabled?: boolean,
    set: (start: number, stop: number) => void,
}

export const SubsequenceFormGroup: React.FC<Props> = ({ sequence, enabled = true, set, children }) => {
    const [subsequence, setSubsequence] = useState<string>('')
    const [valid, setValid] = useState<boolean>(true)

    useEffect(() => setValid(true), [subsequence])

    const classes = 'form-control' + (valid ? '' : ' is-invalid')
    const disabled = !enabled || subsequence.trim() == ''

    const submit = () => {
        if (subsequence.trim() == '') return

        const [start, stop] = extract(sequence, subsequence.trim())

        start > 0 ? set(start, stop) : setValid(false)
    }

    return (
        <div className="row">
            <div className="col-9">
                <input
                    type="text"
                    className={classes}
                    placeholder="Subsequence..."
                    value={subsequence}
                    onChange={e => setSubsequence(e.target.value)}
                />
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={disabled}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

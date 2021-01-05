import React, { useState, useCallback } from 'react'
import { extract } from '../../src/shared'

type Props = {
    sequence: string
    enabled?: boolean
    update: (start: number, stop: number) => void
}

export const SubsequenceFormGroup: React.FC<Props> = ({ sequence, enabled = true, update, children }) => {
    const [subsequence, setSubsequence] = useState<string>('')

    const [start, stop] = extract(sequence, subsequence.trim())

    const invalid = start >= 0 && stop >= 0 && start > stop
    const disabled = !enabled || invalid || start === -1 || stop === -1

    const classes = !invalid ? 'form-control' : 'form-control is-invalid'

    const submit = useCallback(() => update(start, stop), [start, stop, update])

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
                    onClick={() => submit()}
                    disabled={disabled}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

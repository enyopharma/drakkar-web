import React, { useState, useCallback } from 'react'
import { extract } from '../../src/shared'

type Props = {
    sequence: string
    enabled?: boolean
    update: (start: number, stop: number) => void
}

export const ExtractFormGroup: React.FC<Props> = ({ sequence, enabled = true, update, children }) => {
    const [from, setFrom] = useState<string>('')
    const [to, setTo] = useState<string>('')

    const c1 = extract(sequence, from.trim())
    const c2 = extract(sequence, to.trim())

    const start = c1[0]
    const stop = c2[1]

    const invalid = start >= 0 && stop >= 0 && start > stop
    const disabled = !enabled || invalid || start === -1 || stop === -1

    const classes = !invalid ? 'form-control' : 'form-control is-invalid'

    const submit = useCallback(() => update(start, stop), [start, stop, update])

    return (
        <div className="row">
            <div className="col">
                <input
                    type="text"
                    className={classes}
                    placeholder="From..."
                    value={from}
                    onChange={e => setFrom(e.target.value)}
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className={classes}
                    placeholder="To..."
                    value={to}
                    onChange={e => setTo(e.target.value)}
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

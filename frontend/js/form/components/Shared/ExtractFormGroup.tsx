import React, { useState, useEffect } from 'react'

import { extract } from '../../src/utils/shared'

type Props = {
    sequence: string,
    enabled?: boolean,
    set: (start: number, stop: number) => void,
}

export const ExtractFormGroup: React.FC<Props> = ({ sequence, enabled = true, set, children }) => {
    const [from, setFrom] = useState<string>('')
    const [to, setTo] = useState<string>('')
    const [valid, setValid] = useState<boolean>(true)

    useEffect(() => setValid(true), [from, to])

    const submit = () => {
        if (from.trim() == '' || to.trim() == '') return

        const [start1, stop1] = extract(sequence, from.trim())
        const [start2, stop2] = extract(sequence, to.trim())

        start1 > 0 && start2 > 0 && stop1 < start2
            ? set(start1, stop2)
            : setValid(false)
    }

    return (
        <div className="row">
            <div className="col">
                <input
                    type="text"
                    className={'form-control' + (valid ? '' : ' is-invalid')}
                    placeholder="From..."
                    value={from}
                    onChange={e => setFrom(e.target.value)}
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className={'form-control' + (valid ? '' : ' is-invalid')}
                    placeholder="To..."
                    value={to}
                    onChange={e => setTo(e.target.value)}
                />
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={e => submit()}
                    disabled={!enabled || from.trim() == '' || to.trim() == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

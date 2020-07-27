import React, { useState, useEffect } from 'react'

import { CoordinateField } from './CoordinateField'

type Props = {
    sequence: string,
    enabled: boolean,
    set: (sequence: string) => void,
}

export const CoordinatesFormGroup: React.FC<Props> = ({ sequence, enabled = true, set, children }) => {
    const [start, setStart] = useState<number | null>(null)
    const [stop, setStop] = useState<number | null>(null)
    const [valid, setValid] = useState<boolean>(true)

    useEffect(() => setValid(true), [start, stop])

    const disabled = !enabled || start == null || stop == null

    const submit = () => {
        start != null && stop != null && start <= stop
            ? set(sequence.slice(start - 1, stop))
            : setValid(false)
    }

    return (
        <div className="row">
            <div className="col">
                <CoordinateField
                    value={start}
                    set={setStart}
                    max={sequence.length}
                    valid={valid}
                    placeholder="Start"
                />
            </div>
            <div className="col">
                <CoordinateField
                    value={stop}
                    set={setStop}
                    max={sequence.length}
                    valid={valid}
                    placeholder="Stop"
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
        </div >
    )
}

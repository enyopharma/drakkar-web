import React, { useState, useCallback } from 'react'

import { CoordinateField } from './CoordinateField'

type Props = {
    sequence: string
    enabled: boolean
    update: (sequence: string) => void
}

export const CoordinatesFormGroup: React.FC<Props> = ({ sequence, enabled = true, update, children }) => {
    const [start, setStart] = useState<number | null>(null)
    const [stop, setStop] = useState<number | null>(null)

    const invalid = start != null && stop != null && start > stop
    const disabled = !enabled || invalid || start === null || stop === null

    const submit = useCallback(() => {
        if (start === null) return
        if (stop === null) return
        update(sequence.slice(start - 1, stop))
    }, [start, stop, sequence, update])

    return (
        <div className="row">
            <div className="col">
                <CoordinateField
                    value={start}
                    update={setStart}
                    max={sequence.length}
                    valid={!invalid}
                    placeholder="Start"
                />
            </div>
            <div className="col">
                <CoordinateField
                    value={stop}
                    update={setStop}
                    max={sequence.length}
                    valid={!invalid}
                    placeholder="Stop"
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
        </div >
    )
}

import React from 'react'

import { InteractorI } from '../../src/types'
import { useInteractorSelector } from '../../src/hooks'

const style = {
    overflowWrap: 'break-word',
    height: '120px',
    overflowX: 'hidden',
    overflowY: 'scroll',
    fontSize: '15px',
    lineHeight: '22.5px',
    padding: '6px 12px',
    color: '#495057',
    border: '1px solid #ced4da',
    backgroundColor: '#e9ecef',
} as React.CSSProperties

type SequenceTextareaProps = {
    i: InteractorI
    sequence: string
}

export const SequenceTextarea: React.FC<SequenceTextareaProps> = ({ i, sequence }) => {
    const start = useInteractorSelector(i, state => state.start)
    const stop = useInteractorSelector(i, state => state.stop)

    const before = start == null ? '' : sequence.slice(0, start - 1)
    const after = stop == null ? '' : sequence.slice(stop, sequence.length)
    const slice = start == null || stop == null ? sequence : sequence.slice(start - 1, stop)

    return (
        <div style={style}>
            {before == '' && after == '' ? slice : (
                <React.Fragment>
                    {before}<strong>{slice}</strong>{after}
                </React.Fragment>
            )}
        </div>
    )
}

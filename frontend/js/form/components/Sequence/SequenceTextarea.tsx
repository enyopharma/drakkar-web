import React from 'react'

type Props = {
    sequence: string,
    start: number | null,
    stop: number | null,
}

export const SequenceTextarea: React.FC<Props> = ({ sequence, start, stop }) => {
    const before = start == null ? '' : sequence.slice(0, start - 1)
    const after = stop == null ? '' : sequence.slice(stop, sequence.length)
    const slice = start == null || stop == null ? sequence : sequence.slice(start - 1, stop)

    return (
        <div style={{
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
        }}>
            {before == '' && after == '' ? slice : (
                <React.Fragment>
                    {before}<strong>{slice}</strong>{after}
                </React.Fragment>
            )}
        </div>
    )
}

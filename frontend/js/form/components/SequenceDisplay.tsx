import React from 'react'

import { Mature } from '../types'

type Props = {
    current: Mature,
    sequence: string,
    valid: boolean,
}

export const SequenceDisplay: React.FC<Props> = ({ current, sequence, valid }) => {
    const before = current == null ? '' : sequence.slice(0, current.start - 1)
    const after = current == null ? '' : sequence.slice(current.stop, sequence.length)
    const slice = current == null ? sequence : sequence.slice(current.start - 1, current.stop)

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Name"
                        value={current.name}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Start"
                        value={current.start == null ? '' : current.start}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Stop"
                        value={current.stop == null ? '' : current.stop}
                        readOnly
                    />
                </div>
                <div className="col">
                    {valid ? (
                        <button className="btn btn-block btn-outline-success" disabled>
                            <span className="fas fa-check"></span>&nbsp;Sequence is valid
                        </button>
                    ) : (
                            <button className="btn btn-block btn-outline-danger" disabled>
                                <span className="fas fa-exclamation-triangle"></span>
                                &nbsp;
                                Please select a sequence.
                        </button>
                        )}
                </div>
            </div>
            <div className="row">
                <div className="col">
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
                </div>
            </div>
        </React.Fragment>
    )
}

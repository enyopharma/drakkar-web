import React from 'react'

const SequenceDisplay = ({ current, sequence, valid }) => {
    const before = current.start == '' ? '' : sequence.slice(0, current.start - 1)
    const after = current.stop == '' ? '' : sequence.slice(current.stop, sequence.length)
    const slice = current.start == '' && current.stop == '' ? sequence : sequence.slice(
        current.start - 1,
        current.stop
    )

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
                        value={current.start}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Stop"
                        value={current.stop}
                        readOnly
                    />
                </div>
                <div className="col">
                    {valid ? (
                        <button className="btn btn-block btn-outline-success" disabled>
                            <i className="fas fa-check" />&nbsp;Sequence is valid
                        </button>
                    ) : (
                        <button className="btn btn-block btn-outline-danger" disabled>
                            <i className="fas fa-exclamation-triangle" />
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

export default SequenceDisplay;

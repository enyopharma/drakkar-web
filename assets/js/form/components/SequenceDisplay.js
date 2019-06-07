import React from 'react'

const SequenceDisplay = ({ source, protein, valid }) => {
    const before = protein.start != ''
        ? source.sequence.slice(0, protein.start - 1)
        : ''

    const after = protein.stop != ''
        ? source.sequence.slice(protein.stop, source.sequence.length)
        : ''

    const sequence = (protein.start != '' && protein.stop != '')
        ? source.sequence.slice(protein.start - 1, protein.stop)
        : source.sequence;

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Name"
                        value={protein.name}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Start"
                        value={protein.start}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Stop"
                        value={protein.stop}
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
                        {before == '' && after == '' ? sequence : (
                            <React.Fragment>
                                {before}<strong>{sequence}</strong>{after}
                            </React.Fragment>
                        )}
                    </div>
                </div>
            </div>
        </React.Fragment>
    )
}

export default SequenceDisplay;

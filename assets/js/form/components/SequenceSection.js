import React, { useState } from 'react'

import MatureProteinList from './MatureProteinList'
import MatureProteinEditor from './MatureProteinEditor'

const SequenceSection = ({ type, interactor, update, editing, processing, setEditing }) => {
    const start = interactor.start
    const stop = interactor.stop
    const sequence = interactor.protein.sequence

    const before = start == '' ? '' : sequence.slice(0, start - 1)
    const after = stop == '' ? '' : sequence.slice(stop, sequence.length)
    const selected = (start == '' || stop == '') ? sequence : sequence.slice(
        start - 1,
        stop,
    )

    return (
        <React.Fragment>
            <h4>Sequence</h4>
            <div className="row">
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Name"
                        value={interactor.name}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Start"
                        value={interactor.start}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Stop"
                        value={interactor.stop}
                        readOnly
                    />
                </div>
                <div className="col">
                    {editing ? (
                        <button className="btn btn-block btn-outline-danger" disabled>
                            <i className="fas fa-exclamation-triangle" />
                            &nbsp;
                            Please select a sequence.
                        </button>
                    ) : (
                        <button className="btn btn-block btn-outline-success" disabled>
                            <i className="fas fa-check" />&nbsp;Sequence is valid
                        </button>
                    )}
                </div>
            </div>
            <div className="form-group row">
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
                        {before == '' && after == '' ? sequence :(
                            <React.Fragment>
                                {before}<strong>{selected}</strong>{after}
                            </React.Fragment>
                        )}
                    </div>
                </div>
            </div>
            {type == 'h' || editing ? null : (
                <div className="row">
                    <div className="col offset-9">
                        <button
                            className="btn btn-sm btn-block btn-outline-warning"
                            onClick={e => setEditing(true)}
                            disabled={processing}
                        >
                            <i className="fas fa-edit" />&nbsp;Edit sequence
                        </button>
                    </div>
                </div>
            )}
            {type == 'h' || ! editing ? null : (
                <MatureProteinEditor
                    interactor={interactor}
                    update={update}
                    cancel={e => setEditing(false)}
                />
            )}
        </React.Fragment>
    )
}

export default SequenceSection;
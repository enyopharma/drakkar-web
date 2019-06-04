import React from 'react'

import MappingImg from './MappingImg'
import MatureProtein from './MatureProtein'

const SequenceSection = ({ name, start, stop, protein, editing, editable, edit, update }) => {
    const before = start == '' ? '' : protein.sequence.slice(0, start - 1)
    const after = stop == '' ? '' : protein.sequence.slice(stop, protein.sequence.length)
    const selected = (start == '' || stop == '') ? protein.sequence : protein.sequence.slice(
        start - 1,
        stop,
    )

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Name"
                        value={name}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Start"
                        value={start}
                        readOnly
                    />
                </div>
                <div className="col">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Stop"
                        value={stop}
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
                        {before == '' && after == '' ? protein.sequence :(
                            <React.Fragment>
                                {before}<strong>{selected}</strong>{after}
                            </React.Fragment>
                        )}
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <MappingImg
                        type={protein.type}
                        start={start}
                        stop={stop}
                        length={protein.sequence.length}
                    />
                </div>
                <div className="col-1">
                    <button
                        className="btn btn-block btn-warning"
                        onClick={edit}
                        disabled={! editable}
                    >
                        <i className="fas fa-edit" />
                    </button>
                </div>
            </div>
            {! editing ? null : (
                <MatureProtein
                    name={name}
                    start={start}
                    stop={stop}
                    protein={protein}
                    update={update}
                />
            )}
        </React.Fragment>
    )
}

export default SequenceSection;

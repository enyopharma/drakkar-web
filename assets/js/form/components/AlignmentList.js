import React, { useState } from 'react'

import MappingImg from './MappingImg'

const AlignmentList = ({ type, interactor, remove }) => {
    const length = interactor.protein.sequence.length
    const start = interactor.start == '' ? 1 : interactor.start
    const stop = interactor.stop == '' ? length : interactor.stop

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <MappingImg type={type} start={start} stop={stop} length={length} />
                </div>
            </div>
            {interactor.mapping.length == 0 ? (
                <p>
                    No mapping yet.
                </p>
            ) : interactor.mapping.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <div className="input-group">
                            <input
                                type="text"
                                className="form-control"
                                value={alignment.sequence}
                                readOnly
                            />
                            <div className="input-group-append">
                                <button
                                    type="button"
                                    className="btn btn-block btn-warning"
                                    onClick={() => remove(i)}
                                >
                                    <i className="fas fa-trash" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default AlignmentList;

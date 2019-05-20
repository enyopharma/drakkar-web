import React, { useState } from 'react'

const AlignmentList = ({ type, interactor, remove }) => {
    const length = interactor.protein.sequence.length
    const start = interactor.start == '' ? 1 : interactor.start
    const stop = interactor.stop == '' ? length : interactor.stop

    return (
        <React.Fragment>
            {interactor.mapping.length == 0 ? (
                <p>
                    No mapping yet.
                </p>
            ) : interactor.mapping.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <input
                            type="text"
                            className="form-control"
                            value={alignment.sequence}
                            readOnly
                        />
                    </div>
                    <div className="col-1">
                        <button
                            type="button"
                            className="btn btn-block btn-warning"
                            onClick={() => remove(i)}
                        >
                            <i className="fas fa-trash" />
                        </button>
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default AlignmentList;

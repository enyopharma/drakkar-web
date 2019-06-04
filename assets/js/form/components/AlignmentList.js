import React from 'react'

import IsoformList from './IsoformList'

const AlignmentList = ({ type, subjects, alignments, remove }) => {
    return alignments.length == 0 ? (
        <p>
            No alignment yet.
        </p>
    ) : (
        <React.Fragment>
            {alignments.map((alignment, i) => (
                <div key={i} className="row">
                    <div className="col">
                        <div className="card">
                            <div className="card-header">
                                <div className="row">
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
                                            className="btn btn-block btn-warning"
                                            onClick={() => remove(i)}
                                        >
                                            <i className="fas fa-trash" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <IsoformList
                                type={type}
                                subjects={subjects}
                                isoforms={alignment.isoforms}
                                remove={(...idxs) => remove(i, ...idxs)}
                            />
                        </div>
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default AlignmentList

import React from 'react'

import IsoformList from './IsoformList'

const AlignmentList = ({ type, subjects, alignments, removeAlignment, removeIsoform, removeOccurence }) => {
    return alignments.length == 0 ? (
        <p>
            No alignment yet.
        </p>
    ) : (
        <div className="card">
        <ul className="list-group list-group-flush">
            {alignments.map((alignment, i) => (
                <li key={i} className="list-group-item">
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
                                onClick={() => removeAlignment(i)}
                            >
                                <i className="fas fa-trash" />
                            </button>
                        </div>
                    </div>
                    <IsoformList
                        type={type}
                        subjects={subjects}
                        isoforms={alignment.isoforms}
                        removeIsoform={j => removeIsoform(i, j)}
                        removeOccurence={(j, k) => removeOccurence(i, j, k)}
                    />
                </li>
            ))}
        </ul>
        </div>
    )
}

export default AlignmentList

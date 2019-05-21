import React, { useState } from 'react'

const Mapping = ({ start, stop, protein, mapping, removeAlignment }) => (
    <React.Fragment>
        {mapping.length == 0 ? (
            <p>
                No alignment yet.
            </p>
        ) : mapping.map((alignment, i) => (
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
                        onClick={() => removeAlignment(i)}
                    >
                        <i className="fas fa-trash" />
                    </button>
                </div>
            </div>
        ))}
    </React.Fragment>
)

export default Mapping;

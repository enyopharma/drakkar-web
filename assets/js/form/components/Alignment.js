import React from 'react'

import Isoform from './Isoform'

const Alignment = ({ type, alignment, remove }) => {
    const length = Math.max(...alignment.isoforms.map(isoform => {
        return isoform.sequence.length
    }))

    return (
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
                            onClick={(e) => remove()}
                        >
                            <i className="fas fa-trash" />
                        </button>
                    </div>
                </div>
            </div>
            <ul className="list-group list-group-flush">
                {alignment.isoforms.map((isoform, j) => (
                    <li key={j} className="list-group-item">
                        <Isoform
                            type={type}
                            length={length}
                            isoform={isoform}
                            remove={(...idxs) => remove(j, ...idxs)}
                        />
                    </li>
                ))}
            </ul>
        </div>
    )
}

export default Alignment

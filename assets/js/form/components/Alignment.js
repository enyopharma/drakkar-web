import React from 'react'

import MappingImg from './MappingImg'

const Alignment = ({ type, width, subjects, alignment, remove }) => {
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
                {alignment.results.map((result, j) => (
                    <li key={j} className="list-group-item">
                        <h4>
                            {result.accession}
                        </h4>
                        <p>
                            <MappingImg
                                type={type}
                                start={1}
                                stop={subjects[result.accession].length}
                                width={width}
                            />
                        </p>
                        <ul className="list-unstyled">
                            {result.occurences.map((occurence, k) => (
                                <li key={k}>
                                    <MappingImg
                                        type={type}
                                        start={occurence.start}
                                        stop={occurence.stop}
                                        width={width}
                                    />
                                </li>
                            ))}
                        </ul>
                    </li>
                ))}
            </ul>
        </div>
    )
}

export default Alignment

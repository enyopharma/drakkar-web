import React from 'react'

import MappingImg from './MappingImg'

const MappingDisplay = ({ type, sequences, mapping, remove }) => {
    const maxwidth = Math.max(...Object.values(sequences).map(sequence => sequence.length))

    return (
        <React.Fragment>
            {mapping.map((alignment, i) => (
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
                                            onClick={e => remove(i)}
                                        >
                                            <i className="fas fa-trash" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <ul className="list-group list-group-flush">
                                {alignment.isoforms.map((isoform, j) => (
                                    <li key={j} className="list-group-item">
                                        <h4>
                                            {isoform.accession}
                                        </h4>
                                        <p>
                                            <MappingImg
                                                type={type}
                                                start={1}
                                                stop={sequences[isoform.accession].length}
                                                width={maxwidth}
                                            />
                                        </p>
                                        <ul className="list-unstyled">
                                            {isoform.occurrences.map((occurrence, k) => (
                                                <li key={k}>
                                                    <MappingImg
                                                        type={type}
                                                        start={occurrence.start}
                                                        stop={occurrence.stop}
                                                        width={maxwidth}
                                                    />
                                                </li>
                                            ))}
                                        </ul>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default MappingDisplay;

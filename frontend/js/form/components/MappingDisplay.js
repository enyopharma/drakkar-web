import React from 'react'

import MappingImg from './MappingImg'

const MappingDisplay = ({ type, name, coordinates, mapping, remove }) => {
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
                                            <span className="fas fa-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <ul className="list-group list-group-flush mt-0">
                                {alignment.isoforms.map((isoform, j) => (
                                    <li key={j} className="list-group-item">
                                        <h4>
                                            {coordinates[isoform.accession].start == 1
                                                ? isoform.accession
                                                : [isoform.accession, '/', name].join('')} (
                                                {coordinates[isoform.accession].start},&nbsp;
                                                {coordinates[isoform.accession].stop}
                                            )
                                        </h4>
                                        <ul className="list-unstyled">
                                            {isoform.occurrences.sort((a, b) => a.start - b.start).map((occurrence, k) => (
                                                <li key={k}>
                                                    <MappingImg
                                                        type={type}
                                                        start={occurrence.start}
                                                        stop={occurrence.stop}
                                                        width={coordinates[isoform.accession].width}
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
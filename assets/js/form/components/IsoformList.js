import React from 'react'

import MappingImg from './MappingImg'
import OccurenceList from './OccurenceList'

const IsoformList = ({ type, subjects, isoforms, remove }) => {
    const max = Math.max(...Object.values(subjects).map(sequence => sequence.length))

    return isoforms.length == 0 ? (
        <div className="card-body">
            No isoform contains this sequence.
        </div>
    ) : (
        <ul className="list-group list-group-flush">
            {isoforms.map((isoform, j) => (
                <li key={j} className="list-group-item">
                    <h5>
                        {isoform.accession}
                    </h5>
                    <div className="row">
                        <div className="col">
                            <MappingImg
                                type={type}
                                start={1}
                                stop={subjects[isoform.accession].length}
                                length={max}
                            />
                        </div>
                        <div className="col-1">
                            <button
                                className="btn btn-block btn-warning"
                                onClick={() => remove(j)}
                            >
                                <i className="fas fa-trash" />
                            </button>
                        </div>
                    </div>
                    <OccurenceList
                        type={type}
                        length={max}
                        occurences={isoform.occurences}
                        remove={k => remove(j, k)}
                    />
                </li>
            ))}
        </ul>
    )
}

export default IsoformList

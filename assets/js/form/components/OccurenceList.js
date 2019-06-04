import React from 'react'

import MappingImg from './MappingImg'

const OccurenceList = ({ type, length, occurences, remove }) => {
    return occurences.length == 0 ? (
        <p>
            No occurence of the sequence on this isoform.
        </p>
    ) : (
        <ul className="list-unstyled">
            {occurences.map((occurence, k) => (
                <li key={k}>
                    <div className="row">
                        <div className="col">
                            <MappingImg
                                type={type}
                                start={occurence.start}
                                stop={occurence.stop}
                                length={length}
                            />
                        </div>
                        <div className="col-1">
                            <button
                                className="btn btn-block btn-sm btn-warning"
                                onClick={() => remove(k)}
                            >
                                <i className="fas fa-trash" />
                            </button>
                        </div>
                    </div>
                </li>
            ))}
        </ul>
    )
}

export default OccurenceList

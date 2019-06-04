import React from 'react'

import Occurence from './Occurence'
import MappingImg from './MappingImg'

const Isoform = ({ type, length, isoform, remove }) => {
    return (
        <div className="row">
            <div className="col">
                <h4>
                    {isoform.accession}
                </h4>
                <div className="row">
                    <div className="col">
                        <MappingImg
                            type={type}
                            start={1}
                            stop={isoform.sequence.length}
                            length={length}
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
                <ul className="list-unstyled">
                    {isoform.occurences.map((occurence, k) => (
                        <li key={k}>
                            <Occurence
                                type={type}
                                length={length}
                                occurence={occurence}
                                remove={k => remove(k)}
                            />
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    )
}

export default Isoform

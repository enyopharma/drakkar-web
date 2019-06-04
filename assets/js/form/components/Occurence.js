import React from 'react'

import MappingImg from './MappingImg'

const Occurence = ({ type, length, occurence, remove }) => {
    return (
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
                    onClick={(e) => remove()}
                >
                    <i className="fas fa-trash" />
                </button>
            </div>
        </div>
    )
}

export default Occurence

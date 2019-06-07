import React from 'react'

import MappingImg from './MappingImg'

const SequenceToggle = ({ source, protein, editable, edit }) => {
    return (
        <div className="row">
            <div className="col">
                <MappingImg
                    type={protein.type}
                    start={protein.start}
                    stop={protein.stop}
                    width={source.sequence.length}
                />
            </div>
            <div className="col-1">
                <button
                    className="btn btn-block btn-warning"
                    onClick={edit}
                    disabled={! editable}
                >
                    <i className="fas fa-edit" />
                </button>
            </div>
        </div>
    )
}

export default SequenceToggle

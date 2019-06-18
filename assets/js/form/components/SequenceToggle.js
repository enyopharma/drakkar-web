import React from 'react'

import MappingImg from './MappingImg'

const SequenceToggle = ({ type, start, stop, width, editable, edit }) => {
    return (
        <div className="row">
            <div className="col">
                <MappingImg type={type} start={start} stop={stop} width={width} />
            </div>
            <div className="col-1">
                <button
                    className="btn btn-block btn-warning"
                    onClick={e => edit()}
                    disabled={! editable}
                >
                    <i className="fas fa-edit" />
                </button>
            </div>
        </div>
    )
}

export default SequenceToggle

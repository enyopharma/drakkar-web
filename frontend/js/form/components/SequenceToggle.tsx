import React from 'react'

import { ProteinType, Mature } from '../types'

import { MappingImg } from './MappingImg'

type Props = {
    type: ProteinType,
    current: Mature,
    width: number,
    editable: boolean,
    edit: () => void,
}

export const SequenceToggle: React.FC<Props> = ({ type, current, width, editable, edit }) => {
    return (
        <div className="row">
            <div className="col">
                <MappingImg type={type} start={current.start} stop={current.stop} width={width} />
            </div>
            <div className="col-1">
                <button
                    className="btn btn-block btn-warning"
                    onClick={e => edit()}
                    disabled={!editable}
                >
                    <span className="fas fa-edit"></span>
                </button>
            </div>
        </div>
    )
}

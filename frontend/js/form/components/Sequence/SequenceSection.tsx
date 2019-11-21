import React from 'react'

import { Protein, Mature } from '../../types'

import { SequenceEditor } from './SequenceEditor'
import { SequenceTextarea } from './SequenceTextarea'
import { SequenceFormGroup } from './SequenceFormGroup'
import { SequenceImg } from '../Shared/SequenceImg'

type Props = {
    protein: Protein,
    name: string,
    start: number,
    stop: number,
    editing: boolean,
    processing: boolean,
    edit: () => void,
    update: (mature: Mature) => void,
}

export const SequenceSection: React.FC<Props> = ({ edit, ...props }) => {
    const type = props.protein.type
    const sequence = props.protein.sequence
    const length = props.protein.sequence.length
    const chains = props.protein.chains
    const matures = props.protein.matures
    const valid = !props.editing
    const enabled = props.protein.type == 'v' && !props.editing && !props.processing

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <SequenceFormGroup {...props} valid={valid} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceTextarea {...props} sequence={sequence} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceImg {...props} type={type} length={length} />
                </div>
                <div className="col-1">
                    <button
                        className="btn btn-block btn-warning"
                        onClick={e => edit()}
                        disabled={!enabled}
                    >
                        <span className="fas fa-edit"></span>
                    </button>
                </div>
            </div>
            {!props.editing ? null : (
                <SequenceEditor {...props} sequence={sequence} chains={chains} matures={matures} />
            )}
        </React.Fragment>
    )
}

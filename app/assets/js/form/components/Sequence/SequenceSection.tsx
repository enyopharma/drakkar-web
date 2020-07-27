import React from 'react'
import { FaEdit } from 'react-icons/fa'
import { useAction } from '../../src/hooks'

import { InteractorI, Protein } from '../../src/types'
import { editMature } from '../../src/reducer'

import { SequenceEditor } from './SequenceEditor'
import { SequenceTextarea } from './SequenceTextarea'
import { SequenceFormGroup } from './SequenceFormGroup'
import { SequenceImg } from '../Shared/SequenceImg'

type Props = {
    i: InteractorI,
    protein: Protein,
    name: string,
    start: number | null,
    stop: number | null,
    editing: boolean,
    processing: boolean,
}

export const SequenceSection: React.FC<Props> = ({ i, ...props }) => {
    const edit = useAction(editMature)

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
                        onClick={e => edit({ i })}
                        disabled={!enabled}
                    >
                        <FaEdit />
                    </button>
                </div>
            </div>
            {!props.editing ? null : (
                <SequenceEditor i={i} sequence={sequence} chains={chains} matures={matures} {...props} />
            )}
        </React.Fragment>
    )
}

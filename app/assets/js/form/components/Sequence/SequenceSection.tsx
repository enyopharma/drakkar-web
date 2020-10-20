import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faEdit } from '@fortawesome/free-solid-svg-icons/faEdit'

import { useAction } from '../../src/hooks'
import { editMature } from '../../src/reducer'
import { InteractorI, Protein } from '../../src/types'

import { SequenceEditor } from './SequenceEditor'
import { SequenceTextarea } from './SequenceTextarea'
import { SequenceFormGroup } from './SequenceFormGroup'
import { SequenceImg } from '../Shared/SequenceImg'

type Props = {
    i: InteractorI
    protein: Protein
    name: string
    start: number | null
    stop: number | null
    editing: boolean
    processing: boolean
}

export const SequenceSection: React.FC<Props> = ({ i, protein, editing, processing, ...props }) => {
    const edit = useAction(editMature)

    const valid = !editing
    const enabled = protein.type == 'v' && !editing && !processing

    return (
        <React.Fragment>
            <div className="row">
                <div className="col">
                    <SequenceFormGroup valid={valid} {...props} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceTextarea sequence={protein.sequence} {...props} />
                </div>
            </div>
            <div className="row">
                <div className="col">
                    <SequenceImg type={protein.type} length={protein.sequence.length} {...props} />
                </div>
                <div className="col-1">
                    <button
                        className="btn btn-block btn-warning"
                        onClick={e => edit({ i })}
                        disabled={!enabled}
                    >
                        <FontAwesomeIcon icon={faEdit} />
                    </button>
                </div>
            </div>
            {editing && (
                <SequenceEditor
                    i={i}
                    sequence={protein.sequence}
                    chains={protein.chains}
                    matures={protein.matures}
                    {...props}
                />
            )}
        </React.Fragment>
    )
}

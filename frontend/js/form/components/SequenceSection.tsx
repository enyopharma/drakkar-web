import React from 'react'

import { ProteinType, Protein, Mature } from '../types'

import { SequenceEditor } from './SequenceEditor'
import { SequenceToggle } from './SequenceToggle'
import { SequenceDisplay } from './SequenceDisplay'

type Props = {
    type: ProteinType,
    current: Mature,
    protein: Protein,
    valid: boolean,
    editable: boolean,
    editing: boolean,
    edit: () => void,
    update: (mature: Mature) => void,
}

export const SequenceSection: React.FC<Props> = ({ protein, ...props }) => {
    return (
        <React.Fragment>
            <SequenceDisplay {...props} sequence={protein.sequence} />
            <SequenceToggle {...props} width={protein.sequence.length} />
            {!props.editing ? null : (
                <SequenceEditor {...props}
                    sequence={protein.sequence}
                    chains={protein.chains}
                    matures={protein.matures}
                />
            )}
        </React.Fragment>
    )
}

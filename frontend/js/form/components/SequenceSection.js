import React from 'react'

import SequenceEditor from './SequenceEditor'
import SequenceToggle from './SequenceToggle'
import SequenceDisplay from './SequenceDisplay'

const SequenceSection = ({ protein, ...props }) => {
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

export default SequenceSection;

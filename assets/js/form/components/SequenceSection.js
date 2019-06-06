import React from 'react'

import SequenceEditor from './SequenceEditor'
import SequenceToggle from './SequenceToggle'
import SequenceDisplay from './SequenceDisplay'

const SequenceSection = ({ editing, display, toggle, editor }) => {
    return (
        <React.Fragment>
            <SequenceDisplay {...display} />
            <SequenceToggle {...toggle} />
            {! editing ? null : (
                <SequenceEditor {...editor} />
            )}
        </React.Fragment>
    )
}

export default SequenceSection;

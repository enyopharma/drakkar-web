import React from 'react'

import MappingModal from './MappingModal'
import MappingEditor from './MappingEditor'
import MappingDisplay from './MappingDisplay'

const MappingSection = ({ selecting, display, editor, modal }) => {
    return (
        <React.Fragment>
            <MappingEditor {...editor} />
            <MappingDisplay {...display} />
            {! selecting ? null : (
                <MappingModal {...modal} />
            )}
        </React.Fragment>
    )
}

export default MappingSection;

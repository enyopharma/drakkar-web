import React, { useState } from 'react'

import MappingSection from './MappingSection'
import SequenceSection from './SequenceSection'

const InteractorEditor = ({ type, interactor, processing, setProcessing, actions }) => {
    const [editing, setEditing] = useState(type == 'v')

    return (
        <React.Fragment>
            <SequenceSection
                type={type}
                interactor={interactor}
                editing={editing}
                processing={processing}
                setEditing={setEditing}
                actions={actions}
            />
            <MappingSection
                type={type}
                interactor={interactor}
                editing={editing}
                processing={processing}
                setProcessing={setProcessing}
                actions={actions}
            />
        </React.Fragment>
    )
}

export default InteractorEditor

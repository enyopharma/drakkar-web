import React, { useState } from 'react'

import MappingEditor from './MappingEditor'
import AlignmentList from './AlignmentList'

const MappingSection = ({ type, interactor, editing, processing, setProcessing, actions }) => {
    return (
        <React.Fragment>
            <h4>Mapping</h4>
            {editing ? (
                <p>
                    Please select a sequence first.
                </p>
            ) : (
                <React.Fragment>
                    <MappingEditor
                        type={type}
                        interactor={interactor}
                        processing={processing}
                        setProcessing={setProcessing}
                        add={actions.addAlignment}
                    />
                    <AlignmentList
                        type={type}
                        interactor={interactor}
                        remove={actions.removeAlignment}
                    />
                </React.Fragment>
            )}
        </React.Fragment>
    )
}

export default MappingSection;

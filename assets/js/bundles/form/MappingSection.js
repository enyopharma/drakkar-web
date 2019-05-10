import React, { useState } from 'react'

import MappingEditor from './MappingEditor'

const MappingSection = ({ type, interactor, editing, processing, setProcessing }) => {
    return (
        <React.Fragment>
            <h4>Mapping</h4>
            {editing ? (
                <p>
                    Please select a sequence first.
                </p>
            ) : (
                <MappingEditor
                    type={type}
                    interactor={interactor}
                    processing={processing}
                    setProcessing={setProcessing}
                />
            )}
        </React.Fragment>
    )
}

export default MappingSection;

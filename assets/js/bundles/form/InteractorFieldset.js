import React, { useState } from 'react'

import UniprotSection from './UniprotSection'
import MappingSection from './MappingSection'
import SequenceSection from './SequenceSection'

const color = type => type == 'h' ? 'primary' : 'danger'

const InteractorFieldset = ({ i, type, interactor, actions }) => {
    const [processing, setProcessing] = useState(false)

    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-' + color(type)}></i>
                &nbsp;
                Interactor {i}
            </legend>
            <UniprotSection type={type} protein={interactor.protein} actions={actions} />
            {interactor.protein == null ? null : (
                <React.Fragment>
                    <SequenceSection
                        type={type}
                        interactor={interactor}
                        processing={processing}
                        update={actions.updateMature}
                    />
                    <MappingSection
                        type={type}
                        interactor={interactor}
                        processing={processing}
                        setProcessing={setProcessing}
                    />
                </React.Fragment>
            )}
        </fieldset>
    )
}

export default InteractorFieldset

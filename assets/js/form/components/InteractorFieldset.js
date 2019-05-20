import React, { useState } from 'react'

import UniprotSection from './UniprotSection'
import InteractorEditor from './InteractorEditor'

const InteractorFieldset = ({ i, type, interactor, actions }) => {
    const [processing, setProcessing] = useState(false)

    return (
        <fieldset>
            <legend>
                <i className={'fas fa-circle small text-' + (type == 'h' ? 'primary' : 'danger')} />
                &nbsp;
                Interactor {i}
            </legend>
            <UniprotSection
                type={type}
                protein={interactor.protein}
                processing={processing}
                actions={actions}
            />
            {interactor.protein == null ? null : (
                <InteractorEditor
                    type={type}
                    interactor={interactor}
                    processing={processing}
                    setProcessing={setProcessing}
                    actions={actions}
                />
            )}
        </fieldset>
    )
}

export default InteractorFieldset

import React from 'react'

import Conditional from './Conditional'
import UniprotSection from './UniprotSection'
import SequenceSection from './SequenceSection'

const color = type => type == 'h' ? 'primary' : 'danger'

const InteractorFieldset = ({ i, type, interactor, actions }) => (
    <fieldset>
        <legend>
            <i className={'fas fa-circle small text-' + color(type)}></i>
            &nbsp;
            Interactor {i}
        </legend>
        <UniprotSection type={type} protein={interactor.protein} actions={actions} />
        <Conditional state={interactor.protein != null}>
            <SequenceSection
                type={type}
                interactor={interactor}
                update={actions.updateMature}
            />
        </Conditional>
    </fieldset>
)

export default InteractorFieldset

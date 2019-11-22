import React from 'react'

import { ProteinType } from '../src/types'
import { InteractorProps } from '../src/props'

import { ProteinAlert } from './ProteinAlert'
import { MappingSection } from './Mapping/MappingSection'
import { SequenceSection } from './Sequence/SequenceSection'
import { ProteinSearchField } from './ProteinSearchField'

const classes: Record<ProteinType, string> = {
    'h': 'fas fa-circle small text-primary',
    'v': 'fas fa-circle small text-danger',
}

export const InteractorFieldset: React.FC<InteractorProps> = ({ actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className={classes[props.type]}></span> Interactor {props.i}
            </legend>
            <h3>Uniprot</h3>
            {props.protein == null
                ? <ProteinSearchField {...props} {...actions.protein} />
                : <ProteinAlert {...props} {...actions.protein} enabled={!props.processing} />
            }
            <h3>Sequence</h3>
            {props.protein == null
                ? <p>Please select an uniprot entry first.</p>
                : <SequenceSection {...props} {...actions.sequence} />
            }
            <h3>Mapping</h3>
            {props.protein == null || props.editing
                ? <p>Please select a sequence first.</p>
                : <MappingSection {...props} {...actions.mapping} />
            }
        </fieldset>
    )
}

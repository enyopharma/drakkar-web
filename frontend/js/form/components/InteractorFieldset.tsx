import React from 'react'
import { FaCircle } from 'react-icons/fa'

import { ProteinType } from '../src/types'
import { InteractorProps } from '../src/props'

import { ProteinAlert } from './ProteinAlert'
import { MappingSection } from './Mapping/MappingSection'
import { SequenceSection } from './Sequence/SequenceSection'
import { ProteinSearchField } from './ProteinSearchField'

const classes: Record<ProteinType, string> = {
    'h': 'small text-primary',
    'v': 'small text-danger',
}

export const InteractorFieldset: React.FC<InteractorProps> = ({ protein, actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className={classes[props.type]}><FaCircle /></span> Interactor {props.i}
            </legend>
            <h3>Uniprot</h3>
            <div className="row">
                <div className="col">
                    {protein == null
                        ? <ProteinSearchField {...props} {...actions.protein} />
                        : <ProteinAlert {...props} {...actions.protein} protein={protein} enabled={!props.processing} />
                    }
                </div>
            </div>
            <h3>Sequence</h3>
            {protein == null
                ? <p>Please select an uniprot entry first.</p>
                : <SequenceSection {...props} {...actions.sequence} protein={protein} />
            }
            <h3>Mapping</h3>
            {protein == null || props.start == null || props.stop == null
                ? <p>Please select a sequence first.</p>
                : <MappingSection {...props} {...actions.mapping} protein={protein} start={props.start} stop={props.stop} />
            }
        </fieldset>
    )
}

import React from 'react'

import { proteins as api } from '../src/api'
import { InteractorProps } from '../src/props'

import { ProteinAlert } from './ProteinAlert'
import { SearchField } from './Shared/SearchField'
import { MappingSection } from './Mapping/MappingSection'
import { SequenceSection } from './Sequence/SequenceSection'

export const InteractorFieldset: React.FC<InteractorProps> = ({ actions, ...props }) => {
    return (
        <fieldset>
            <legend>
                <span className={'fas fa-circle small text-' + (props.type == 'h' ? 'primary' : 'danger')}></span>
                &nbsp;
                Interactor {props.i}
            </legend>
            <h3>Uniprot</h3>
            {props.protein == null
                ? <SearchField {...props} {...actions.protein} search={api.search(props.type)} placeholder="Search an uniprot entry..." />
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

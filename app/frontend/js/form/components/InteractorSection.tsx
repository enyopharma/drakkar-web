import React from 'react'

import { InteractorProps } from '../src/props'

import { ProteinFieldset } from './Protein/ProteinFieldset'
import { MappingFieldset } from './Mapping/MappingFieldset'
import { SequenceFieldset } from './Sequence/SequenceFieldset'

export const InteractorSection: React.FC<InteractorProps> = ({ actions, ...props }) => {
    return (
        <React.Fragment>
            <ProteinFieldset {...props} {...actions.protein} enabled={!props.processing} />
            <SequenceFieldset {...props} {...actions.sequence} />
            <MappingFieldset {...props} {...actions.mapping} />
        </React.Fragment>
    )
}

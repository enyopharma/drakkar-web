import React from 'react'

import { InteractorProps } from '../src/props'

import { ProteinFieldset } from './Protein/ProteinFieldset'
import { SequenceFieldset } from './Sequence/SequenceFieldset'
import { MappingFieldset } from './Mapping/MappingFieldset'

export const InteractorSection: React.FC<InteractorProps> = ({ ...props }) => (
    <React.Fragment>
        <ProteinFieldset {...props} />
        <SequenceFieldset {...props} />
        <MappingFieldset {...props} />
    </React.Fragment>
)

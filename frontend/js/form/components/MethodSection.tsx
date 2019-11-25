import React from 'react'

import { MethodProps } from '../src/props'

import { MethodFieldset } from './Method/MethodFieldset'

export const MethodSection: React.FC<MethodProps> = ({ ...props }) => {
    return <MethodFieldset {...props} {...props.actions} />
}

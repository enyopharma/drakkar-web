import React, { useEffect } from 'react'

import { AppProps } from '../src/props'

import { Form } from './Form'

export const FormInit: React.FC<AppProps> = ({ ...props }) => {
    useEffect(() => { props.actions.init() }, [])

    return props.init ? <Form {...props} /> : null
}

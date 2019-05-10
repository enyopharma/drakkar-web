import React from 'react'

import MethodFieldset from './MethodFieldset'
import InteractorFieldset from './InteractorFieldset'

const Form = ({ type, method, interactor1, interactor2, actions }) => (
    <form onSubmit={e => e.preventDefault()}>
        <MethodFieldset
            method={method}
            actions={actions.method}
        />
        <InteractorFieldset
            i={1}
            type="h"
            interactor={interactor1}
            actions={actions.interactor1}
        />
        <InteractorFieldset
            i={2}
            type={type == 'hh' ? 'h' : 'v'}
            interactor={interactor2}
            actions={actions.interactor2}
        />
    </form>
)

export default Form

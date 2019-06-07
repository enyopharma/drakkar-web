import React from 'react'

import MethodFieldset from './MethodFieldset'
import ActionsFieldset from './ActionsFieldset'
import InteractorFieldset from './InteractorFieldset'

const Form = ({ method, interactor1, interactor2, actions }) => {
    return (
        <form onSubmit={e => e.preventDefault()}>
            <MethodFieldset {...method} />
            <InteractorFieldset {...interactor1} />
            <InteractorFieldset {...interactor2} />
            <ActionsFieldset {...actions} />
        </form>
    )
}

export default Form

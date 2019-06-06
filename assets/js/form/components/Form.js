import React from 'react'

import MethodFieldset from './MethodFieldset'
import InteractorFieldset from './InteractorFieldset'

const Form = ({ method, interactor1, interactor2 }) => {
    return (
        <form onSubmit={e => e.preventDefault()}>
            <MethodFieldset {...method} />
            <InteractorFieldset {...interactor1} />
            <InteractorFieldset {...interactor2} />
        </form>
    )
}

export default Form

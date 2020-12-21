import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCheck } from '@fortawesome/free-solid-svg-icons/faCheck'
import { faExclamationTriangle } from '@fortawesome/free-solid-svg-icons/faExclamationTriangle'

import { InteractorI } from '../../src/types'
import { useInteractorSelector } from '../../src/hooks'

type SequenceFormGroupProps = {
    i: InteractorI
}

export const SequenceFormGroup: React.FC<SequenceFormGroupProps> = ({ i }) => (
    <div className="row">
        <div className="col">
            <NameInput i={i} />
        </div>
        <div className="col">
            <StartInput i={i} />
        </div>
        <div className="col">
            <StopInput i={i} />
        </div>
        <div className="col">
            <InfoButtonProps i={i} />
        </div>
    </div >
)

type NameInputProps = {
    i: InteractorI
}

const NameInput: React.FC<NameInputProps> = ({ i }) => {
    const name = useInteractorSelector(i, state => state.name)

    return <input type="text" className="form-control" placeholder="Name" value={name} readOnly />
}

type StartInputProps = {
    i: InteractorI
}

const StartInput: React.FC<StartInputProps> = ({ i }) => {
    const start = useInteractorSelector(i, state => state.start)

    return <input type="text" className="form-control" placeholder="Start" value={start ?? ''} readOnly />
}

type StopInputProps = {
    i: InteractorI
}

const StopInput: React.FC<StopInputProps> = ({ i }) => {
    const stop = useInteractorSelector(i, state => state.stop)

    return <input type="text" className="form-control" placeholder="Stop" value={stop ?? ''} readOnly />
}

type InfoButtonProps = {
    i: InteractorI
}

const InfoButtonProps: React.FC<InfoButtonProps> = ({ i }) => {
    const editing = useInteractorSelector(i, state => state.editing)

    return editing ? <InvalidButton /> : <ValidButton />
}

const ValidButton: React.FC = () => (
    <button className="btn btn-block btn-outline-success" disabled>
        <FontAwesomeIcon icon={faCheck} /> Sequence is valid
    </button>
)

const InvalidButton: React.FC = () => (
    <button className="btn btn-block btn-outline-danger" disabled>
        <FontAwesomeIcon icon={faExclamationTriangle} /> Please select a sequence.
    </button>
)

import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faCheck } from '@fortawesome/free-solid-svg-icons/faCheck'
import { faExclamationTriangle } from '@fortawesome/free-solid-svg-icons/faExclamationTriangle'

type Props = {
    name: string
    start: number | null
    stop: number | null
    valid: boolean
}

export const SequenceFormGroup: React.FC<Props> = ({ name, start, stop, valid }) => {
    return (
        <div className="row">
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Name"
                    value={name}
                    readOnly
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Start"
                    value={start ?? ''}
                    readOnly
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Stop"
                    value={stop ?? ''}
                    readOnly
                />
            </div>
            <div className="col">
                {valid ? <ValidButton /> : <InvalidButton />}
            </div>
        </div >
    )
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

import React from 'react'

type Props = {
    name: string,
    start: number,
    stop: number,
    valid: boolean,
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
                    value={start == null ? '' : start}
                    readOnly
                />
            </div>
            <div className="col">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Stop"
                    value={stop == null ? '' : stop}
                    readOnly
                />
            </div>
            <div className="col">
                {valid
                    ? (
                        <button className="btn btn-block btn-outline-success" disabled>
                            <span className="fas fa-check"></span>&nbsp;Sequence is valid
                        </button>
                    ) : (
                        <button className="btn btn-block btn-outline-danger" disabled>
                            <span className="fas fa-exclamation-triangle"></span>
                            &nbsp;
                            Please select a sequence.
                        </button>
                    )
                }
            </div>
        </div>
    )
}

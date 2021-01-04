import React from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faTrash } from '@fortawesome/free-solid-svg-icons/faTrash'

import { removeAlignment } from '../../src/reducer'
import { InteractorI, Sequences } from '../../src/types'
import { useAction, useInteractorSelector } from '../../src/hooks'

import { SequenceImg } from '../Shared/SequenceImg'

type Props = {
    i: InteractorI
    sequences: Sequences
}

export const MappingDisplay: React.FC<Props> = ({ i, sequences }) => {
    const mapping = useInteractorSelector(i, state => state.mapping)

    return (
        <React.Fragment>
            {mapping.map((alignment, x) => (
                <div key={x} className="row">
                    <div className="col">
                        <div className="card">
                            <div className="card-header">
                                <div className="row">
                                    <div className="col">
                                        <input type="text" className="form-control" value={alignment.sequence} readOnly />
                                    </div>
                                    <div className="col-1">
                                        <RemoveButton i={i} x={x}>
                                            <FontAwesomeIcon icon={faTrash} />
                                        </RemoveButton>
                                    </div>
                                </div>
                            </div>
                            <ul className="list-group list-group-flush mt-0">
                                {alignment.isoforms.map((isoform, y) => (
                                    <li key={y} className="list-group-item">
                                        <h4><IsoformName i={i} accession={isoform.accession} /></h4>
                                        <ul className="list-unstyled">
                                            {isoform.occurrences.sort((a, b) => a.start - b.start).map((occurrence, z) => (
                                                <li key={z}>
                                                    <MappingSequenceImg
                                                        i={i}
                                                        start={occurrence.start}
                                                        stop={occurrence.stop}
                                                        length={sequences[isoform.accession].length}
                                                    />
                                                </li>
                                            ))}
                                        </ul>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </div>
            ))
            }
        </React.Fragment >
    )
}

type IsoformNameProps = {
    i: InteractorI
    accession: string
}

const IsoformName: React.FC<IsoformNameProps> = ({ i, accession }) => {
    const name = useInteractorSelector(i, state => state.name)

    return <React.Fragment>{name}/{accession}</React.Fragment>
}

type MappingSequenceImgProps = {
    i: InteractorI
    start: number
    stop: number
    length: number
}

const MappingSequenceImg: React.FC<MappingSequenceImgProps> = ({ i, start, stop, length }) => {
    const type = useInteractorSelector(i, state => state.type)

    return <SequenceImg type={type} start={start} stop={stop} length={length} />
}

type RemoveButtonProps = {
    i: InteractorI
    x: number
}

const RemoveButton: React.FC<RemoveButtonProps> = ({ i, x, children }) => {
    const remove = useAction(removeAlignment)

    return (
        <button className="btn btn-block btn-warning" onClick={() => remove({ i, index: x })}>
            {children}
        </button>
    )
}

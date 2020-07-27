import React from 'react'
import { FaTrash } from 'react-icons/fa'
import { useAction } from '../../src/hooks'

import { InteractorI, ProteinType, Coordinates, Alignment } from '../../src/types'
import { removeAlignment } from '../../src/reducer'

import { SequenceImg } from '../Shared/SequenceImg'

type Props = {
    i: InteractorI,
    type: ProteinType,
    name: string,
    coordinates: Coordinates,
    mapping: Alignment[],
}

export const MappingDisplay: React.FC<Props> = ({ i, type, name, coordinates, mapping }) => {
    const remove = useAction(removeAlignment)

    return (
        <React.Fragment>
            {mapping.map((alignment, index) => (
                <div key={index} className="row">
                    <div className="col">
                        <div className="card">
                            <div className="card-header">
                                <div className="row">
                                    <div className="col">
                                        <input
                                            type="text"
                                            className="form-control"
                                            value={alignment.sequence}
                                            readOnly
                                        />
                                    </div>
                                    <div className="col-1">
                                        <button
                                            className="btn btn-block btn-warning"
                                            onClick={e => remove({ i, index })}
                                        >
                                            <FaTrash />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <ul className="list-group list-group-flush mt-0">
                                {alignment.isoforms.map((isoform, j) => (
                                    <li key={j} className="list-group-item">
                                        <h4>
                                            {coordinates[isoform.accession].start == 1
                                                ? isoform.accession
                                                : [isoform.accession, '/', name].join('')} (
                                                {coordinates[isoform.accession].start},&nbsp;
                                                {coordinates[isoform.accession].stop}
                                            )
                                        </h4>
                                        <ul className="list-unstyled">
                                            {isoform.occurrences.sort((a, b) => a.start - b.start).map((occurrence, k) => (
                                                <li key={k}>
                                                    <SequenceImg
                                                        type={type}
                                                        start={occurrence.start}
                                                        stop={occurrence.stop}
                                                        length={coordinates[isoform.accession].length}
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

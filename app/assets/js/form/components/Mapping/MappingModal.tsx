import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal'
import { useAction } from '../../src/hooks'

import { ProteinType, InteractorI, Coordinates, Alignment } from '../../src/types'
import { addAlignment, cancelAlignment } from '../../src/reducer';

import { SequenceImg } from '../Shared/SequenceImg';

type Index = [number, number]

type Props = {
    i: InteractorI,
    type: ProteinType,
    name: string,
    coordinates: Coordinates,
    alignment: Alignment,
}

const indexes = (alignment: Alignment): Index[] => {
    const indexes: Index[] = []

    alignment.isoforms.map((r, i) => {
        r.occurrences.map((o, j) => indexes.push([i, j]))
    })

    return indexes
}

const filter = (alignment: Alignment, selected: Index[]): Alignment => {
    return Object.assign({}, alignment, {
        isoforms: alignment.isoforms.map((isoform, i) => {
            return Object.assign({}, isoform, {
                occurrences: isoform.occurrences.filter((o, j) => {
                    return selected.filter(s => s[0] == i && s[1] == j).length == 1
                })
            })
        }).filter(isoform => isoform.occurrences.length > 0)
    })
}

export const MappingModal: React.FC<Props> = ({ i, type, name, coordinates, alignment }) => {
    const add = useAction(addAlignment)
    const cancel = useAction(cancelAlignment)
    const [selected, setSelected] = useState<Index[]>(indexes(alignment))

    const filtered = filter(alignment, selected)

    const isValid = filtered.isoforms.length > 0

    const isOccurrenceSelected = (i: number, j: number): boolean => {
        return selected.filter(s => s[0] == i && s[1] == j).length > 0
    }

    const select = (i: number, j: number) => {
        setSelected(selected.concat([[i, j]]))
    }

    const unselect = (i: number, j: number) => {
        setSelected(selected.filter(s => s[0] != i || s[1] != j))
    }

    const toggle = (checked: boolean, i: number, j: number) => {
        checked ? select(i, j) : unselect(i, j)
    }

    return (
        <Modal visible={true} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping result on interactor {i}
                </h5>
                <button type="button" className="close" onClick={e => cancel({ i })}>
                    &times;
                </button>
            </div>
            <div className="modal-body">
                <p>
                    <label>Mapped sequence</label>
                    <input
                        type="text"
                        className="form-control"
                        value={alignment.sequence}
                        readOnly
                    />
                </p>
                <ul className="list-unstyled">
                    {alignment.isoforms.map((isoform, i) => (
                        <li key={i}>
                            <h4>
                                {coordinates[isoform.accession].start == 1
                                    ? isoform.accession
                                    : [isoform.accession, '/', name].join('')} (
                                    {coordinates[isoform.accession].start},&nbsp;
                                    {coordinates[isoform.accession].stop}
                                )
                            </h4>
                            {isoform.occurrences.length == 0
                                ? <p>No alignment of the sequence on this isoform.</p>
                                : (
                                    <ul className="list-unstyled">
                                        {isoform.occurrences.sort((a, b) => a.start - b.start).map((occurrence, j) => (
                                            <li key={j}>
                                                <div className="row">
                                                    <div className="col-11">
                                                        <SequenceImg
                                                            type={type}
                                                            start={occurrence.start}
                                                            stop={occurrence.stop}
                                                            length={coordinates[isoform.accession].length}
                                                            active={isOccurrenceSelected(i, j)}
                                                        />
                                                    </div>
                                                    <div className="col">
                                                        <input
                                                            type="checkbox"
                                                            checked={isOccurrenceSelected(i, j)}
                                                            onChange={e => toggle(e.target.checked, i, j)}
                                                        />
                                                    </div>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                        </li>
                    ))}
                </ul>
            </div>
            <div className="modal-footer">
                <button
                    type="button"
                    className="btn btn-block btn-primary"
                    disabled={!isValid}
                    onClick={e => add({ i, alignment: filtered })}
                >
                    Save selected
                </button>
            </div>
        </Modal>
    )
}

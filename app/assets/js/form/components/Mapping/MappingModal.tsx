import React, { useState, useCallback } from 'react'
import Modal from 'react-bootstrap4-modal'

import { useAction, useInteractorSelector } from '../../src/hooks'
import { addAlignment, cancelAlignment } from '../../src/reducer';
import { InteractorI, Coordinates, Alignment } from '../../src/types'

import { SequenceImg } from '../Shared/SequenceImg';

type Index = [number, number]

type MappingModalProps = {
    i: InteractorI
    coordinates: Coordinates
    alignment: Alignment
}

export const MappingModal: React.FC<MappingModalProps> = ({ i, coordinates, alignment }) => {
    const [selection, setSelection] = useState<Index[]>(indexes(alignment))

    return (
        <Modal visible={true} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping result on interactor {i}
                </h5>
                <CancelButton i={i}>
                    &times;
                </CancelButton>
            </div>
            <div className="modal-body">
                <p>
                    <label>Mapped sequence</label>
                    <input type="text" className="form-control" value={alignment.sequence} readOnly />
                </p>
                <ul className="list-unstyled">
                    {alignment.isoforms.map((isoform, x) => (
                        <IsoformLi
                            key={x}
                            i={i}
                            x={x}
                            accession={isoform.accession}
                            occurrences={isoform.occurrences}
                            length={coordinates[isoform.accession].length}
                            selection={selection}
                            update={setSelection}
                        />
                    ))}
                </ul>
            </div>
            <div className="modal-footer">
                <SaveButton i={i} selection={selection} alignment={alignment}>
                    Save selection
                </SaveButton>
            </div>
        </Modal>
    )
}

type IsoformLiProps = {
    i: InteractorI
    x: number
    accession: string
    occurrences: { start: number, stop: number }[]
    length: number
    selection: Index[]
    update: (selection: Index[]) => void
}

const IsoformLi: React.FC<IsoformLiProps> = ({ i, x, accession, occurrences, selection, length, update }) => {
    const name = useInteractorSelector(i, state => state.name)

    return (
        <li>
            <h4>{name}/{accession}</h4>
            {occurrences.length == 0
                ? <p>No alignment of the sequence on this isoform.</p>
                : (
                    <ul className="list-unstyled">
                        {occurrences.sort((a, b) => a.start - b.start).map((occurrence, y) => (
                            <OccurrenceLi
                                key={y}
                                i={i}
                                x={x}
                                y={y}
                                start={occurrence.start}
                                stop={occurrence.stop}
                                length={length}
                                selection={selection}
                                update={update}
                            />
                        ))}
                    </ul>
                )
            }
        </li>
    )
}

type OccurenceLiProps = {
    i: InteractorI
    x: number
    y: number
    start: number
    stop: number
    length: number
    selection: Index[]
    update: (selection: Index[]) => void
}

const OccurrenceLi: React.FC<OccurenceLiProps> = ({ i, x, y, start, stop, selection, length, update }) => {
    const type = useInteractorSelector(i, state => state.type)

    const selected = selection.filter(s => s[0] == x && s[1] == y).length > 0

    const toggle = useCallback((checked: boolean) => {
        return checked
            ? update(selection.concat([[x, y]]))
            : update(selection.filter(s => s[0] != x || s[1] != y))
    }, [x, y, selection, update])

    return (
        <li>
            <div className="row">
                <div className="col-11">
                    <SequenceImg type={type} start={start} stop={stop} length={length} active={selected} />
                </div>
                <div className="col">
                    <input type="checkbox" checked={selected} onChange={e => toggle(e.target.checked)} />
                </div>
            </div>
        </li>
    )
}

type SaveButtonProps = {
    i: InteractorI
    selection: Index[]
    alignment: Alignment
}

const SaveButton: React.FC<SaveButtonProps> = ({ i, selection, alignment, children }) => {
    const add = useAction(addAlignment)

    const filtered = filter(alignment, selection)

    return (
        <button
            type="button"
            className="btn btn-block btn-primary"
            disabled={filtered.isoforms.length === 0}
            onClick={() => add({ i, alignment: filtered })}
        >
            {children}
        </button>
    )
}

type CancelButtonProps = {
    i: InteractorI
}

const CancelButton: React.FC<CancelButtonProps> = ({ i, children }) => {
    const cancel = useAction(cancelAlignment)

    return (
        <button type="button" className="close" onClick={() => cancel({ i })}>
            {children}
        </button>
    )
}

const indexes = (alignment: Alignment): Index[] => {
    const indexes: Index[] = []

    alignment.isoforms.map((r, i) => {
        r.occurrences.map((o, j) => indexes.push([i, j]))
    })

    return indexes
}

const filter = (alignment: Alignment, selection: Index[]): Alignment => {
    return Object.assign({}, alignment, {
        isoforms: alignment.isoforms.map((isoform, i) => {
            return Object.assign({}, isoform, {
                occurrences: isoform.occurrences.filter((o, j) => {
                    return selection.filter(s => s[0] == i && s[1] == j).length == 1
                })
            })
        }).filter(isoform => isoform.occurrences.length > 0)
    })
}

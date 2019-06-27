import React, { useState } from 'react'
import Modal from 'react-bootstrap4-modal';

import MappingImg from './MappingImg';

const indexes = alignment => {
    const indexes = []

    alignment.isoforms.map((r, i) => {
        r.occurrences.map((o, j) => indexes.push([i, j]))
    })

    return indexes
}

const filter = (alignment, selected) => {
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

const MappingModal = ({ i, type, sequences, alignment, add, cancel }) => {
    const [selected, setSelected] = useState(indexes(alignment))

    const filtered = filter(alignment, selected)

    const isValid = filtered.isoforms.length > 0

    const maxwidth = Math.max(...Object.values(sequences).map(sequence => sequence.length))

    const isIsoformSelected = i => {
        return selected.filter(s => s[0] == i).length > 0
    }

    const isOccurrenceSelected = (i, j) => {
        return selected.filter(s => s[0] == i && s[1] == j).length > 0
    }

    const select = (i, j) => {
        setSelected(Array.concat(selected, [[i, j]]))
    }

    const unselect = (i, j) => {
        setSelected(selected.filter(s => s[0] != i || s[1] != j))
    }

    const toggle = (checked, i, j) => {
        checked ? select(i, j) : unselect(i, j)
    }

    return (
        <Modal visible={true} dialogClassName="modal-lg">
            <div className="modal-header">
                <h5 className="modal-title">
                    Mapping result on interactor {i}
                </h5>
                <button type="button" className="close" onClick={e => cancel()}>
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
                            <h4>{isoform.accession}</h4>
                            <div className="row">
                                <div className="col-11">
                                    <MappingImg
                                        type={type}
                                        start={1}
                                        stop={sequences[isoform.accession].length}
                                        width={maxwidth}
                                        active={isIsoformSelected(i)}
                                    />
                                </div>
                            </div>
                            {isoform.occurrences.length == 0 ? (
                                <p>
                                    No alignment of the sequence on this isoform.
                                </p>
                            ) : (
                                <ul className="list-unstyled">
                                    {isoform.occurrences.map((occurrence, j) => (
                                        <li key={j}>
                                            <div className="row">
                                                <div className="col-11">
                                                    <MappingImg
                                                        type={type}
                                                        start={occurrence.start}
                                                        stop={occurrence.stop}
                                                        width={maxwidth}
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
                    disabled={! isValid}
                    onClick={e => add(filtered)}
                >
                    Save selected
                </button>
            </div>
        </Modal>
    )
}

export default MappingModal
